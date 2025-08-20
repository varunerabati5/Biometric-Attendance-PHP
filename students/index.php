<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Student.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$studentClass = new Student();
$students = $studentClass->getAll();

$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - <?php echo APP_NAME; ?></title>
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
                        <a class="nav-link" href="../dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-users me-2"></i>Students
                        </a>
                        <a class="nav-link" href="../attendance/index.php">
                            <i class="fas fa-clock me-2"></i>Attendance
                        </a>
                        <a class="nav-link" href="../reports/index.php">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                        <a class="nav-link" href="../settings/index.php">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                        <hr class="text-white">
                        <a class="nav-link" href="../auth/logout.php">
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
                            <h2>Students Management</h2>
                            <p class="text-muted mb-0">Manage student records and fingerprint enrollment</p>
                        </div>
                        <a href="add.php" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>Add Student
                        </a>
                    </div>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Students Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>All Students
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($students)): ?>
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-users fa-4x mb-3"></i>
                                    <h4>No Students Found</h4>
                                    <p>Start by adding your first student to the system.</p>
                                    <a href="add.php" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-2"></i>Add First Student
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Matric Number</th>
                                                <th>Department</th>
                                                <th>Level</th>
                                                <th>Fingerprint</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($students as $student): ?>
                                                <?php 
                                                $fingerprints = $studentClass->getFingerprints($student['user_id']);
                                                $hasFingerprint = count($fingerprints) > 0;
                                                ?>
                                                <tr>
                                                    <td><?php echo $student['user_id']; ?></td>
                                                    <td>
                                                        <i class="fas fa-user me-2"></i>
                                                        <?php echo htmlspecialchars($student['user_name']); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($student['department']); ?></td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo $student['level']; ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($hasFingerprint): ?>
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-fingerprint me-1"></i>Enrolled
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>Not Enrolled
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <?php if (!$hasFingerprint): ?>
                                                                <a href="enroll.php?id=<?php echo $student['user_id']; ?>" 
                                                                   class="btn btn-success" title="Enroll Fingerprint">
                                                                    <i class="fas fa-fingerprint"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="verify.php?id=<?php echo $student['user_id']; ?>" 
                                                                   class="btn btn-primary" title="Verify Fingerprint">
                                                                    <i class="fas fa-check"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            <a href="edit.php?id=<?php echo $student['user_id']; ?>" 
                                                               class="btn btn-warning" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="delete.php?id=<?php echo $student['user_id']; ?>" 
                                                               class="btn btn-danger" title="Delete"
                                                               onclick="return confirm('Are you sure you want to delete this student?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
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
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>