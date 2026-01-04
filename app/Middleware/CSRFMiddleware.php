<?php
class CSRFMiddleware {
    public static function protect() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRFProtection::validateRequest();
        }
    }
    
    public static function injectToken($content) {
        // Auto-inject CSRF tokens into forms
        return preg_replace_callback(
            '/<form[^>]*method=["\']post["\'][^>]*>/i',
            function($matches) {
                return $matches[0] . "\n" . CSRFProtection::getTokenField();
            },
            $content
        );
    }
}
?>