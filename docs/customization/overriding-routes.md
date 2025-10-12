# Overriding Routes

You can override any Navia routes by writing the routes you want to overwrite below `Navia::routes()`. For example if you want to override your post LoginController:

```php
Route::group(['prefix' => 'admin'], function () {
   Navia::routes();

   // Your overwrites here
   Route::post('login', ['uses' => 'MyAuthController@postLogin', 'as' => 'postlogin']);
});
```

