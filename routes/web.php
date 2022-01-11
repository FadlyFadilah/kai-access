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
    $router->put('admin/trains/{slug}', 'TrainController@update');
    $router->delete('admin/trains/{slug}', 'TrainController@destroy');

    $router->get('admin/stations', 'StationController@index');
    $router->get('admin/station/{slug}', 'StationController@show');
    $router->post('admin/stations', 'StationController@store');
    $router->put('admin/station/{slug}', 'StationController@update');
    $router->delete('admin/station/{slug}', 'StationController@destroy');

    $router->get('admin/schedules', 'ScheduleController@index');
    $router->get('admin/schedule/{slug}', 'ScheduleController@show');
    $router->post('admin/schedules', 'ScheduleController@store');
    $router->put('admin/schedule/{slug}', 'ScheduleController@update');
    $router->delete('admin/schedule/{slug}', 'ScheduleController@destroy');

    $router->get('admin/tickets', 'TicketController@index');
    $router->get('admin/ticket/{id}', 'TicketController@show');
    $router->post('admin/tickets', 'TicketController@store');
    $router->put('admin/ticket/{id}', 'TicketController@update');
    $router->delete('admin/ticket/{id}', 'TicketController@destroy');
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/registration', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});
