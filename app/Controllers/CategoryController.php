<?php
class CategoryController extends BaseController {
    private $categoryModel;
    private $paintingModel;
    
    public function __construct() {
        parent::__construct();
        $this->categoryModel = new Category();
        $this->paintingModel = new Painting();
    }
    
    public function paintings($categoryId) {
        try {
            $category = $this->categoryModel->find($categoryId);
            if (!$category) {
                $this->view('errors/404');
                return;
            }
            
            $filters = [
                'min_price' => $_GET['min_price'] ?? '',
                'max_price' => $_GET['max_price'] ?? '',
                'sort' => $_GET['sort'] ?? 'created_at',
                'order' => $_GET['order'] ?? 'DESC'
            ];
            
            $paintings = $this->getFilteredCategoryPaintings($categoryId, $filters);
            $priceRange = $this->getCategoryPriceRange($categoryId);
            
            $this->view('category/paintings', [
                'category' => $category,
                'paintings' => $paintings,
                'filters' => $filters,
                'priceRange' => $priceRange
            ]);
        } catch (Exception $e) {
            ErrorHandler::logError([
                'type' => 'CONTROLLER_ERROR',
                'message' => $e->getMessage(),
                'controller' => 'CategoryController',
                'action' => 'paintings'
            ]);
            $this->view('errors/500');
        }
    }
    
    private function getFilteredCategoryPaintings($categoryId, $filters) {
        $sql = "SELECT p.*, a.name as artist_name, c.name as category_name 
                FROM paintings p 
                LEFT JOIN artists a ON p.artist_id = a.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND p.status = 'available'";
        
        $params = [$categoryId];
        
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
        $sql .= " ORDER BY p.$sortBy $sortOrder";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    private function getCategoryPriceRange($categoryId) {
        $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM paintings WHERE category_id = ? AND status = 'available'";
        return $this->db->fetchOne($sql, [$categoryId]);
    }
}
?>