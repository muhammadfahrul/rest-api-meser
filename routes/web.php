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
    $router->get('/user/{id}', 'UserController@showId');
    $router->post('/user', 'UserController@add');
    $router->put('/user/{id}', 'UserController@update');
    $router->delete('/user/{id}', 'UserController@delete');

    $router->get('/products', 'ProductController@showAll');
    $router->get('/product-images', 'ProductController@showAllImage');
    $router->get('/product-orders', 'ProductController@showAllProductOrder');
    $router->get('/product-categories', 'ProductController@showAllProductCategory');
    $router->get('/product/{id}', 'ProductController@showId');
    $router->get('/product-category-id/{id}', 'ProductController@showIdCategory');
    $router->get('/product-category/{id}', 'ProductController@showIdProductCategory');
    $router->post('/product', 'ProductController@add');
    $router->put('/product/{id}', 'ProductController@update');
    $router->delete('/product/{id}', 'ProductController@delete');

    $router->get('/categories', 'CategoryController@showAll');
    $router->get('/category-products', 'CategoryController@showAllCategoryProduct');
    $router->get('/category/{id}', 'CategoryController@showId');
    $router->get('/category-product/{id}', 'CategoryController@showIdCategoryProduct');
    $router->post('/category', 'CategoryController@add');
    $router->put('/category/{id}', 'CategoryController@update');
    $router->delete('/category/{id}', 'CategoryController@delete');

    $router->get('/orders', 'OrderController@showAll');
    $router->get('/order-products', 'OrderController@showAllOrderProduct');
    $router->get('/order/{code}', 'OrderController@showId');
    $router->get('/order-product/{code}', 'OrderController@showIdOrderProduct');
    $router->post('/order', 'OrderController@add');
    $router->put('/order/{code}', 'OrderController@update');
    $router->delete('/order/{code}', 'OrderController@delete');

    $router->get('/payments', 'PaymentController@showAll');
    $router->get('/payment-orders', 'PaymentController@showAllPaymentOrder');
    $router->get('/payment/{id}', 'PaymentController@showId');
    $router->get('/payment-order/{id}', 'PaymentController@showIdPaymentOrder');
    $router->post('/payment', 'PaymentController@add');
    $router->delete('/payment/{id}', 'PaymentController@delete');
    $router->post('/payment/midtrans/push', 'PaymentController@midtransPush');
});