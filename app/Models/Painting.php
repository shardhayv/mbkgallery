<?php
class Painting extends BaseModel {
    protected $table = 'paintings';
    protected $fillable = [
        'title', 'artist_id', 'category_id', 'description', 
        'price', 'dimensions', 'medium', 'image_path', 'status'
    ];
    
    public function getAvailable($limit = 50, $offset = 0) {
        $sql = "
            SELECT p.*, a.name as artist_name, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN artists a ON p.artist_id = a.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 'available' 
            ORDER BY p.created_at DESC 
            LIMIT ? OFFSET ?
        ";
        
        return $this->db->fetchAll($sql, [$limit, $offset]);
    }
    
    public function getByArtist($artistId) {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.artist_id = ? AND p.status = 'available'
            ORDER BY p.created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$artistId]);
    }
    
    public function getByIds($ids) {
        if (empty($ids)) return [];
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "
            SELECT p.*, a.name as artist_name 
            FROM {$this->table} p 
            LEFT JOIN artists a ON p.artist_id = a.id 
            WHERE p.id IN ($placeholders) AND p.status = 'available'
        ";
        
        return $this->db->fetchAll($sql, $ids);
    }
    
    public function getByCategory($categoryId) {
        $sql = "
            SELECT p.*, a.name as artist_name, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN artists a ON p.artist_id = a.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = ? AND p.status = 'available'
            ORDER BY p.created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$categoryId]);
    }
    
    public function markAsSold($id) {
        return $this->update($id, ['status' => 'sold']);
    }
    
    public function getLowStock($threshold = 5) {
        $sql = "SELECT a.name as artist_name, COUNT(p.id) as available_count
                FROM artists a
                LEFT JOIN {$this->table} p ON a.id = p.artist_id AND p.status = 'available'
                WHERE a.status = 'active'
                GROUP BY a.id, a.name
                HAVING available_count <= ?
                ORDER BY available_count";
        return $this->db->fetchAll($sql, [$threshold]);
    }
    
    public function getInventoryReport() {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count,
                    AVG(price) as avg_price,
                    SUM(price) as total_value
                FROM {$this->table}
                GROUP BY status";
        return $this->db->fetchAll($sql);
    }
}
?>