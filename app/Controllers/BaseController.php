<?php
abstract class BaseController {
    protected $db;
    
    public function __construct() {
        $this->db = DatabaseManager::getInstance();
    }
    
    protected function view($template, $data = []) {
        extract($data);
        ob_start();
        include APP_ROOT . "/app/Views/$template.php";
        $content = ob_get_clean();
        
        // Auto-inject CSRF tokens for admin views
        if (strpos($template, 'admin/') === 0) {
            $content = CSRFMiddleware::injectToken($content);
        }
        
        echo $content;
    }
    
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    protected function validateRequest($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[$field] = $rule['message'] ?? "$field is required";
                continue;
            }
            
            if (!empty($value) && isset($rule['type'])) {
                if (!SecurityManager::validateInput($value, $rule['type'], $rule)) {
                    $errors[$field] = $rule['message'] ?? "Invalid $field";
                }
            }
        }
        
        return $errors;
    }
    
    protected function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return SecurityManager::sanitizeInput($data);
    }
}
?>