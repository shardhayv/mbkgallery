<?php
namespace API;

class PaintingController extends \BaseController {
    private $paintingModel;
    
    public function __construct() {
        parent::__construct();
        $this->paintingModel = new \Painting();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
    
    public function index() {
        try {
            SecurityManager::rateLimit('api_paintings', 200, 3600);
            
            $status = $_GET['status'] ?? 'available';
            $limit = min((int)($_GET['limit'] ?? 50), 100);
            $offset = max((int)($_GET['offset'] ?? 0), 0);
            
            if ($status === 'available') {
                $paintings = $this->paintingModel->getAvailable($limit, $offset);
            } else {
                $paintings = $this->paintingModel->findAll(['status' => $status], $limit, $offset);
            }
            
            $this->json([
                'success' => true,
                'data' => $paintings,
                'meta' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => count($paintings)
                ]
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Unable to fetch paintings'], 500);
        }
    }
}
?>