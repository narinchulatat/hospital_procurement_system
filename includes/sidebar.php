<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0" id="sidebar">
    <div class="flex items-center justify-center h-16 bg-gray-50 border-b border-gray-200 lg:hidden">
        <span class="text-lg font-semibold text-gray-900">เมนูหลัก</span>
        <button class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="document.getElementById('sidebar-overlay').click()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="mt-5 px-2 space-y-1">
        <a href="<?php echo $base_url ?? ''; ?>dashboard.php" 
           class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
            <i class="fas fa-tachometer-alt <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
            Dashboard
        </a>
        
        <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
        <a href="<?php echo $base_url ?? ''; ?>request_form.php" 
           class="<?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
            <i class="fas fa-plus-circle <?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
            ยื่นคำขอใหม่
        </a>
        <?php endif; ?>
        
        <a href="<?php echo $base_url ?? ''; ?>requests.php" 
           class="<?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
            <i class="fas fa-list <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
            รายการคำขอ
        </a>
        
        <?php if ($_SESSION['user_role'] == 'department_head' || $_SESSION['user_role'] == 'admin'): ?>
        <a href="<?php echo $base_url ?? ''; ?>approve_request.php" 
           class="<?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
            <i class="fas fa-check-circle <?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
            อนุมัติคำขอ
        </a>
        <?php endif; ?>
        
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <div class="mt-8">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                จัดการระบบ
            </h3>
            <div class="mt-1 space-y-1">
                <a href="<?php echo $base_url ?? ''; ?>admin/users/" 
                   class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-users <?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
                    ผู้ใช้งาน
                </a>
                <a href="<?php echo $base_url ?? ''; ?>admin/departments/" 
                   class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-building <?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
                    แผนก
                </a>
                <a href="<?php echo $base_url ?? ''; ?>admin/items/" 
                   class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-boxes <?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
                    รายการครุภัณฑ์
                </a>
                <a href="<?php echo $base_url ?? ''; ?>admin/roles/" 
                   class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-user-tag <?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
                    บทบาท
                </a>
                <a href="<?php echo $base_url ?? ''; ?>admin/status/" 
                   class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-flag <?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
                    สถานะ
                </a>
                <a href="<?php echo $base_url ?? ''; ?>users.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-users-cog <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
                    จัดการผู้ใช้
                </a>
                <a href="<?php echo $base_url ?? ''; ?>reports.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-chart-bar <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
                    รายงาน
                </a>
                <a href="<?php echo $base_url ?? ''; ?>logs.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-history <?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
                    บันทึกกิจกรรม
                </a>
                <a href="<?php echo $base_url ?? ''; ?>test_system.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'test_system.php' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-cog <?php echo basename($_SERVER['PHP_SELF']) == 'test_system.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3"></i>
                    ทดสอบระบบ
                </a>
            </div>
        </div>
        <?php endif; ?>
    </nav>
</div>