<?php
/**
 * Base Model Class
 * Semua model extends dari kelas ini
 */

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all records from table
     */
    public function all($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Find record by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Find record by custom column
     */
    public function findBy($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1");
        $stmt->execute(['value' => $value]);
        return $stmt->fetch();
    }
    
    /**
     * Get records by condition
     */
    public function where($conditions, $params = [], $orderBy = null) {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Insert new record
     */
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update record by ID
     */
    public function update($id, $data) {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = :{$column}";
        }
        $setString = implode(', ', $set);
        
        $sql = "UPDATE {$this->table} SET {$setString} WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    /**
     * Delete record by ID
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Count records
     */
    public function count($where = null, $params = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Execute custom query
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->db->rollBack();
    }
}
