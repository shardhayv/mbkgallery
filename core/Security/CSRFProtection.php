<?php
class CSRFProtection {
    const TOKEN_NAME = 'csrf_token';
    const TOKEN_LENGTH = 32;
    
    public static function generateToken() {
        if (!isset($_SESSION[self::TOKEN_NAME])) {
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(self::TOKEN_LENGTH));
        }
        return $_SESSION[self::TOKEN_NAME];
    }
    
    public static function validateToken($token) {
        return isset($_SESSION[self::TOKEN_NAME]) && 
               hash_equals($_SESSION[self::TOKEN_NAME], $token);
    }
    
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . htmlspecialchars($token) . '">';
    }
    
    public static function validateRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[self::TOKEN_NAME] ?? '';
            if (!self::validateToken($token)) {
                SecurityManager::logSecurityEvent('CSRF_ATTACK', 'Invalid CSRF token');
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
    }
}
?>