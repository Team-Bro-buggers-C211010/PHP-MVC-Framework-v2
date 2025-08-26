<?php

namespace Core\MVC;

use Core\Http\Request;
use Core\Http\Response;

class BaseController
{
    public $request;
    public $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}