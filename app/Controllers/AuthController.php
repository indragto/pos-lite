<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLogin(): void
    {
        // Redirect if already logged in
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        $this->viewOnly('auth/login', [
            'title' => 'Login - POS System'
        ], 'auth');
    }

    /**
     * Process login
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
        }

        $username = $this->input('username');
        $password = $this->input('password');

        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Username and password are required');
            $this->redirect('login');
        }

        $result = Auth::login($username, $password);

        if ($result['success']) {
            // Redirect to intended URL or dashboard
            $redirectUrl = $_SESSION['redirect_after_login'] ?? 'dashboard';
            unset($_SESSION['redirect_after_login']);

            // Parse URL if it contains the base path
            if (str_starts_with($redirectUrl, '/')) {
                $redirectUrl = ltrim($redirectUrl, '/');
            }

            // Strip query parameters for safety
            $redirectUrl = explode('?', $redirectUrl)[0];

            $this->redirect($redirectUrl);
        } else {
            $this->setFlash('error', $result['message']);
            $this->redirect('login');
        }
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        Auth::logout();
        $this->redirect('login');
    }
}
