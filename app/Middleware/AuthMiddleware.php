<?php
class AuthMiddleware {
    const SESSION_TIMEOUT = 3600; // 1 hour
    const IDLE_TIMEOUT = 1800;    // 30 minutes
    
    public static function requireAuth() {
        if (!self::isAuthenticated()) {
            self::logout();
            header('Location: /gallery/admin/login');
            exit;
        }
        
        self::refreshSession();
    }
    
    public static function redirectIfAuthenticated() {
        if (self::isAuthenticated()) {
            header('Location: /gallery/admin');
            exit;
        }
    }
    
    public static function isAuthenticated() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > self::SESSION_TIMEOUT) {
            SecurityManager::logSecurityEvent('SESSION_TIMEOUT', 'Session expired due to timeout');
            return false;
        }
        
        // Check idle timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > self::IDLE_TIMEOUT) {
            SecurityManager::logSecurityEvent('SESSION_IDLE', 'Session expired due to inactivity');
            return false;
        }
        
        return true;
    }
    
    public static function login($userId, $username) {
        session_regenerate_id(true);
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $userId;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['session_token'] = bin2hex(random_bytes(32));
        
        SecurityManager::logSecurityEvent('ADMIN_LOGIN_SUCCESS', "User ID: $userId, Username: $username");
    }
    
    public static function logout() {
        if (isset($_SESSION['admin_username'])) {
            SecurityManager::logSecurityEvent('ADMIN_LOGOUT', "Username: {$_SESSION['admin_username']}");
        }
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    private static function refreshSession() {
        $_SESSION['last_activity'] = time();
        
        // Regenerate session ID periodically for security
        if (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration']) > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    public static function getCurrentUser() {
        if (self::isAuthenticated()) {
            return [
                'id' => $_SESSION['admin_id'] ?? null,
                'username' => $_SESSION['admin_username'] ?? null
            ];
        }
        return null;
    }
}
?>