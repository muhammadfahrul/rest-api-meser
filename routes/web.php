<?php

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

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->post('/login', 'LoginController@login');

    $router->get('/users', 'UserController@showAll');
    $router->get('/users/{id}', 'UserController@showId');
    $router->post('/users', 'UserController@add');
    $router->put('/users/{id}', 'UserController@update');
    $router->delete('/users/{id}', 'UserController@delete');

    $router->get('/products', 'ProductController@showAll');
    $router->get('/products-image', 'ProductController@showAllImage');
    $router->get('/products-join', 'ProductController@showAllJoin');
    $router->get('/products/{id}', 'ProductController@showId');
    $router->get('/products-join/{id}', 'ProductController@showIdJoin');
    $router->post('/products', 'ProductController@add');
    $router->put('/products/{id}', 'ProductController@update');
    $router->delete('/products/{id}', 'ProductController@delete');

    $router->get('/categories', 'CategoryController@showAll');
    $router->get('/categories/{id}', 'CategoryController@showId');
    $router->post('/categories', 'CategoryController@add');
    $router->put('/categories/{id}', 'CategoryController@update');
    $router->delete('/categories/{id}', 'CategoryController@delete');

    $router->get('/orders', 'OrderController@showAll');
    $router->get('/orders-join', 'OrderController@showAllJoin');
    $router->get('/orders/{id}', 'OrderController@showId');
    $router->get('/orders-join/{id}', 'OrderController@showIdJoin');
    $router->post('/orders', 'OrderController@add');
    $router->put('/orders/{id}', 'OrderController@update');
    $router->delete('/orders/{id}', 'OrderController@delete');

    $router->get('/payments', 'PaymentController@showAll');
    $router->get('/payments-join', 'PaymentController@showAllJoin');
    $router->get('/payments/{id}', 'PaymentController@showId');
    $router->get('/payments-join/{id}', 'PaymentController@showIdJoin');
    $router->post('/payments', 'PaymentController@add');
    $router->delete('/payments/{id}', 'PaymentController@delete');
    $router->post('/payments/midtrans/push', 'PaymentController@midtransPush');
});