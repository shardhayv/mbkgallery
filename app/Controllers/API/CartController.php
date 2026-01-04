<?php
namespace API;

class CartController extends \BaseController {
    private $paintingModel;
    
    public function __construct() {
        parent::__construct();
        $this->paintingModel = new \Painting();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
    
    public function items() {
        try {
            SecurityManager::rateLimit('api_cart', 200, 3600);
            
            $input = json_decode(file_get_contents('php://input'), true);
            $ids = array_filter($input['ids'] ?? [], function($id) {
                return SecurityManager::validateInput($id, 'int', ['min' => 1, 'max' => 999999]);
            });
            
            if (empty($ids)) {
                $this->json(['success' => true, 'data' => []]);
                return;
            }
            
            $items = $this->paintingModel->getByIds($ids);
            
            $this->json([
                'success' => true,
                'data' => $items
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Unable to fetch cart items'], 500);
        }
    }
}
?>