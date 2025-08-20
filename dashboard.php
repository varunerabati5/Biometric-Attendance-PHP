<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/Student.php';
require_once __DIR__ . '/classes/AttendanceLog.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$studentClass = new Student();
$logClass = new AttendanceLog();

// Get statistics
$students = $studentClass->getAll();
$recentLogs = $logClass->getAll();
$todayLogs = $logClass->getByDate(date('Y-m-d'));

$totalStudents = count($students);
$todayAttendance = count($todayLogs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4">
                    <div class="text-center text-white mb-4">
                        <i class="fas fa-fingerprint fa-3x mb-2"></i>
                        <h5><?php echo APP_NAME; ?></h5>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="students/index.php">
                            <i class="fas fa-users me-2"></i>Students
                        </a>
                        <a class="nav-link" href="attendance/index.php">
                            <i class="fas fa-clock me-2"></i>Attendance
                        </a>
                        <a class="nav-link" href="reports/index.php">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                        <a class="nav-link" href="settings/index.php">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                        <hr class="text-white">
                        <a class="nav-link" href="auth/logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-0">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2>Dashboard</h2>
                            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</p>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-calendar me-2"></i>
                            <?php echo date('F j, Y'); ?>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-3"></i>
                                    <h3><?php echo $totalStudents; ?></h3>
                                    <p class="mb-0">Total Students</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-3"></i>
                                    <h3><?php echo $todayAttendance; ?></h3>
                                    <p class="mb-0">Today's Attendance</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-percentage fa-2x mb-3"></i>
                                    <h3><?php echo $totalStudents > 0 ? round(($todayAttendance / $totalStudents) * 100) : 0; ?>%</h3>
                                    <p class="mb-0">Attendance Rate</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-fingerprint fa-2x mb-3"></i>
                                    <h3><?php echo count(array_filter($students, function($s) { 
                                        $studentClass = new Student();
                                        return count($studentClass->getFingerprints($s['user_id'])) > 0;
                                    })); ?></h3>
                                    <p class="mb-0">Enrolled</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-history me-2"></i>Recent Attendance
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recentLogs)): ?>
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-clock fa-3x mb-3"></i>
                                            <p>No attendance records yet</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Student</th>
                                                        <th>Time</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach (array_slice($recentLogs, 0, 10) as $log): ?>
                                                        <tr>
                                                            <td>
                                                                <i class="fas fa-user me-2"></i>
                                                                <?php echo htmlspecialchars($log['user_name']); ?>
                                                            </td>
                                                            <td>
                                                                <i class="fas fa-clock me-2"></i>
                                                                <?php echo date('M j, Y g:i A', strtotime($log['log_time'])); ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-success">Present</span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="students/add.php" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-2"></i>Add Student
                                        </a>
                                        <a href="attendance/mark.php" class="btn btn-success">
                                            <i class="fas fa-fingerprint me-2"></i>Mark Attendance
                                        </a>
                                        <a href="reports/daily.php" class="btn btn-info">
                                            <i class="fas fa-file-alt me-2"></i>Daily Report
                                        </a>
                                        <a href="settings/devices.php" class="btn btn-warning">
                                            <i class="fas fa-cog me-2"></i>Device Settings
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>