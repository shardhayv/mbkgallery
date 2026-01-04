<?php
abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    public function __construct() {
        $this->db = DatabaseManager::getInstance();
    }
    
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function findById($id) {
        return $this->find($id);
    }
    
    public function findOne($conditions = []) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " LIMIT 1";
        
        return $this->db->fetchOne($sql, $params);
    }
    
    public function findAll($conditions = [], $limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function create($data) {
        $data = $this->filterFillable($data);
        $fields = array_keys($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ($placeholders)";
        $this->db->query($sql, array_values($data));
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $data = $this->filterFillable($data);
        $fields = array_keys($data);
        $set = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = ?";
        $params = array_merge(array_values($data), [$id]);
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
}
?>