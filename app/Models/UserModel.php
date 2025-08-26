<?php

namespace Models;
use Core\MVC\Model;

class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct("users");
    }
}