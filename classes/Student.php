<?php
require_once __DIR__ . '/../config/config.php';

class Student {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function create($data) {
        $sql = "INSERT INTO user (user_name, department, level) VALUES (?, ?, ?)";
        return $this->db->execute($sql, [
            $data['user_name'],
            $data['department'],
            $data['level']
        ]);
    }
    
    public function getAll() {
        $sql = "SELECT user_id, user_name, department, level FROM user ORDER BY user_name";
        return $this->db->fetchAll($sql);
    }
    
    public function getById($id) {
        $sql = "SELECT user_id, user_name, department, level FROM user WHERE user_id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function delete($id) {
        // Delete fingerprint data first
        $this->db->execute("DELETE FROM finger WHERE user_id = ?", [$id]);
        
        // Delete student record
        $sql = "DELETE FROM user WHERE user_id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function exists($userName) {
        $sql = "SELECT COUNT(*) as count FROM user WHERE user_name = ?";
        $result = $this->db->fetchOne($sql, [$userName]);
        return $result['count'] > 0;
    }
    
    public function getFingerprints($userId) {
        $sql = "SELECT finger_id, finger_data FROM finger WHERE user_id = ?";
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    public function addFingerprint($userId, $fingerId, $fingerData) {
        $sql = "INSERT INTO finger (user_id, finger_id, finger_data) VALUES (?, ?, ?)";
        return $this->db->execute($sql, [$userId, $fingerId, $fingerData]);
    }
}
?>