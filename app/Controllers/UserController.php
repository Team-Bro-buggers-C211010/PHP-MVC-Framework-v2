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

    // GET /users - Get all users
    public function getUsers($params = [])
    {
        $users = $this->userModels->find($params);
        $this->response->sendStatusCode(200);
        $this->response->setContent($users);
    }

    // GET /users/:id - Get a single user
    public function show($params)
    {
        $id = $params['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            $this->response->sendStatusCode(400);
            $this->response->setContent(['error' => 'Valid ID required']);
            return;
        }

        $user = $this->userModels->findById($id);

        if (!$user) {
            $this->response->sendStatusCode(404);
            $this->response->setContent(['error' => 'User not found']);
            return;
        }

        $this->response->sendStatusCode(200);
        $this->response->setContent($user);
    }

    // POST /users - Create a new user
    public function createUser($params)
    {
        // $params is the JSON body (e.g., ['name' => 'John', 'email' => 'john@example.com'])
        if (empty($params['name']) || empty($params['email'])) {
            $this->response->sendStatusCode(400);
            $this->response->setContent(['error' => 'Name and email are required']);
            return;
        }

        $newId = $this->userModels->create($params);

        $this->response->sendStatusCode(201);
        $this->response->setContent(['message' => 'User created', 'id' => $newId]);
    }

    // PUT /users/:id - Update a user
    public function updateUser($params)
    {
        $id = $params['id'] ?? null;
        unset($params['id']); // Remove id from data

        if (!$id || !is_numeric($id)) {
            $this->response->sendStatusCode(400);
            $this->response->setContent(['error' => 'Valid ID required']);
            return;
        }

        if (empty($params)) {
            $this->response->sendStatusCode(400);
            $this->response->setContent(['error' => 'Update data required']);
            return;
        }

        $updated = $this->userModels->update($id, $params);

        if (!$updated) {
            $this->response->sendStatusCode(404);
            $this->response->setContent(['error' => 'User not found or update failed']);
            return;
        }

        $this->response->sendStatusCode(200);
        $this->response->setContent(['message' => 'User updated']);
    }

    // DELETE /users/:id - Delete a user
    public function deleteUser($params)
    {
        $id = $params['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            $this->response->sendStatusCode(400);
            $this->response->setContent(['error' => 'Valid ID required']);
            return;
        }

        $deleted = $this->userModels->delete($id);

        if (!$deleted) {
            $this->response->sendStatusCode(404);
            $this->response->setContent(['error' => 'User not found or deletion failed']);
            return;
        }

        $this->response->sendStatusCode(200);
        $this->response->setContent(['message' => 'User deleted']);
    }
}