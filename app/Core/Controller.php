<?php

namespace App\Core;

class Controller
{
    public function __construct()
    {
    }

    /**
     * Render a view
     */
    protected function view(string $viewPath, array $data = [], ?string $layout = 'main'): void
    {
        // Extract data to variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        $viewFile = APP_PATH . '/Views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: {$viewFile}");
        }

        include $viewFile;

        // Get the content
        $content = ob_get_clean();

        // If layout is specified, wrap content in layout
        if ($layout) {
            $layoutFile = APP_PATH . '/Views/layouts/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
                return;
            }
        }

        // Otherwise, just output the content
        echo $content;
    }

    /**
     * Render content without layout
     */
    protected function viewOnly(string $viewPath, array $data = []): void
    {
        $this->view($viewPath, $data, null);
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url): void
    {
        // If URL doesn't start with http, make it relative to app URL
        if (!preg_match('/^https?:\/\//', $url)) {
            $baseUrl = rtrim(config('app.url', ''), '/');
            $url = $baseUrl . '/' . ltrim($url, '/');
        }

        header("Location: {$url}");
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Load a model
     */
    protected function model(string $modelName): mixed
    {
        $modelClass = "App\\Models\\{$modelName}";

        if (!class_exists($modelClass)) {
            die("Model not found: {$modelClass}");
        }

        return new $modelClass();
    }

    /**
     * Get input data
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            return $_GET[$key] ?? $default;
        }

        // POST or other methods
        if ($method === 'POST') {
            return $_POST[$key] ?? $default;
        }

        // Check JSON body
        $json = json_decode(file_get_contents('php://input'), true);
        return $json[$key] ?? $default;
    }

    /**
     * Get all input
     */
    protected function allInput(): array
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            return $_GET;
        }

        if ($method === 'POST') {
            return $_POST;
        }

        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    /**
     * Get session data
     */
    protected function session(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set session flash message
     */
    protected function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Get and clear flash message
     */
    protected function getFlash(string $key): ?string
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    /**
     * Check if flash message exists
     */
    protected function hasFlash(string $key): bool
    {
        return isset($_SESSION['flash'][$key]);
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf(): bool
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method !== 'POST') {
            return true;
        }

        $token = $_POST['csrf_token'] ?? '';
        return csrf_verify($token);
    }

    /**
     * Require valid CSRF or return error
     */
    protected function requireCsrf(): void
    {
        if (!$this->validateCsrf()) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Invalid CSRF token'], 403);
            }
            $this->setFlash('error', 'Invalid CSRF token');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? url(''));
        }
    }

    /**
     * Check if request is AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get uploaded file
     */
    protected function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }
}
