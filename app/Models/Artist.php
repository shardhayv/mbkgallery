<?php
class Artist extends BaseModel {
    protected $table = 'artists';
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'bio', 'profile_image', 'status'
    ];
    
    public function getActiveWithPaintingCount() {
        $sql = "
            SELECT a.*, COUNT(p.id) as painting_count 
            FROM {$this->table} a 
            LEFT JOIN paintings p ON a.id = p.artist_id AND p.status = 'available'
            WHERE a.status = 'active' 
            GROUP BY a.id 
            ORDER BY a.name
        ";
        
        return $this->db->fetchAll($sql);
    }
    
    public function getTopArtists($limit = 5) {
        $sql = "SELECT a.*, COUNT(oi.id) as sales_count, SUM(oi.price) as total_revenue
                FROM {$this->table} a
                JOIN paintings p ON a.id = p.artist_id
                JOIN order_items oi ON p.id = oi.painting_id
                WHERE a.status = 'active'
                GROUP BY a.id
                ORDER BY total_revenue DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    public function getPerformanceReport($days = 30) {
        $sql = "SELECT a.name, COUNT(oi.id) as paintings_sold, COALESCE(SUM(oi.price), 0) as revenue
                FROM {$this->table} a
                JOIN paintings p ON a.id = p.artist_id
                JOIN order_items oi ON p.id = oi.painting_id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                AND o.status IN ('confirmed', 'shipped', 'delivered')
                GROUP BY a.id, a.name
                ORDER BY revenue DESC";
        return $this->db->fetchAll($sql, [$days]);
    }
}
?>