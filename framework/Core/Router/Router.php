<?php

namespace Core\Router;

use Core\Http\Request;
use Core\Http\Response;

class Router
{
    protected $request;
    protected $response;
    private array $routes = [];

    public function __construct(Response $response, Request $request)
    {
        $this->response = $response;
        $this->request = $request;
    }

    private function add(string $method, string $path, $handler): void
    {
        $method = strtoupper($method);
        $this->routes[$method][$path] = $handler;
    }

    public function get(string $path, $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function resolve(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $method = strtoupper($method);

        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            echo json_encode(['error' => 'Route not found']);
            return;
        }

        try {
            $params = $method === 'GET' ? $_GET : $_POST;

            // Handle [ClassName::class, 'method'] format
            if (is_array($handler) && is_string($handler[0]) && class_exists($handler[0])) {
                $controller = new $handler[0]($this->request, $this->response);
                call_user_func([$controller, $handler[1]], $params);
            }
            // Handle closures or callable functions
            elseif (is_callable($handler)) {
                call_user_func($handler, $params);
            } else {
                throw new \Exception("Invalid route handler");
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Internal server error',
                'details' => $e->getMessage()
            ]);
        }
    }
}
