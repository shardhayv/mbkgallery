<?php
class ErrorHandler {
    private static $logPath = 'storage/logs/';
    
    public static function init() {
        if (!file_exists(APP_ROOT . '/' . self::$logPath)) {
            mkdir(APP_ROOT . '/' . self::$logPath, 0755, true);
        }
        
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatalError']);
        
        error_reporting(E_ALL);
        ini_set('display_errors', APP_DEBUG ? 1 : 0);
        ini_set('log_errors', 1);
    }
    
    public static function handleError($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) return false;
        
        $error = [
            'type' => 'ERROR',
            'severity' => $severity,
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        self::logError($error);
        
        if ($severity === E_ERROR || $severity === E_USER_ERROR) {
            self::showErrorPage('System Error', 'An unexpected error occurred.');
        }
        
        return true;
    }
    
    public static function handleException($exception) {
        $error = [
            'type' => 'EXCEPTION',
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        self::logError($error);
        self::showErrorPage('Application Error', 'Something went wrong.');
    }
    
    public static function handleFatalError() {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::logError([
                'type' => 'FATAL',
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            self::showErrorPage('System Error', 'A critical error occurred.');
        }
    }
    
    public static function logError($error) {
        $logFile = APP_ROOT . '/' . self::$logPath . 'error-' . date('Y-m-d') . '.log';
        $logEntry = json_encode($error) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    public static function showErrorPage($title, $message) {
        http_response_code(500);
        include APP_ROOT . '/app/Views/errors/500.php';
        exit;
    }
}
?>