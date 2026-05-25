<?php

namespace App\Core;

class Auth
{
    /**
     * Authenticate user
     */
    public static function login(string $username, string $password): array
    {
        $db = new Database();

        // Get user with role info
        $user = $db->fetch("
            SELECT u.*, r.name as role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.username = :username AND u.is_active = 1
        ", ['username' => $username]);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }

        // Get user permissions
        $permissions = $db->fetchAll("
            SELECT p.name
            FROM permissions p
            INNER JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id
        ", ['role_id' => $user['role_id']]);

        $permissionNames = array_column($permissions, 'name');

        // Remove password from session data
        unset($user['password']);

        // Store user in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = array_merge($user, [
            'permissions' => $permissionNames
        ]);
        $_SESSION['login_time'] = time();

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        return ['success' => true, 'user' => $user];
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        // Clear session
        $_SESSION = [];

        // Destroy session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();
    }

    /**
     * Check if user is logged in
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['user']);
    }

    /**
     * Get current user
     */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Get current user ID
     */
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Check if user has permission
     */
    public static function hasPermission(string $permission): bool
    {
        if (!self::check()) {
            return false;
        }

        $user = self::user();
        return in_array($permission, $user['permissions'] ?? []);
    }

    /**
     * Check if user has any of the given permissions
     */
    public static function hasAnyPermission(array $permissions): bool
    {
        if (!self::check()) {
            return false;
        }

        $user = self::user();
        $userPermissions = $user['permissions'] ?? [];

        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all given permissions
     */
    public static function hasAllPermissions(array $permissions): bool
    {
        if (!self::check()) {
            return false;
        }

        $user = self::user();
        $userPermissions = $user['permissions'] ?? [];

        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has role
     */
    public static function hasRole(string $role): bool
    {
        if (!self::check()) {
            return false;
        }

        $user = self::user();
        return ($user['role_name'] ?? '') === $role;
    }

    /**
     * Require authentication (redirects if not logged in)
     */
    public static function require(): void
    {
        if (!self::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . url('login'));
            exit;
        }

        // Check session timeout
        $timeout = (int) setting('session_timeout', 30) * 60;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::logout();
            header('Location: ' . url('login?timeout=1'));
            exit;
        }

        $_SESSION['last_activity'] = time();
    }

    /**
     * Require permission (shows 403 if not authorized)
     */
    public static function requirePermission(string $permission): void
    {
        self::require();

        if (!self::hasPermission($permission)) {
            http_response_code(403);
            die("Access Denied: You don't have permission to access this resource.");
        }
    }
}
