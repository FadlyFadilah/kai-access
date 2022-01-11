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