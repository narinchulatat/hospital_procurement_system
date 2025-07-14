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
        <!-- Sidebar -->
        <div class="sidebar bg-white shadow-sm" id="sidebar">
            <div class="p-3">
                <nav class="nav flex-column">
                    <a href="<?php echo $base_url ?? ''; ?>dashboard.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                    
                    <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
                    <a href="<?php echo $base_url ?? ''; ?>request_form.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                        <i class="fas fa-plus-circle me-2"></i>
                        ยื่นคำขอใหม่
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo $base_url ?? ''; ?>requests.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                        <i class="fas fa-list me-2"></i>
                        รายการคำขอ
                    </a>
                    
                    <?php if ($_SESSION['user_role'] == 'department_head' || $_SESSION['user_role'] == 'admin'): ?>
                    <a href="<?php echo $base_url ?? ''; ?>approvals.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'approvals.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                        <i class="fas fa-check-circle me-2"></i>
                        อนุมัติคำขอ
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['user_role'] == 'admin'): ?>
                    <div class="mt-3">
                        <div class="small text-muted text-uppercase fw-semibold mb-2">จัดการระบบ</div>
                        <a href="<?php echo $base_url ?? ''; ?>admin/users/" class="nav-link text-dark <?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                            <i class="fas fa-users me-2"></i>
                            ผู้ใช้งาน
                        </a>
                        <a href="<?php echo $base_url ?? ''; ?>admin/departments/" class="nav-link text-dark <?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                            <i class="fas fa-building me-2"></i>
                            แผนก
                        </a>
                        <a href="<?php echo $base_url ?? ''; ?>admin/items/" class="nav-link text-dark <?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                            <i class="fas fa-boxes me-2"></i>
                            รายการครุภัณฑ์
                        </a>
                        <a href="<?php echo $base_url ?? ''; ?>admin/procurement/" class="nav-link text-dark <?php echo strpos($_SERVER['PHP_SELF'], '/admin/procurement/') !== false ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                            <i class="fas fa-shopping-cart me-2"></i>
                            คำขอจัดซื้อ
                        </a>
                        <a href="<?php echo $base_url ?? ''; ?>admin/budget_years/" class="nav-link text-dark <?php echo strpos($_SERVER['PHP_SELF'], '/admin/budget_years/') !== false ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                            <i class="fas fa-calendar-alt me-2"></i>
                            ปีงบประมาณ
                        </a>
                        <a href="<?php echo $base_url ?? ''; ?>admin/roles/" class="nav-link text-dark <?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                            <i class="fas fa-user-tag me-2"></i>
                            บทบาท
                        </a>
                        <a href="<?php echo $base_url ?? ''; ?>admin/status/" class="nav-link text-dark <?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                            <i class="fas fa-flag me-2"></i>
                            สถานะ
                        </a>
                        <a href="<?php echo $base_url ?? ''; ?>reports.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                            <i class="fas fa-chart-bar me-2"></i>
                            รายงาน
                        </a>
                        <a href="<?php echo $base_url ?? ''; ?>logs.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                            <i class="fas fa-history me-2"></i>
                            บันทึกกิจกรรม
                        </a>
                    </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
        
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