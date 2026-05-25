<?php

namespace App\Middleware;

use App\Core\Auth;

class RBACMiddleware
{
    public function handle(?string $permission = null): void
    {
        if (!$permission) {
            return;
        }

        // Require authentication first
        Auth::require();

        // Check if user has the required permission
        if (!Auth::hasPermission($permission)) {
            // Check if AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden: You do not have permission to access this resource.']);
                exit;
            }

            http_response_code(403);
            
            // Show forbidden page or redirect
            $errorFile = APP_PATH . '/Views/errors/403.php';
            if (file_exists($errorFile)) {
                include $errorFile;
            } else {
                die("<h1>403 - Access Denied</h1><p>You don't have permission to access this resource.</p>");
            }
            exit;
        }
    }
}
