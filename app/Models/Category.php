<?php
class Category extends BaseModel {
    protected $table = 'categories';
    protected $fillable = ['name', 'description'];
    
    public function getAllCategories() {
        return $this->findAll();
    }
    
    public function getCategoryAnalysis($days = 30) {
        $sql = "SELECT c.name, COUNT(oi.id) as sales_count, COALESCE(SUM(oi.price), 0) as revenue
                FROM {$this->table} c
                JOIN paintings p ON c.id = p.category_id
                JOIN order_items oi ON p.id = oi.painting_id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                AND o.status IN ('confirmed', 'shipped', 'delivered')
                GROUP BY c.id, c.name
                ORDER BY revenue DESC";
        return $this->db->fetchAll($sql, [$days]);
    }
}
?>