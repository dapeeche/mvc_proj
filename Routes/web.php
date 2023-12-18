<?php

use Core\Router;

Router::add(
    'users',
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'index',
        'method' => 'GET'
    ]
);

Router::add(
    'users/{id:\d+}',
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'show',
        'method' => 'GET'
    ]
);

Router::add(
    'users/{id:\d+}/edit',
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'edit',
        'method' => 'GET'
    ]
);

Router::add(
    'users/{id:\d+}/update',
    [
        'controller' => \App\Controllers\UsersController::class,
        'action' => 'update',
        'method' => 'POST'
    ]
);
