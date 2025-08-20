<?php
// Application Configuration
define('APP_NAME', 'Biometric Attendance System');
define('APP_VERSION', '2.0');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'bio_attendance');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Settings
define('BASE_URL', 'http://localhost/bio_attendance/');
define('ASSETS_URL', BASE_URL . 'assets/');

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);

// Fingerprint Settings
define('TIME_LIMIT_REG', 15);
define('TIME_LIMIT_VER', 10);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database class
require_once __DIR__ . '/database.php';

// Initialize database connection
$db = new Database();
?>