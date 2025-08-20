<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Student.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['user_name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $level = intval($_POST['level'] ?? 0);
    
    if (empty($userName) || empty($department) || $level <= 0) {
        $error = 'All fields are required.';
    } elseif (!in_array($level, [100, 200, 300, 400, 500, 600])) {
        $error = 'Invalid level. Please select a valid level.';
    } else {
        $studentClass = new Student();
        
        if ($studentClass->exists($userName)) {
            $error = 'Matric number already exists.';
        } else {
            try {
                $result = $studentClass->create([
                    'user_name' => $userName,
                    'department' => $department,
                    'level' => $level
                ]);
                
                if ($result) {
                    header('Location: index.php?message=Student added successfully');
                    exit;
                } else {
                    $error = 'Failed to add student. Please try again.';
                }
            } catch (Exception $e) {
                $error = 'An error occurred: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - <?php echo APP_NAME; ?></title>
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
                            <h2>Add New Student</h2>
                            <p class="text-muted mb-0">Register a new student in the system</p>
                        </div>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Students
                        </a>
                    </div>
                    
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-plus me-2"></i>Student Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <?php echo htmlspecialchars($error); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="">
                                        <div class="mb-3">
                                            <label for="user_name" class="form-label">
                                                <i class="fas fa-id-card me-2"></i>Matric Number
                                            </label>
                                            <input type="text" class="form-control" id="user_name" name="user_name" 
                                                   value="<?php echo htmlspecialchars($_POST['user_name'] ?? ''); ?>" 
                                                   placeholder="Enter matric number" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="department" class="form-label">
                                                <i class="fas fa-building me-2"></i>Department
                                            </label>
                                            <input type="text" class="form-control" id="department" name="department" 
                                                   value="<?php echo htmlspecialchars($_POST['department'] ?? ''); ?>" 
                                                   placeholder="Enter department" required>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label for="level" class="form-label">
                                                <i class="fas fa-graduation-cap me-2"></i>Level
                                            </label>
                                            <select class="form-select" id="level" name="level" required>
                                                <option value="">Select Level</option>
                                                <option value="100" <?php echo (($_POST['level'] ?? '') == '100') ? 'selected' : ''; ?>>100 Level</option>
                                                <option value="200" <?php echo (($_POST['level'] ?? '') == '200') ? 'selected' : ''; ?>>200 Level</option>
                                                <option value="300" <?php echo (($_POST['level'] ?? '') == '300') ? 'selected' : ''; ?>>300 Level</option>
                                                <option value="400" <?php echo (($_POST['level'] ?? '') == '400') ? 'selected' : ''; ?>>400 Level</option>
                                                <option value="500" <?php echo (($_POST['level'] ?? '') == '500') ? 'selected' : ''; ?>>500 Level</option>
                                                <option value="600" <?php echo (($_POST['level'] ?? '') == '600') ? 'selected' : ''; ?>>600 Level</option>
                                            </select>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="index.php" class="btn btn-secondary me-md-2">
                                                <i class="fas fa-times me-2"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Add Student
                                            </button>
                                        </div>
                                    </form>
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