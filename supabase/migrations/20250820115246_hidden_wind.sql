-- Biometric Attendance System Database Schema
-- Updated for modern PHP and security best practices

-- Create database
CREATE DATABASE IF NOT EXISTS bio_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bio_attendance;

-- Users table (for system administrators/lecturers)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'lecturer') DEFAULT 'lecturer',
    is_active BOOLEAN DEFAULT TRUE,
    trn_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Students table
CREATE TABLE IF NOT EXISTS user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(50) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    level INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_name (user_name),
    INDEX idx_department (department),
    INDEX idx_level (level)
);

-- Fingerprint data table
CREATE TABLE IF NOT EXISTS finger (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    finger_id INT NOT NULL,
    finger_data TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_finger (user_id, finger_id),
    INDEX idx_user_id (user_id)
);

-- Attendance log table
CREATE TABLE IF NOT EXISTS log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(50) NOT NULL,
    log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data TEXT NOT NULL,
    device_sn VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_name (user_name),
    INDEX idx_log_time (log_time),
    INDEX idx_device_sn (device_sn)
);

-- Device management table
CREATE TABLE IF NOT EXISTS device (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(100) NOT NULL,
    sn VARCHAR(50) NOT NULL UNIQUE,
    vc VARCHAR(50) NOT NULL,
    ac VARCHAR(50) NOT NULL,
    vkey VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sn (sn),
    INDEX idx_device_name (device_name)
);

-- Settings table for application configuration
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (fullname, username, email, department, password, role) VALUES 
('System Administrator', 'admin', 'admin@example.com', 'IT Department', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE username = username;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES 
('app_name', 'Biometric Attendance System', 'Application name'),
('time_limit_reg', '15', 'Time limit for fingerprint registration in seconds'),
('time_limit_ver', '10', 'Time limit for fingerprint verification in seconds'),
('session_timeout', '3600', 'Session timeout in seconds'),
('max_login_attempts', '5', 'Maximum login attempts before lockout')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- Create views for reporting
CREATE OR REPLACE VIEW attendance_summary AS
SELECT 
    u.user_name,
    u.department,
    u.level,
    DATE(l.log_time) as attendance_date,
    COUNT(*) as attendance_count,
    MIN(l.log_time) as first_attendance,
    MAX(l.log_time) as last_attendance
FROM user u
LEFT JOIN log l ON u.user_name = l.user_name
GROUP BY u.user_name, u.department, u.level, DATE(l.log_time);

-- Create stored procedure for daily attendance report
DELIMITER //
CREATE PROCEDURE GetDailyAttendance(IN report_date DATE)
BEGIN
    SELECT 
        u.user_name,
        u.department,
        u.level,
        CASE 
            WHEN l.user_name IS NOT NULL THEN 'Present'
            ELSE 'Absent'
        END as status,
        l.log_time as attendance_time
    FROM user u
    LEFT JOIN log l ON u.user_name = l.user_name AND DATE(l.log_time) = report_date
    ORDER BY u.department, u.level, u.user_name;
END //
DELIMITER ;