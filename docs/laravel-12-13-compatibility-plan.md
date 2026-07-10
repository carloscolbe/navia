# Plan: compatibilidad Laravel 12/13 + arreglo de vulnerabilidades Dependabot

> Documento de trabajo para ejecutar en próximas sesiones. Estado: **plan aprobado en diseño, implementación no iniciada**.

## Contexto

Navia (fork renombrado de Voyager) soporta hoy Laravel 8/9 con PHP <8.1. GitHub reporta 40 alertas Dependabot (2 críticas, 19 altas). Objetivos: (1) arreglar las vulnerabilidades y (2) compatibilidad con Laravel 13. Decisiones ya tomadas por Carlos:

- **Soporte: Laravel 12 + 13** (`illuminate ^12|^13`, PHP `^8.3`).
- **Build de assets: migrar laravel-mix → Vite** (elimina las ~6 vulns npm sin fix de la cadena mix/webpack).
- **Bootstrap 3 / Vue 2 se aceptan como deuda conocida** (1 moderada + 2 bajas restantes, documentadas).
- **intervention/image v2 → v3** (5 ficheros afectados).
- **Database Manager: deshabilitar como hizo Voyager 1.8 upstream**, pero con gating limpio (menú oculto + rutas capadas con mensaje claro, no 500s).

### Hechos del entorno (WSL)

- PHP 8.3.6 y 8.4.13 instalados (`php8.4`); el `php` por defecto es 8.0.30.
- El composer moderno funciona como `php8.4 /usr/bin/composer` (el binario a secas revienta bajo PHP 8.0; `/usr/local/bin/composer-old` 2.2 es para el stack viejo).
- App anfitriona de pruebas: `~/projects/laravel-for-navia` (Laravel 9, MySQL `laravel-navia`, admin@admin.com/password, puerto 8001). Habrá que recrearla en Laravel 13.

### Hallazgos clave de la investigación (jul 2026)

- **doctrine/dbal es el mayor obstáculo**: Laravel 11+ eliminó `getDoctrineSchemaManager()` etc. Todo `src/Database/*` (~60 clases) extiende/envuelve DBAL. **Voyager 1.8 upstream (rama Laravel 11) ya hizo el trabajo y se porta desde ahí** (mapeo `TCG\Voyager\`→`Navia\`, `Voyager*`→`Navia*`, `voyager`→`navia`). Verificado fichero a fichero: 68/70 ficheros de `src/Database` son byte-idénticos a upstream tras el mapeo — Navia no divergió funcionalmente.
- Upstream 1.8 **no reescribió todo**: rehízo `SchemaManager` sobre `Schema::getTables/getColumns/getIndexes/getForeignKeys` nativos (cubre todo lo que necesita el BREAD builder) y **deshabilitó el Database Manager** (crear/alterar/borrar tablas desde la UI), dejando `DatabaseUpdater`, `Schema/{Table,Column,Index,ForeignKey}`, `Platforms/*` y ~60 clases `Types/*` como código muerto que referencia Doctrine.
- Versiones con soporte L13 confirmadas en Packagist: `orchestra/testbench ^11` (v11.1.0), `orchestra/testbench-browser-kit ^11`, `laravel/browser-kit-testing ^7.2.8`, `laravel/ui ^4.6.3`, `arrilot/laravel-widgets ^3.15` (illuminate >=11), `league/flysystem ^3`, `intervention/image` v3/v4, PHPUnit `^11.5.50|^12`.
- Laravel 13: requiere PHP ≥8.3; cero breaking changes respecto a L12 (fuente: laravel.com/docs/13.x/upgrade, laravel-news.com/laravel-13-released).
- Las rutas con strings FQCN `'Controller@method'` de `routes/navia.php` siguen funcionando en L12/13 y upstream las mantiene (son la base del override `config('navia.controllers.namespace')`) — **no se tocan**.
- Legacy detectado: `factory(User::class)` en `tests/SearchTest.php:85` (helper eliminado); `\App::` en `MenuItemPolicy.php:42` y `NaviaCompassController.php:24`; `config('app.env')` en `NaviaServiceProvider.php:85,115`.
- npm audit (50 vulns en lockfile): 5 críticas + 14 altas **todas arreglables** con `npm audit fix` (incluida tinymce, la única alta de runtime); las sin-fix (`laravel-mix`, `webpack-dev-server`, `sockjs`, `uuid`, `node-notifier`, `webpack-notifier`) son todas del toolchain mix → desaparecen con Vite.
- Las vistas cargan assets por nombre fijo vía `navia_asset()` (`master.blade.php:23,120`), sin `mix()` → Vite debe emitir **IIFE sin hash**. `resources/assets/js/*.js` usan `require()` CommonJS + globals `window.*` → convertir a imports ESM.

## Fase 1 — Dependencias y esqueleto (composer.json)

- `composer.json` del paquete: `php: ^8.3`, `illuminate/support: ^12.0|^13.0`, `intervention/image: ^3.0` (+ `intervention/image-laravel: ^1.3`), quitar `doctrine/dbal`, `league/flysystem: ^3.0`, `laravel/ui: ^4.6`, `arrilot/laravel-widgets: ^3.15`.
- require-dev: `orchestra/testbench: ^10.0|^11.0`, `orchestra/testbench-browser-kit: ^10.0|^11.0`, `laravel/browser-kit-testing: ^7.2.8`, `phpunit/phpunit: ^11.5`, quitar `phpunit/phpcov` (solo agregación de cobertura; phpunit.xml usa cobertura nativa).
- Todo composer del paquete se ejecuta con `php8.4 /usr/bin/composer`.

## Fase 2 — Port del subsistema Database sin DBAL (desde Voyager 1.8)

1. `src/Database/Schema/SchemaManager.php` — sustituir por la versión 1.8 (con mapeo de namespace); además: eliminar el fallback muerto a `getDoctrineSchemaManager()` en `listTableNames()`, arreglar o borrar `listTables()` (bug upstream: usa un array como clave), normalizar `null` a `'YES'/'NO'` en `describeTable()` (la vista `tools/bread/edit-add.blade.php:313` compara con `"NO"`), y derivar `key` de los flags `primary`/`unique` (upstream devuelve `'btr'` por un substr sobre `'btree'`).
2. `src/Database/Types/Type.php` — sustituir por la versión 1.8 (paridad upstream; solo se carga desde rutas deshabilitadas; usa `get_protected_property()` que ya existe en `src/Helpers/Reflection.php`).
3. `src/Http/Controllers/NaviaDatabaseController.php:193` — `SchemaManager::getDatabasePlatform()->getName()` → `DB::connection()->getDriverName()`.
4. `migrations/2017_11_26_015000_create_user_roles_table.php:17` — `getDoctrineColumn(...)->getType()->getName()` → `DB::connection()->getSchemaBuilder()->getColumnType('users', 'id')`.
5. `publishable/database/migrations/2017_04_11_000000_alter_post_nullable_fields_table.php` — quitar los `registerDoctrineTypeMapping` de `up()`; en `down()`, `->nullable(false)->change()`.
6. **Gating del Database Manager** (mejora sobre upstream): ocultar el ítem de menú y capar el grupo de rutas `navia.database.*` de creación/edición/borrado con un mensaje claro de "no soportado en esta versión" en lugar de 500s (browse/listar puede seguir si solo usa introspección; decidir al implementar según lo que la vista index necesite).
7. Vistas: `resources/views/bread/browse.blade.php:166` y `read.blade.php:95` — `formatLocalized()` no existe en Carbon 3 → `translatedFormat($row->details->format)`.
8. Tests a paridad upstream: `tests/DatabaseTest.php` en skip (y quitar el import muerto de `Doctrine\DBAL\Schema\SchemaException`), skip de los 3 tests de eventos de tablas en `EventTest.php`, comentar las 3 aserciones de rutas `navia.database.*` en `RouteTest.php` (ajustar al gating del punto 6).
9. Las ~60 clases muertas de `src/Database/{Types,Platforms,Schema}` y `DatabaseUpdater` se mantienen (paridad con upstream para facilitar futuros ports); nunca se autocargan en rutas activas. Riesgo conocido: los nombres de tipo nativos difieren de los de Doctrine en las listas de campos del BREAD — verificar columnas `enum` de MySQL en el smoke test (era la razón del mapping doctrine eliminado).

## Fase 3 — Modernización del resto del código PHP

- `tests/SearchTest.php:85`: `factory(User::class)->create(...)` → `User::factory()->create(...)`.
- intervention/image v2 → v3 en 5 ficheros: `src/Http/Controllers/ContentTypes/Image.php`, `ContentTypes/MultipleImage.php`, `NaviaMediaController.php`, `NaviaController.php`, y registro del provider en `NaviaServiceProvider.php` (v3: `Intervention\Image\Laravel\ServiceProvider`; API: `Image::make()` → `Image::read()`, `->orientate()` es automático, `->fit(w,h)` → `->cover(w,h)`, `->resize(w,h,constraint aspectRatio+upsize)` → `->scaleDown(w,h)`, `->encode(ext,q)` → `->encodeByExtension(ext, quality: q)`, `->crop(w,h,x,y)` se mantiene).
- Flysystem: eliminar el shim 1.x (`ListWith` plugin en `NaviaMediaController.php:56` y la rama `Util::normalizeRelativePath` en `NaviaController.php:85-90`, dejando solo `WhitespacePathNormalizer`).
- Limpiezas: `\App::environment` → `app()->environment()`; `config('app.env')` → `app()->environment('testing')`.
- PHPUnit 9 → 11.5: ajustar `phpunit.xml` al esquema 11 (ya está en formato 10.4, cambios menores), arreglar lo que falle según salga.

## Fase 4 — npm: audit fix + migración a Vite

1. `npm audit fix` primero (arregla 5 críticas + 14 altas + mayoría de moderadas, incluida tinymce que es la única de runtime).
2. Sustituir laravel-mix por Vite:
   - Quitar `laravel-mix`, `cross-env`, `vue-loader`, `vue-template-compiler`, `sass-loader`, `postcss`; añadir `vite`, `@vitejs/plugin-vue2`, `vite-plugin-static-copy` (`sass` se mantiene).
   - `vite.config.js` reproduciendo `webpack.mix.js`: `resources/assets/js/app.js` (Vue 2) → `publishable/assets/js/app.js` y `resources/assets/sass/app.scss` → `publishable/assets/css/app.css`, **sin hash y en formato IIFE**, equivalente de `processCssUrls: false`, y las copias estáticas: skins/temas/iconos/modelos de tinymce y `ace-builds/src-noconflict` a las mismas rutas de `publishable/assets/js/`.
   - Convertir `resources/assets/js/*.js` de `require()` a imports ESM manteniendo las asignaciones `window.*` (jQuery, Vue, toastr, tinymce, etc.).
   - Eliminar `webpack.mix.js`, `mix.js`, `mix-manifest.json`.
   - Scripts npm: `dev`/`watch`/`prod` → `vite build --watch` / `vite build`.
3. Reconstruir assets y diffear `publishable/assets/` contra lo actual para verificar equivalencia estructural.
4. Documentar en README/docs la deuda aceptada: bootstrap 3 (1 moderada), vue 2 + eonasdan-datetimepicker (2 bajas).

## Fase 5 — Suite de tests en verde

- `php8.4 vendor/bin/phpunit` con testbench 11 (Laravel 13). Iterar fallos (era PHPUnit 11, cambios L9→L13 según aparezcan).
- Smoke adicional con testbench 10 (Laravel 12) para validar la cota inferior del rango.

## Fase 6 — App anfitriona Laravel 13 + verificación en vivo

- Recrear `~/projects/laravel-for-navia` sobre Laravel 13 (mismo repo git: limpiar árbol, `php8.4 /usr/bin/composer create-project laravel/laravel` en temporal y volcar, reconfigurar path-repo `../navia` + `require carloscolbe/navia:@dev`, recrear la BD MySQL, `php artisan navia:install --with-dummy`).
- Smoke test real (curl con login): `/admin`, `/admin/users`, `/admin/roles`, `/admin/posts`, `/admin/media`, `/admin/settings`, el gating del database manager, subida de imagen (valida intervention v3), assets servidos (css/js/fuente), y columnas enum de MySQL en el BREAD builder.
- Commit en ambos repos + push de navia.

## Verificación final

1. `php8.4 vendor/bin/phpunit` → suite en verde (los skips del Database Manager documentados).
2. `php8.4 /usr/bin/composer audit` → sin avisos.
3. `npm audit --package-lock-only` → solo bootstrap/vue/datetimepicker (deuda aceptada).
4. Smoke test en vivo de la app anfitriona L13.
5. Push y comprobar que Dependabot baja de 40 a ~3 alertas conocidas.

## Riesgos

- Migración Vite: detalles del bundle (globals, orden de carga) — se verifica con el smoke de UI.
- Tipos nativos vs Doctrine en los desplegables del BREAD builder; columnas enum MySQL.
- PHPUnit 11 + browser-kit v7: deprecations puntuales.
- La inversión semántica de `null` en `describeTable()` (upstream devuelve booleano invertido) — se normaliza a `'YES'/'NO'` en el port (Fase 2.1).
