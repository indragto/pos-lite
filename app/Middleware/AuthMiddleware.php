<?php

namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware
{
    public function handle(?string $param = null): void
    {
        if (!Auth::check()) {
            // Store intended URL for redirect after login
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

            // Check if AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized', 'redirect' => url('login')]);
                exit;
            }

            header('Location: ' . url('login'));
            exit;
        }

        // Check session timeout
        $timeout = (int) setting('session_timeout', 30) * 60;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            Auth::logout();
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Session expired', 'redirect' => url('login?timeout=1')]);
                exit;
            }

            header('Location: ' . url('login?timeout=1'));
            exit;
        }

        $_SESSION['last_activity'] = time();
    }
}
