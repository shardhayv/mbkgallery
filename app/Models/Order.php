<?php
class Order extends BaseModel {
    protected $table = 'orders';
    protected $fillable = [
        'customer_name', 'customer_email', 'customer_phone', 
        'customer_address', 'total_amount', 'status'
    ];
    
    public function createWithItems($orderData, $items) {
        $this->db->beginTransaction();
        
        try {
            $orderId = $this->create([
                'customer_name' => $orderData['customer_name'],
                'customer_email' => $orderData['customer_email'],
                'customer_phone' => $orderData['customer_phone'],
                'customer_address' => $orderData['customer_address'],
                'total_amount' => $orderData['total']
            ]);
            
            $paintingModel = new Painting();
            $totalCalculated = 0;
            
            foreach ($items as $paintingId) {
                $painting = $paintingModel->find($paintingId);
                if (!$painting || $painting['status'] !== 'available') {
                    throw new Exception("Painting ID $paintingId is not available");
                }
                
                $totalCalculated += $painting['price'];
                
                $this->db->query(
                    "INSERT INTO order_items (order_id, painting_id, price) VALUES (?, ?, ?)",
                    [$orderId, $paintingId, $painting['price']]
                );
                
                $paintingModel->markAsSold($paintingId);
            }
            
            if (abs($totalCalculated - $orderData['total']) > 0.01) {
                throw new Exception('Order total mismatch');
            }
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function getRecentOrders($limit = 10) {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    public function getOrdersWithDetails($status = 'all') {
        $sql = "SELECT o.*, COUNT(oi.id) as item_count 
                FROM {$this->table} o 
                LEFT JOIN order_items oi ON o.id = oi.order_id";
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " WHERE o.status = ?";
            $params[] = $status;
        }
        
        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getTotalRevenue() {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) as total FROM {$this->table} WHERE status IN ('confirmed', 'shipped', 'delivered')";
        $result = $this->db->fetchOne($sql);
        return $result['total'];
    }
    
    public function getMonthlyRevenue() {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) as total FROM {$this->table} 
                WHERE status IN ('confirmed', 'shipped', 'delivered') 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $result = $this->db->fetchOne($sql);
        return $result['total'];
    }
    
    public function getSalesSummary($days = 30) {
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as orders, COALESCE(SUM(total_amount), 0) as revenue 
                FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
                AND status IN ('confirmed', 'shipped', 'delivered')
                GROUP BY DATE(created_at) ORDER BY date DESC";
        return $this->db->fetchAll($sql, [$days]);
    }
    
    public function getRevenueTrend($days = 30) {
        $sql = "SELECT DATE(created_at) as date, COALESCE(SUM(total_amount), 0) as revenue 
                FROM {$this->table} 
                WHERE status IN ('confirmed', 'shipped', 'delivered') 
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
                GROUP BY DATE(created_at) ORDER BY date DESC";
        return $this->db->fetchAll($sql, [$days]);
    }
}
?>