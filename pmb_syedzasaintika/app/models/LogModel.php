<?php
require_once '../core/Database.php';

class LogModel 
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function logActivity($user_id, $action, $description, $ip_address = null)
    {
        $this->db->query("INSERT INTO logs (user_id, action, description, ip_address, created_at) 
                         VALUES (:user_id, :action, :description, :ip_address, NOW())");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':action', $action);
        $this->db->bind(':description', $description);
        $this->db->bind(':ip_address', $ip_address ?? $_SERVER['REMOTE_ADDR']);
        
        return $this->db->execute();
    }

    public function getAllLogs($limit = 50, $offset = 0)
    {
        $this->db->query("SELECT l.*, u.username 
                         FROM logs l
                         LEFT JOIN users u ON l.user_id = u.id
                         ORDER BY l.created_at DESC
                         LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    public function getLogsByUser($user_id, $limit = 50, $offset = 0)
    {
        $this->db->query("SELECT l.*, u.username 
                         FROM logs l
                         LEFT JOIN users u ON l.user_id = u.id
                         WHERE l.user_id = :user_id
                         ORDER BY l.created_at DESC
                         LIMIT :limit OFFSET :offset");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    public function getLogsByAction($action, $limit = 50, $offset = 0)
    {
        $this->db->query("SELECT l.*, u.username 
                         FROM logs l
                         LEFT JOIN users u ON l.user_id = u.id
                         WHERE l.action = :action
                         ORDER BY l.created_at DESC
                         LIMIT :limit OFFSET :offset");
        $this->db->bind(':action', $action);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    public function getLogsByDateRange($start_date, $end_date, $limit = 50, $offset = 0)
    {
        $this->db->query("SELECT l.*, u.username 
                         FROM logs l
                         LEFT JOIN users u ON l.user_id = u.id
                         WHERE l.created_at BETWEEN :start_date AND :end_date
                         ORDER BY l.created_at DESC
                         LIMIT :limit OFFSET :offset");
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }
}