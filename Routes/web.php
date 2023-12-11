<?php

use Core\Router;

Router::add(
    'users/{id:\d+}/edit',
    [
        'controller' => \App\Controllers\UserController::class,
        'action' => 'edit',
        'method' => 'GET'
    ]
);