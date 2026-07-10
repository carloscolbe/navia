# Overriding files

## Overriding BREAD Views

You can override any of the BREAD views for a **single** BREAD by creating a new folder in `resources/views/vendor/navia/slug-name` where _slug-name_ is the _slug_ that you have assigned for that table. There are 4 files that you can override:

* browse.blade.php
* edit-add.blade.php
* read.blade.php
* order.blade.php

Alternatively you can override the views for **all** BREADs by creating any of the above files under `resources/views/vendor/navia/bread`

## Overriding submit button:
You can override the submit button without the need to override the whole `edit-add.blade.php` by extending the `submit-buttons` section:  
```blade
@extends('navia::bread.edit-add')
@section('submit-buttons')
    @parent
    <button type="submit" class="btn btn-primary save">Save And Publish</button>
@endsection
```

## Using custom Controllers

You can override the controller for a single BREAD by creating a controller which extends Navias controller, for example:

```php
<?php

namespace App\Http\Controllers;

class NaviaCategoriesController extends \Navia\Http\Controllers\NaviaBaseController
{
    //...
}
```

After that go to the BREAD-settings and fill in the Controller Name with your fully-qualified class-name:

![](../.gitbook/assets/bread_controller.png)

You can now override all methods from the [NaviaBaseController]

## Overriding Navias Controllers

{% hint style="danger" %}
**Only use this method if you know what you are doing**  
We don't recommend or support overriding all controllers as you won't get any code-changes made in future updates.
{% endhint %}

If you want to override any of Navias core controllers you first have to change your config file `config/navia.php`:

```php
/*
|--------------------------------------------------------------------------
| Controllers config
|--------------------------------------------------------------------------
|
| Here you can specify naviacontroller settings
|
*/

'controllers' => [
    'namespace' => 'App\\Http\\Controllers\\Navia',
],
```

Then run `php artisan navia:controllers`, Navia will now use the child controllers which will be created at `App/Http/Controllers/Navia`

## Overriding Navia-Models

You are also able to override Navias models if you need to.  
To do so, you need to add the following to your AppServiceProviders register method:

```php
Navia::useModel($name, $object);
```

Where **name** is the class-name of the model and **object** the fully-qualified name of your custom model. For example:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher;
use Navia\Facades\Navia;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Navia::useModel('DataRow', \App\DataRow::class);
    }
    // ...
}
```

The next step is to create your model and make it extend the original model. In case of `DataRow`:

```php
<?php

namespace App;

class DataRow extends \Navia\Models\DataRow
{
    // ...
}
```

If the model you are overriding has an associated BREAD, go to the BREAD settings for the model you are overriding
and replace the Model Name with your fully-qualified class-name. For example, if you are overriding the Navia `Menu`
model with your own `App\Menu` model:

![](../.gitbook/assets/bread_override_navia_models.png)

