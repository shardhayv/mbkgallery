<?php
namespace API;

class CategoryController extends \BaseController {
    private $db;
    
    public function __construct() {
        parent::__construct();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
    
    public function index() {
        try {
            SecurityManager::rateLimit('api_categories', 200, 3600);
            
            $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
            
            $this->json([
                'success' => true,
                'data' => $categories
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Unable to fetch categories'], 500);
        }
    }
}
?>