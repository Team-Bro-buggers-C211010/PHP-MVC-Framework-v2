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

    public function post(string $path, $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function resolve(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $method = strtoupper($method);

        if (!isset($this->routes[$method])) {
            $this->response->sendStatusCode(404);
            $this->response->setContent(['error' => 'Route not found']);
            return;
        }

        $found = false;
        foreach ($this->routes[$method] as $routePath => $handler) {
            // Create regex pattern for dynamic routes (e.g., /users/:id)
            $pattern = preg_replace('/\//', '\\/', $routePath);
            $pattern = preg_replace('/\:(\w+)/', '(?P<$1>[^\/]+)', $pattern);
            $pattern = '/^' . $pattern . '$/';

            if (preg_match($pattern, $path, $matches)) {
                $found = true;

                // Extract route params (e.g., ['id' => '1'])
                $routeParams = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Get body for POST/PUT (JSON), query for GET/DELETE
                $body = in_array($method, ['POST', 'PUT']) ? $this->request->input() : [];
                $query = $_GET;

                // Merge all params into one array
                $params = array_merge($routeParams, $query, $body);

                try {
                    // Handle [ClassName::class, 'method'] format
                    if (is_array($handler) && is_string($handler[0]) && class_exists($handler[0])) {
                        $controller = new $handler[0]($this->request, $this->response);
                        call_user_func_array([$controller, $handler[1]], [$params]);
                    }
                    // Handle closures or callable functions
                    elseif (is_callable($handler)) {
                        call_user_func($handler, $params);
                    } else {
                        throw new \Exception("Invalid route handler");
                    }
                } catch (\Throwable $e) {
                    $this->response->sendStatusCode(500);
                    $this->response->setContent([
                        'error' => 'Internal server error',
                        'details' => $e->getMessage()
                    ]);
                }

                return;
            }
        }

        if (!$found) {
            $this->response->sendStatusCode(404);
            $this->response->setContent(['error' => 'Route not found']);
        }
    }
}