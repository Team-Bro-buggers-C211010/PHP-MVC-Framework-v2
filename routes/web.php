<?php

use Controllers\UserController;

$router->get('/users', [UserController::class,'getUsers']);
