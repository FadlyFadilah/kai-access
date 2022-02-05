<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['middleware' => ['auth']], function ($router) {
    $router->get('admin/trains', 'TrainController@index');
    $router->get('admin/train/{slug}', 'TrainController@show');
    $router->post('admin/trains', 'TrainController@store');
    $router->put('admin/train/{slug}', 'TrainController@update');
    $router->delete('admin/train/{slug}', 'TrainController@destroy');

    $router->get('admin/stations', 'StationController@index');
    $router->get('admin/station/{slug}', 'StationController@show');
    $router->post('admin/stations', 'StationController@store');
    $router->put('admin/station/{slug}', 'StationController@update');
    $router->delete('admin/station/{slug}', 'StationController@destroy');

    $router->get('admin/schedules', 'ScheduleController@index');
    $router->get('admin/schedule/{id}', 'ScheduleController@show');
    $router->post('admin/schedules', 'ScheduleController@store');
    $router->put('admin/schedule/{id}', 'ScheduleController@update');
    $router->delete('admin/schedule/{id}', 'ScheduleController@destroy');

    $router->get('admin/tickets', 'TicketController@index');
    $router->get('admin/ticket/{id}', 'TicketController@show');
    $router->post('admin/tickets', 'TicketController@store');
    $router->put('admin/ticket/{id}', 'TicketController@update');
    $router->delete('admin/ticket/{id}', 'TicketController@destroy');

    
    $router->get('admin/users', 'UserController@index');
    $router->get('admin/user/{id}', 'UserController@show');
    $router->post('admin/users', 'UserController@store');
    $router->put('admin/user/{id}', 'UserController@update');
    $router->delete('admin/user/{id}', 'UserController@destroy');
    $router->patch('admin/user/{id}', 'UserController@updatePicture');

    $router->get('user/tickets', 'TicketController@index');
    $router->get('user/ticket/{id}', 'TicketController@show');

    $router->get('user/orders', 'PublicOrderController@index');
    $router->get('user/order/{id}', 'PublicOrderController@show');
    $router->post('user/orders', 'PublicOrderController@store');
    $router->put('user/order/{id}', 'PublicOrderController@update');
    $router->delete('user/order/{id}', 'PublicOrderController@destroy');

    $router->get('admin/orders', 'OrderController@index');
    $router->get('admin/order/{id}', 'OrderController@show');
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/registration', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
    $router->post('/passwordverify/{email}', 'AuthController@sendMail');
    $router->post('/password/forgot', 'AuthController@forgot');
    $router->put('/password/new/{id}', 'AuthController@newPass');
});
