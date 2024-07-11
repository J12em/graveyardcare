<?php

class Router
{
    protected $routes = [];

    public function get($route, $controller)
    {
        $this->addRoute('GET', $route, $controller);
    }

    public function post($route, $controller)
    {
        $this->addRoute('POST', $route, $controller);
    }

    public function put($route, $controller)
    {
        $this->addRoute('PUT', $route, $controller);
    }

    public function delete($route, $controller)
    {
        $this->addRoute('DELETE', $route, $controller);
    }

    protected function addRoute($method, $route, $controller)
    {
        $this->routes[$method][$route] = $controller;
    }

    public function dispatch()
    {
        $uri = $this->getUri() ?? "home";
        $method = $_SERVER['REQUEST_METHOD'];
        $routeFound = false;

        foreach ($this->routes[$method] as $route => $controller) {
            $pattern = preg_replace('/\{[^\}]+\}/', '([^/]+)', $route);
            if (preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches);
                $routeFound = true;
                $_controller = explode("@", $controller);
                $this->callControllerMethod(
                    $_controller[0],
                    $_controller[1],
                    $matches
                );
                break;
            }
        }

        if (!$routeFound) {
            $this->callControllerMethod(
                "RootController",
                "notFound"
            );
            // http_response_code(404);
            // echo "404 Not Found";
        }
    }

    private function getUri()
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $uri = str_replace(ROUTE_URL, '', $uri);
        $uri = ltrim($uri, '/');
        return $uri == '' ? '' : $uri;
    }

    protected function callControllerMethod($controller, $method, $parameters = [])
    {
        require_once __DIR__ . "/../controllers/$controller.php";
        $controller = new $controller;
        call_user_func_array([$controller, $method], $parameters);
    }
}
