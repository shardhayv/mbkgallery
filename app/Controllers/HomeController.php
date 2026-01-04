<?php
class HomeController extends BaseController {
    private $paintingModel;
    
    public function __construct() {
        parent::__construct();
        $this->paintingModel = new Painting();
    }
    
    public function index() {
        try {
            $filters = [
                'min_price' => $_GET['min_price'] ?? '',
                'max_price' => $_GET['max_price'] ?? '',
                'sort' => $_GET['sort'] ?? 'created_at',
                'order' => $_GET['order'] ?? 'DESC'
            ];
            
            $paintings = $this->getFilteredPaintings($filters);
            $priceRange = $this->getPriceRange();
            
            $this->view('home/index', [
                'paintings' => $paintings,
                'filters' => $filters,
                'priceRange' => $priceRange
            ]);
        } catch (Exception $e) {
            ErrorHandler::logError([
                'type' => 'CONTROLLER_ERROR',
                'message' => $e->getMessage(),
                'controller' => 'HomeController',
                'action' => 'index'
            ]);
            $this->view('errors/500');
        }
    }
    
    private function getFilteredPaintings($filters) {
        $sql = "SELECT p.*, a.name as artist_name, c.name as category_name 
                FROM paintings p 
                LEFT JOIN artists a ON p.artist_id = a.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'available'";
        
        $params = [];
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        $sortBy = in_array($filters['sort'], ['title', 'price', 'created_at']) ? $filters['sort'] : 'created_at';
        $sortOrder = $filters['order'] === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY p.$sortBy $sortOrder LIMIT 50";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    private function getPriceRange() {
        $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM paintings WHERE status = 'available'";
        return $this->db->fetchOne($sql);
    }
}
?>