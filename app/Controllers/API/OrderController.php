<?php
namespace API;

class OrderController extends \BaseController {
    private $orderModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new \Order();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
    
    public function create() {
        try {
            SecurityManager::rateLimit('api_orders', 20, 3600);
            
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                throw new Exception('Invalid JSON data');
            }
            
            $orderData = [
                'customer_name' => SecurityManager::sanitizeInput($input['customer_name'] ?? ''),
                'customer_email' => SecurityManager::sanitizeInput($input['customer_email'] ?? '', 'email'),
                'customer_phone' => SecurityManager::sanitizeInput($input['customer_phone'] ?? ''),
                'customer_address' => SecurityManager::sanitizeInput($input['customer_address'] ?? ''),
                'total' => SecurityManager::sanitizeInput($input['total'] ?? 0, 'float'),
            ];
            
            $items = $input['items'] ?? [];
            
            // Validate
            if (!SecurityManager::validateInput($orderData['customer_email'], 'email')) {
                throw new Exception('Invalid email address');
            }
            
            if (!is_array($items) || empty($items)) {
                throw new Exception('No items in order');
            }
            
            $orderId = $this->orderModel->createWithItems($orderData, $items);
            
            $this->json([
                'success' => true,
                'data' => ['order_id' => $orderId]
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Unable to process order'
            ], 500);
        }
    }
}
?>