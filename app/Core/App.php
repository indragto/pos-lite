<?php

namespace App\Core;

class App
{
    protected array $routes = [];
    protected string $url = '';

    public function __construct()
    {
        $this->routes = require CONFIG_PATH . '/routes.php';
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        $this->parseUrl();
        $route = $this->matchRoute();

        if (!$route) {
            $this->notFound();
            return;
        }

        // Check middleware
        if (isset($route['middleware']) && !empty($route['middleware'])) {
            foreach ($route['middleware'] as $middleware) {
                $this->handleMiddleware($middleware);
            }
        }

        // Load controller
        $controllerName = $route['controller'];
        $controllerClass = "App\\Controllers\\{$controllerName}Controller";

        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }

        $controller = new $controllerClass();
        $action = $route['action'] ?? 'index';

        if (!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }

        // Call the action with parameters
        $params = $route['params'] ?? [];
        call_user_func_array([$controller, $action], $params);
    }

    /**
     * Parse URL from request
     */
    protected function parseUrl(): void
    {
        $url = '';

        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
        }

        $this->url = $url;
    }

    /**
     * Match URL to route
     */
    protected function matchRoute(): ?array
    {
        $url = $this->url;

        // Exact match first
        if (isset($this->routes[$url])) {
            return $this->routes[$url];
        }

        // Pattern matching with parameters
        foreach ($this->routes as $pattern => $route) {
            // Convert route pattern to regex
            // :id becomes ([0-9]+), :slug becomes ([a-zA-Z0-9-_]+)
            $regexPattern = preg_replace(
                '/:([a-zA-Z]+)/',
                '([a-zA-Z0-9-_]+)',
                $pattern
            );
            $regexPattern = '#^' . $regexPattern . '$#';

            if (preg_match($regexPattern, $url, $matches)) {
                // Remove the full match (index 0)
                array_shift($matches);

                // Extract parameter names from pattern
                preg_match_all('/:([a-zA-Z]+)/', $pattern, $paramNames);

                // Build params array
                $params = [];
                foreach ($paramNames[1] as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }

                $route['params'] = $params;
                return $route;
            }
        }

        return null;
    }

    /**
     * Handle middleware
     */
    protected function handleMiddleware(string $middleware): void
    {
        // Parse middleware with parameters (e.g., rbac:products.view)
        $parts = explode(':', $middleware, 2);
        $name = $parts[0];
        $param = $parts[1] ?? null;

        $middlewareClass = "App\\Middleware\\{$name}Middleware";

        if (!class_exists($middlewareClass)) {
            // Try to handle built-in middleware
            switch ($name) {
                case 'auth':
                    $middlewareClass = "App\\Middleware\\AuthMiddleware";
                    break;
                case 'rbac':
                    $middlewareClass = "App\\Middleware\\RBACMiddleware";
                    break;
                default:
                    $this->notFound();
                    return;
            }
        }

        if (!class_exists($middlewareClass)) {
            $this->notFound();
            return;
        }

        $middlewareInstance = new $middlewareClass();

        if (method_exists($middlewareInstance, 'handle')) {
            $middlewareInstance->handle($param);
        }
    }

    /**
     * Show 404 page
     */
    protected function notFound(): void
    {
        http_response_code(404);

        // Check if AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not Found']);
            return;
        }

        // Show error page
        $errorFile = APP_PATH . '/Views/errors/404.php';
        if (file_exists($errorFile)) {
            include $errorFile;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The page you are looking for does not exist.</p>";
            echo "<a href='" . url('') . "'>Back to Home</a>";
        }
    }

    /**
     * Get current URL
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
