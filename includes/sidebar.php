<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0" id="sidebar">
    <!-- Mobile header -->
    <div class="flex items-center justify-between h-16 bg-gradient-to-r from-blue-600 to-blue-700 px-4 lg:hidden">
        <div class="flex items-center text-white">
            <i class="fas fa-hospital text-xl mr-2"></i>
            <span class="font-semibold">Hospital Procurement System</span>
        </div>
        <button class="text-white hover:text-gray-200 transition-colors" onclick="document.getElementById('sidebar-overlay').click()">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    <!-- Desktop logo -->
    <div class="hidden lg:flex items-center justify-center h-16 bg-gradient-to-r from-blue-600 to-blue-700 border-b border-blue-500">
        <div class="flex items-center text-white">
            <i class="fas fa-hospital text-2xl mr-3"></i>
            <div class="text-center">
                <div class="font-bold text-lg">Hospital</div>
                <div class="text-sm text-blue-200">Procurement System</div>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="mt-5 px-3 space-y-1 h-full overflow-y-auto">
        <!-- Dashboard -->
        <a href="<?php echo $base_url ?? ''; ?>dashboard.php" 
           class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
            <i class="fas fa-chart-line <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
            <span>📊 Dashboard</span>
        </a>
        
        <!-- ยื่นคำขอใหม่ - for staff and department_head -->
        <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
        <a href="<?php echo $base_url ?? ''; ?>request_form.php" 
           class="<?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
            <i class="fas fa-plus-circle <?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
            <span>🟢 ยื่นคำขอใหม่</span>
        </a>
        <?php endif; ?>
        
        <!-- รายการคำขอ -->
        <a href="<?php echo $base_url ?? ''; ?>requests.php" 
           class="<?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
            <i class="fas fa-list-alt <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
            <span>📋 รายการคำขอ</span>
        </a>
        
        <!-- อนุมัติคำขอ - for department_head and admin -->
        <?php if ($_SESSION['user_role'] == 'department_head' || $_SESSION['user_role'] == 'admin'): ?>
        <a href="<?php echo $base_url ?? ''; ?>approve_request.php" 
           class="<?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'bg-green-100 text-green-700 border-r-4 border-green-500' : 'text-gray-600 hover:bg-green-50 hover:text-green-700'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
            <i class="fas fa-check-circle <?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'text-green-500' : 'text-gray-400 group-hover:text-green-500'; ?> mr-3 w-5"></i>
            <span>✅ อนุมัติคำขอ</span>
        </a>
        <?php endif; ?>
        
        <!-- จัดการระบบ - for admin only -->
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <div class="pt-4 pb-6">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                📁 จัดการระบบ
            </h3>
            
            <a href="<?php echo $base_url ?? ''; ?>admin/users/" 
               class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-users <?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>👥 ผู้ใช้งาน</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>admin/departments/" 
               class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-building <?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>🏢 แผนก</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>admin/items/" 
               class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-laptop <?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>📦 รายการครุภัณฑ์</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>admin/roles/" 
               class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-user-tag <?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>🏷️ บทบาท</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>admin/status/" 
               class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-flag <?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>📊 สถานะ</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>admin/budget_years/" 
               class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/budget_years/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-calendar-alt <?php echo strpos($_SERVER['PHP_SELF'], '/admin/budget_years/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>💰 ปีงบประมาณ</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>admin/reports/" 
               class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-chart-bar <?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>📈 รายงาน</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
</div>