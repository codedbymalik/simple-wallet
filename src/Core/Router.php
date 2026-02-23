<?php

namespace Core;

use Exception;

class Router
{
    private array $routes = [];
    private string $requestMethod;
    private string $requestUri;

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        
        // Remove trailing slashes except for root
        if ($this->requestUri !== '/' && substr($this->requestUri, -1) === '/') {
            $this->requestUri = rtrim($this->requestUri, '/');
        }
    }

    public function register(string $method, string $path, callable $callback): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'callback' => $callback,
        ];
    }

    public function dispatch(): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $this->requestMethod) {
                continue;
            }

            // Convert route pattern to regex (e.g., /api/accounts/{id} -> /api/accounts/(\d+))
            $pattern = $this->convertPathToRegex($route['path']);

            if (preg_match($pattern, $this->requestUri, $matches)) {
                array_shift($matches); // Remove full match
                
                try {
                    call_user_func_array($route['callback'], $matches);
                    return;
                } catch (Exception $e) {
                    error_log("Route handler error: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode(['error' => 'Internal server error']);
                    return;
                }
            }
        }

        // Route not found
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }

    private function convertPathToRegex(string $path): string
    {
        // Escape slashes and dots
        $pattern = preg_quote($path, '/');
        
        // Replace {id}, {userId}, etc. with regex to match integers
        $pattern = preg_replace('/\\\{[a-zA-Z_][a-zA-Z0-9_]*\\\}/', '(\d+)', $pattern);
        
        // Wrap with delimiters and anchors
        return "/^" . $pattern . "$/";
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function getRequestUri(): string
    {
        return $this->requestUri;
    }
}
