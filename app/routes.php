<?php
// Define routes here

//$this->router->get('/', function() {
//    echo 'Hello World!';
//});

/**
 * Register, login and logout
 */
$this->router->post('register', '\Controllers\AuthController@registerAction');
$this->router->post('login', '\Controllers\AuthController@loginAction');
$this->router->get('logout', '\Controllers\AuthController@logoutAction');


/**
 * User
 */
$this->router->get('user/{id}/{big_data?}', '\Controllers\UserController@getUserAction')->where('id', '[0-9]+');
$this->router->post('user/{id}/edit', '\Controllers\UserController@editAction')->where('id', '[0-9]+');
$this->router->get('user/search/{query?}/{limit?}/{offset?}', '\Controllers\UserController@searchAction');
$this->router->post('user/{id}/follow', '\Controllers\UserController@followAction');
$this->router->post('user/{id}/unfollow', '\Controllers\UserController@unfollowAction');

/**
 * Statuses
 */
$this->router->post('statuses/create', '\Controllers\StatusesController@createAction');
$this->router->get('statuses/{id}', '\Controllers\StatusesController@getStatusAction')->where('id', '[0-9]+');
$this->router->get('statuses/user-timeline/{user_id}/{limit}/{offset}/{page?}', '\Controllers\StatusesController@getUserStatusesAction');
$this->router->get('statuses/home', '\Controllers\StatusesController@getStatusesHomeAction');