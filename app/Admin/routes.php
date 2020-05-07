<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    //学校管理
    $router->resource('/school','SchoolController');
    //版块标签管理
    $router->resource('/labers','LabersController');
    //作品管理
    $router->resource('/works','WorksController');
    //管理用户管理
    $router->resource('/users','UsersController');


});
