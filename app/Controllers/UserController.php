<?php

namespace Controllers;

use Core\Http\Request;
use Core\Http\Response;
use Core\MVC\BaseController;
use Models\UserModel;

class UserController extends BaseController
{
    public $userModels;
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->userModels = new UserModel();
    }

    public function getUsers()
    {
        $users = $this->userModels->find();
        $this->response->sendStatusCode(200);
        $this->response->setContent($users);
    }

    public function createUser()
    {
        
        $user = $this->userModels->create();
    }
}
