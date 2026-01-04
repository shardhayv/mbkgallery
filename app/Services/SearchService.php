<?php
class SearchService {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseManager::getInstance();
    }
    
    public function searchPaintings($query = '', $filters = []) {
        $sql = "SELECT p.*, a.name as artist_name, c.name as category_name 
                FROM paintings p 
                LEFT JOIN artists a ON p.artist_id = a.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'available'";
        
        $params = [];
        
        // Search query
        if (!empty($query)) {
            $sql .= " AND (p.title LIKE ? OR a.name LIKE ? OR c.name LIKE ?)";
            $searchTerm = "%$query%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Category filter
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category'];
        }
        
        // Artist filter
        if (!empty($filters['artist'])) {
            $sql .= " AND p.artist_id = ?";
            $params[] = $filters['artist'];
        }
        
        // Price range filter
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        // Sorting
        $sortBy = $filters['sort'] ?? 'created_at';
        $sortOrder = $filters['order'] ?? 'DESC';
        
        $allowedSorts = ['title', 'price', 'created_at', 'artist_name'];
        if (in_array($sortBy, $allowedSorts)) {
            $sql .= " ORDER BY $sortBy $sortOrder";
        } else {
            $sql .= " ORDER BY p.created_at DESC";
        }
        
        // Pagination
        $limit = $filters['limit'] ?? 12;
        $offset = ($filters['page'] ?? 1 - 1) * $limit;
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getSearchFilters() {
        return [
            'categories' => $this->db->fetchAll("SELECT id, name FROM categories ORDER BY name"),
            'artists' => $this->db->fetchAll("SELECT id, name FROM artists WHERE status = 'active' ORDER BY name"),
            'price_range' => $this->db->fetchOne("SELECT MIN(price) as min_price, MAX(price) as max_price FROM paintings WHERE status = 'available'")
        ];
    }
    
    public function getSearchCount($query = '', $filters = []) {
        $sql = "SELECT COUNT(*) as total 
                FROM paintings p 
                LEFT JOIN artists a ON p.artist_id = a.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'available'";
        
        $params = [];
        
        if (!empty($query)) {
            $sql .= " AND (p.title LIKE ? OR a.name LIKE ? OR c.name LIKE ?)";
            $searchTerm = "%$query%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['artist'])) {
            $sql .= " AND p.artist_id = ?";
            $params[] = $filters['artist'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }
}
?>