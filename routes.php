<?php

use Illuminate\Routing\Router;

// 接口
Route::group([
    'prefix' => 'lbs',
    'namespace' => 'Qihucms\Lbs\Controllers\Api',
    'middleware' => ['api'],
    'as' => 'api.'
], function (Router $router) {
    $router->get('ip', 'LbsController@ip')->name('lbs.ip');
    $router->get('gps', 'LbsController@gps')->name('lbs.gps');
});

// 后台
Route::group([
    'prefix' => config('admin.route.prefix') . '/lbs',
    'namespace' => 'Qihucms\Lbs\Controllers\Admin',
    'middleware' => config('admin.route.middleware'),
    'as' => 'admin.'
], function (Router $router) {
    $router->get('config', 'ConfigController@index')->name('lbs.config');
});