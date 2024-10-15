<?php

namespace SimplePHPRouter;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private array $groupStack = [];

    public function addRoute(string $method, string $uri, $handler, ?string $name = null): void
    {
        $uri = $this->getGroupPrefix() . '/' . trim($uri, '/');
        $uri = implode('/', array_filter(explode('/', $uri)));

        $route = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'handler' => $handler,
        ];

        $this->routes[] = $route;

        if ($name) {
            $this->namedRoutes[$name] = $uri;
        }
    }

    public function get(string $uri, $handler, ?string $name = null): void
    {
        $this->addRoute('GET', $uri, $handler, $name);
    }

    public function post(string $uri, $handler, ?string $name = null): void
    {
        $this->addRoute('POST', $uri, $handler, $name);
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        call_user_func($callback, $this);
        array_pop($this->groupStack);
    }

    private function getGroupPrefix(): string
    {
        if (!empty($this->groupStack)) {
            return end($this->groupStack)['prefix'] ?? '';
        }
        return '';
    }

    public function dispatch(string $method, string $uri)
    {
        $method = strtoupper($method);
        $uri = trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('/\/{([^\/]+)}/', '/(?<$1>[^/]+)', $route['uri']);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $this->executeHandler($route['handler'], $params);
            }
        }

        throw new \Exception("Route not found", 404);
    }

    private function executeHandler($handler, array $params)
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler)) {
            [$class, $method] = explode('@', $handler);
            $instance = new $class();
            return call_user_func_array([$instance, $method], $params);
        }

        throw new \Exception("Invalid route handler");
    }

    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route not found: {$name}");
        }

        $uri = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $uri = str_replace("{{$key}}", $value, $uri);
        }

        return '/' . $uri;
    }
}