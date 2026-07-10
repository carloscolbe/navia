<?php

use Illuminate\Support\Str;
use Navia\Events\Routing;
use Navia\Events\RoutingAdmin;
use Navia\Events\RoutingAdminAfter;
use Navia\Events\RoutingAfter;
use Navia\Facades\Navia;

/*
|--------------------------------------------------------------------------
| Navia Routes
|--------------------------------------------------------------------------
|
| This file is where you may override any of the routes that are included
| with Navia.
|
*/

Route::group(['as' => 'navia.'], function () {
    event(new Routing());

    $namespacePrefix = '\\'.config('navia.controllers.namespace').'\\';

    Route::get('login', ['uses' => $namespacePrefix.'NaviaAuthController@login',     'as' => 'login']);
    Route::post('login', ['uses' => $namespacePrefix.'NaviaAuthController@postLogin', 'as' => 'postlogin']);

    Route::group(['middleware' => 'admin.user'], function () use ($namespacePrefix) {
        event(new RoutingAdmin());

        // Main Admin and Logout Route
        Route::get('/', ['uses' => $namespacePrefix.'NaviaController@index',   'as' => 'dashboard']);
        Route::post('logout', ['uses' => $namespacePrefix.'NaviaController@logout',  'as' => 'logout']);
        Route::post('upload', ['uses' => $namespacePrefix.'NaviaController@upload',  'as' => 'upload']);

        Route::get('profile', ['uses' => $namespacePrefix.'NaviaUserController@profile', 'as' => 'profile']);

        try {
            foreach (Navia::model('DataType')::all() as $dataType) {
                $breadController = $dataType->controller
                                 ? Str::start($dataType->controller, '\\')
                                 : $namespacePrefix.'NaviaBaseController';

                Route::get($dataType->slug.'/order', $breadController.'@order')->name($dataType->slug.'.order');
                Route::post($dataType->slug.'/action', $breadController.'@action')->name($dataType->slug.'.action');
                Route::post($dataType->slug.'/order', $breadController.'@update_order')->name($dataType->slug.'.update_order');
                Route::get($dataType->slug.'/{id}/restore', $breadController.'@restore')->name($dataType->slug.'.restore');
                Route::get($dataType->slug.'/relation', $breadController.'@relation')->name($dataType->slug.'.relation');
                Route::post($dataType->slug.'/remove', $breadController.'@remove_media')->name($dataType->slug.'.media.remove');
                Route::resource($dataType->slug, $breadController, ['parameters' => [$dataType->slug => 'id']]);
            }
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Custom routes hasn't been configured because: ".$e->getMessage(), 1);
        } catch (\Exception $e) {
            // do nothing, might just be because table not yet migrated.
        }

        // Menu Routes
        Route::group([
            'as'     => 'menus.',
            'prefix' => 'menus/{menu}',
        ], function () use ($namespacePrefix) {
            Route::get('builder', ['uses' => $namespacePrefix.'NaviaMenuController@builder',    'as' => 'builder']);
            Route::post('order', ['uses' => $namespacePrefix.'NaviaMenuController@order_item', 'as' => 'order_item']);

            Route::group([
                'as'     => 'item.',
                'prefix' => 'item',
            ], function () use ($namespacePrefix) {
                Route::delete('{id}', ['uses' => $namespacePrefix.'NaviaMenuController@delete_menu', 'as' => 'destroy']);
                Route::post('/', ['uses' => $namespacePrefix.'NaviaMenuController@add_item',    'as' => 'add']);
                Route::put('/', ['uses' => $namespacePrefix.'NaviaMenuController@update_item', 'as' => 'update']);
            });
        });

        // Settings
        Route::group([
            'as'     => 'settings.',
            'prefix' => 'settings',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'NaviaSettingsController@index',        'as' => 'index']);
            Route::post('/', ['uses' => $namespacePrefix.'NaviaSettingsController@store',        'as' => 'store']);
            Route::put('/', ['uses' => $namespacePrefix.'NaviaSettingsController@update',       'as' => 'update']);
            Route::delete('{id}', ['uses' => $namespacePrefix.'NaviaSettingsController@delete',       'as' => 'delete']);
            Route::get('{id}/move_up', ['uses' => $namespacePrefix.'NaviaSettingsController@move_up',      'as' => 'move_up']);
            Route::get('{id}/move_down', ['uses' => $namespacePrefix.'NaviaSettingsController@move_down',    'as' => 'move_down']);
            Route::put('{id}/delete_value', ['uses' => $namespacePrefix.'NaviaSettingsController@delete_value', 'as' => 'delete_value']);
        });

        // Admin Media
        Route::group([
            'as'     => 'media.',
            'prefix' => 'media',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'NaviaMediaController@index',              'as' => 'index']);
            Route::post('files', ['uses' => $namespacePrefix.'NaviaMediaController@files',              'as' => 'files']);
            Route::post('new_folder', ['uses' => $namespacePrefix.'NaviaMediaController@new_folder',         'as' => 'new_folder']);
            Route::post('delete_file_folder', ['uses' => $namespacePrefix.'NaviaMediaController@delete', 'as' => 'delete']);
            Route::post('move_file', ['uses' => $namespacePrefix.'NaviaMediaController@move',          'as' => 'move']);
            Route::post('rename_file', ['uses' => $namespacePrefix.'NaviaMediaController@rename',        'as' => 'rename']);
            Route::post('upload', ['uses' => $namespacePrefix.'NaviaMediaController@upload',             'as' => 'upload']);
            Route::post('crop', ['uses' => $namespacePrefix.'NaviaMediaController@crop',             'as' => 'crop']);
        });

        // BREAD Routes
        Route::group([
            'as'     => 'bread.',
            'prefix' => 'bread',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'NaviaBreadController@index',              'as' => 'index']);
            Route::get('{table}/create', ['uses' => $namespacePrefix.'NaviaBreadController@create',     'as' => 'create']);
            Route::post('/', ['uses' => $namespacePrefix.'NaviaBreadController@store',   'as' => 'store']);
            Route::get('{table}/edit', ['uses' => $namespacePrefix.'NaviaBreadController@edit', 'as' => 'edit']);
            Route::put('{id}', ['uses' => $namespacePrefix.'NaviaBreadController@update',  'as' => 'update']);
            Route::delete('{id}', ['uses' => $namespacePrefix.'NaviaBreadController@destroy',  'as' => 'delete']);
            Route::post('relationship', ['uses' => $namespacePrefix.'NaviaBreadController@addRelationship',  'as' => 'relationship']);
            Route::get('delete_relationship/{id}', ['uses' => $namespacePrefix.'NaviaBreadController@deleteRelationship',  'as' => 'delete_relationship']);
        });

        // Database Routes
        Route::resource('database', $namespacePrefix.'NaviaDatabaseController');

        // Compass Routes
        Route::group([
            'as'     => 'compass.',
            'prefix' => 'compass',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'NaviaCompassController@index',  'as' => 'index']);
            Route::post('/', ['uses' => $namespacePrefix.'NaviaCompassController@index',  'as' => 'post']);
        });

        event(new RoutingAdminAfter());
    });

    //Asset Routes
    Route::get('navia-assets', ['uses' => $namespacePrefix.'NaviaController@assets', 'as' => 'navia_assets']);

    event(new RoutingAfter());
});
