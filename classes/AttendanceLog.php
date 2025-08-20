<?php
require_once __DIR__ . '/../config/config.php';

class AttendanceLog {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function create($userName, $time, $deviceSn) {
        $data = date('Y-m-d H:i:s', strtotime($time)) . " (PC Time) | " . $deviceSn . " (SN)";
        
        $sql = "INSERT INTO log (user_name, data, log_time) VALUES (?, ?, NOW())";
        return $this->db->execute($sql, [$userName, $data]);
    }
    
    public function getAll() {
        $sql = "SELECT log_time, user_name, data FROM log ORDER BY log_time DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getByDate($date) {
        $sql = "SELECT log_time, user_name, data FROM log 
                WHERE DATE(log_time) = ? ORDER BY log_time DESC";
        return $this->db->fetchAll($sql, [$date]);
    }
    
    public function getByUser($userName) {
        $sql = "SELECT log_time, user_name, data FROM log 
                WHERE user_name = ? ORDER BY log_time DESC";
        return $this->db->fetchAll($sql, [$userName]);
    }
}
?>