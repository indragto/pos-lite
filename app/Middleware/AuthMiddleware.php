<?php

namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware
{
    public function handle(?string $param = null): void
    {
        if (!Auth::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized', 'redirect' => url('login')]);
                exit;
            }

            header('Location: ' . url('login'));
            exit;
        }

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

        // Block POS/Journal creation before opening balance
        $currentUrl = $_GET['url'] ?? '';
        $blocked = ['pos', 'transactions/store', 'accounting/journal/create', 'accounting/journal/store'];
        foreach ($blocked as $path) {
            if ($currentUrl === $path || str_starts_with($currentUrl, $path . '/')) {
                // Check DB directly (not cached)
                $db = new \App\Core\Database();
                $obDone = $db->fetchColumn("SELECT value FROM settings WHERE key = 'opening_balance_done'");
                if ($obDone !== '1') {
                    $_SESSION['flash'] = $_SESSION['flash'] ?? [];
                    $_SESSION['flash']['error'] = 'Set Opening Balance first! Go to Accounting Settings → Opening Balance.';
                    header('Location: ' . url('accounting/settings'));
                    exit;
                }
                break;
            }
        }
    }
}
