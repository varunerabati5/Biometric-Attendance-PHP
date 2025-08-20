<?php
require_once __DIR__ . '/../config/config.php';

class User {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function authenticate($username, $password) {
        $sql = "SELECT id, username, password, fullname, email, department FROM users WHERE username = ?";
        $user = $this->db->fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    public function create($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_HASH_ALGO);
        
        $sql = "INSERT INTO users (fullname, username, email, department, password, trn_date) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        return $this->db->execute($sql, [
            $data['fullname'],
            $data['username'],
            $data['email'],
            $data['department'],
            $hashedPassword
        ]);
    }
    
    public function getAll() {
        $sql = "SELECT id, fullname, username, email, department, trn_date FROM users ORDER BY fullname";
        return $this->db->fetchAll($sql);
    }
    
    public function getById($id) {
        $sql = "SELECT id, fullname, username, email, department FROM users WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE users SET fullname = ?, username = ?, email = ?, department = ?";
        $params = [$data['fullname'], $data['username'], $data['email'], $data['department']];
        
        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_HASH_ALGO);
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function search($keyword) {
        $sql = "SELECT id, fullname, username, email, department FROM users 
                WHERE username LIKE ? OR fullname LIKE ? OR email LIKE ?";
        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
}
?>