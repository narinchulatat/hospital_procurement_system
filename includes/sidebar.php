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
            <a href="<?php echo $base_url ?? ''; ?>approve_request.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
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
                <a href="<?php echo $base_url ?? ''; ?>admin/roles/" class="nav-link text-dark <?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                    <i class="fas fa-user-tag me-2"></i>
                    บทบาท
                </a>
                <a href="<?php echo $base_url ?? ''; ?>admin/status/" class="nav-link text-dark <?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                    <i class="fas fa-flag me-2"></i>
                    สถานะ
                </a>
                <a href="<?php echo $base_url ?? ''; ?>users.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                    <i class="fas fa-users-cog me-2"></i>
                    จัดการผู้ใช้
                </a>
                <a href="<?php echo $base_url ?? ''; ?>reports.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                    <i class="fas fa-chart-bar me-2"></i>
                    รายงาน
                </a>
                <a href="<?php echo $base_url ?? ''; ?>logs.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                    <i class="fas fa-history me-2"></i>
                    บันทึกกิจกรรม
                </a>
                <a href="<?php echo $base_url ?? ''; ?>test_system.php" class="nav-link text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'test_system.php' ? 'active bg-primary bg-opacity-10 fw-semibold' : ''; ?>">
                    <i class="fas fa-cog me-2"></i>
                    ทดสอบระบบ
                </a>
            </div>
            <?php endif; ?>
        </nav>
    </div>
</div>