# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Navia is a Laravel admin-panel **package** (a fork/evolution of Voyager) providing a role-based BREAD system (Browse, Read, Edit, Add, Delete). It is a Composer library (`carloscolbe/navia`), not a standalone Laravel app — it is developed and tested via Orchestra Testbench, and installed into host apps with `php artisan navia:install`.

## Commands

```bash
composer install                 # PHP deps (also runs testbench package:discover)
vendor/bin/phpunit               # run the full test suite
vendor/bin/phpunit tests/MenuTest.php            # run one test file
vendor/bin/phpunit --filter test_method_name    # run one test by name

npm install
npm run dev        # compile assets (JS/Sass) into publishable/assets via Laravel Mix
npm run watch      # recompile on change
npm run prod       # production asset build
```

Tests bootstrap through `tests/bootstrap.php` + `tests/TestCase.php` (Orchestra Testbench BrowserKit); no Laravel app or database setup is needed beyond `composer install`. PHP must be >=7.3 <8.1.

## Architecture

- **`src/NaviaServiceProvider.php`** is the entry point: registers the `navia` singleton, facade alias, form fields, policies/gates, publishable resources, console commands, and loads routes/views/migrations. `src/Navia.php` is the core class behind the `Navia` facade — it holds the registries for models, actions, form fields, and alerts. Host apps can swap any built-in model via `Navia::useModel()`, so **always resolve models through `Navia::model('Name')`, never by concrete class**.
- **BREAD is data-driven**: `DataType` and `DataRow` models (stored in the host DB) describe each managed table. `routes/navia.php` iterates all `DataType` records at boot and registers resource routes for each, pointing to `VoyagerBaseController` unless the DataType specifies a custom controller. Route registration is wrapped in try/catch because tables may not be migrated yet.
- **Form fields** (`src/FormFields/`): one `*Handler` class per input type, each rendering a Blade view; registered in the service provider. `FormFields/After/` handlers render after the field. **Content types** (`src/Http/Controllers/ContentTypes/`) are the write-side counterpart — they transform submitted input per field type before saving.
- **Actions** (`src/Actions/`): the buttons shown per row on browse pages (view/edit/delete/restore), registered on the `Navia` singleton and extendable via `Navia::addAction()`.
- **Extension points fire events** (`src/Events/`): routing events in `routes/navia.php`, `FormFieldsRegistered`, alerts, etc.
- **`publishable/`** holds everything copied into host apps: config (`publishable/config/navia.php`), compiled assets, seeders/dummy data, and translations. Compiled assets are **committed** — `webpack.mix.js` builds from `resources/assets/` into `publishable/assets/`, so rebuild and include the output when changing JS/Sass.
- **`src/Database/`** wraps Doctrine DBAL for the in-browser database/BREAD builder UI (`VoyagerDatabaseController`).
- Multilingual support lives in `src/Translator/` + the `Translatable` trait + the `Translation` model.

## Voyager → Navia rename

The package is a rename/evolution of Voyager and the rename is complete: namespace `Navia\`, commands `navia:*`, config key `navia.`, route names `navia.*`, view/translation namespace `navia::`, icon-font classes `navia-*`, body CSS class `navia`, and all internal classes use the `Navia` prefix (`NaviaBaseController`, `NaviaAdminMiddleware`, the `NaviaUser` trait, etc.). Never reintroduce `voyager` in identifiers, keys, or user-facing strings. The only intentional remaining Voyager mentions are historical attribution in README.md, CREDITS.md, CHANGELOG.md, composer.json (description/authors), and the screenshot URL in docs/introduction.md.
