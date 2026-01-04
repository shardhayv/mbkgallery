<?php
class SecurityManager {
    private static $sessionStarted = false;
    
    public static function init() {
        self::startSecureSession();
        self::setSecurityHeaders();
        self::validateRequest();
    }
    
    private static function startSecureSession() {
        if (!self::$sessionStarted) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            session_start();
            self::$sessionStarted = true;
        }
    }
    
    private static function setSecurityHeaders() {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
    
    private static function validateRequest() {
        $suspicious_patterns = [
            '/\b(union|select|insert|update|delete|drop|create|alter)\b/i',
            '/<script[^>]*>.*?<\/script>/i',
            '/javascript:/i',
            '/onload|onerror|onclick/i'
        ];
        
        $request_data = array_merge($_GET, $_POST, $_COOKIE);
        foreach ($request_data as $value) {
            if (is_string($value)) {
                foreach ($suspicious_patterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        self::logSecurityEvent('SUSPICIOUS_INPUT', "Pattern: $pattern");
                        http_response_code(400);
                        die('Invalid request');
                    }
                }
            }
        }
    }
    
    public static function rateLimit($action, $limit = 100, $window = 3600) {
        $ip = self::getClientIP();
        $key = "rate_limit_{$action}_{$ip}";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'reset_time' => time() + $window];
        }
        
        $data = $_SESSION[$key];
        
        if (time() > $data['reset_time']) {
            $_SESSION[$key] = ['count' => 1, 'reset_time' => time() + $window];
            return true;
        }
        
        if ($data['count'] >= $limit) {
            self::logSecurityEvent('RATE_LIMIT_EXCEEDED', "Action: $action, IP: $ip");
            http_response_code(429);
            die('Rate limit exceeded');
        }
        
        $_SESSION[$key]['count']++;
        return true;
    }
    
    public static function sanitizeInput($data, $type = 'string') {
        if (is_array($data)) {
            return array_map(function($item) use ($type) {
                return self::sanitizeInput($item, $type);
            }, $data);
        }
        
        switch ($type) {
            case 'email':
                return filter_var(trim($data), FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            default:
                return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
    }
    
    public static function validateInput($data, $type, $options = []) {
        switch ($type) {
            case 'email':
                return filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
            case 'int':
                $min = $options['min'] ?? null;
                $max = $options['max'] ?? null;
                $filter_options = [];
                if ($min !== null) $filter_options['min_range'] = $min;
                if ($max !== null) $filter_options['max_range'] = $max;
                return filter_var($data, FILTER_VALIDATE_INT, ['options' => $filter_options]) !== false;
            case 'float':
                return filter_var($data, FILTER_VALIDATE_FLOAT) !== false;
            case 'string':
                $min_len = $options['min_length'] ?? 0;
                $max_len = $options['max_length'] ?? 1000;
                $len = strlen($data);
                return $len >= $min_len && $len <= $max_len;
            default:
                return true;
        }
    }
    
    public static function getClientIP() {
        $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    public static function logSecurityEvent($type, $details) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'ip' => self::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        $logFile = APP_ROOT . '/storage/logs/security-' . date('Y-m-d') . '.log';
        file_put_contents($logFile, json_encode($log_entry) . "\n", FILE_APPEND | LOCK_EX);
    }
}
?>