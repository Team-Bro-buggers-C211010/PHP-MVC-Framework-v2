<?php

use Controllers\UserController;

$router->get('/users', [UserController::class, 'getUsers']);
$router->get('/users/:id', [UserController::class, 'show']);
$router->post('/users', [UserController::class, 'createUser']);
$router->put('/users/:id', [UserController::class, 'updateUser']);
$router->delete('/users/:id', [UserController::class, 'deleteUser']);