<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Hospital Procurement System'; ?> - ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo $base_url ?? ''; ?>css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <!-- Mobile menu button -->
            <button class="btn btn-primary d-lg-none me-2" type="button" id="mobile-menu-button">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo $base_url ?? ''; ?>dashboard.php">
                <i class="fas fa-hospital me-2"></i>
                <div>
                    <div class="fw-semibold">ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</div>
                    <div class="small text-light opacity-75">โรงพยาบาล</div>
                </div>
            </a>
            
            <!-- User menu -->
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle d-flex align-items-center" type="button" id="user-menu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-2"></i>
                    <div class="text-start">
                        <div class="small">สวัสดี, <?php echo $_SESSION['user_name']; ?></div>
                        <div class="small opacity-75"><?php echo $_SESSION['user_role']; ?></div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="user-menu">
                    <li><a class="dropdown-item" href="<?php echo $base_url ?? ''; ?>profile.php">
                        <i class="fas fa-user me-2"></i>ข้อมูลส่วนตัว
                    </a></li>
                    <li><a class="dropdown-item" href="<?php echo $base_url ?? ''; ?>change_password.php">
                        <i class="fas fa-key me-2"></i>เปลี่ยนรหัสผ่าน
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo $base_url ?? ''; ?>logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                    </a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Mobile sidebar overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    
    <!-- Main container -->
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>
        
        <!-- Main content -->
        <div class="main-content flex-fill">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="bg-light border-bottom">
                <div class="container-fluid">
                    <ol class="breadcrumb mb-0 py-2">
                        <li class="breadcrumb-item">
                            <a href="<?php echo $base_url ?? ''; ?>dashboard.php" class="text-decoration-none">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo $page_title ?? 'หน้าหลัก'; ?>
                        </li>
                    </ol>
                </div>
            </nav>
            
            <!-- Page content -->
            <div class="container-fluid py-4">
                <?php
                // Display flash messages
                if (isset($_SESSION['success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                    echo '<i class="fas fa-check-circle me-2"></i>' . $_SESSION['success'];
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    echo '</div>';
                    unset($_SESSION['success']);
                }
                
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                    echo '<i class="fas fa-exclamation-triangle me-2"></i>' . $_SESSION['error'];
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    echo '</div>';
                    unset($_SESSION['error']);
                }
                
                if (isset($_SESSION['warning'])) {
                    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
                    echo '<i class="fas fa-exclamation-triangle me-2"></i>' . $_SESSION['warning'];
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    echo '</div>';
                    unset($_SESSION['warning']);
                }
                
                if (isset($_SESSION['info'])) {
                    echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                    echo '<i class="fas fa-info-circle me-2"></i>' . $_SESSION['info'];
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    echo '</div>';
                    unset($_SESSION['info']);
                }
                ?>
                
    <script src="<?php echo $base_url ?? ''; ?>js/main.js"></script>
    <!-- Page content starts here -->