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
        
        <!-- การจัดซื้อ Section -->
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                การจัดซื้อ
            </h3>
            
            <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
            <a href="<?php echo $base_url ?? ''; ?>request_form.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-plus-circle <?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>รายการขอซื้อ</span>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo $base_url ?? ''; ?>requests.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-file-invoice <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>ใบสั่งซื้อ</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>purchase_history.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'purchase_history.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-history <?php echo basename($_SERVER['PHP_SELF']) == 'purchase_history.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>ประวัติการสั่งซื้อ</span>
            </a>
        </div>
        
        <!-- คลังสินค้า Section -->
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                คลังสินค้า
            </h3>
            
            <a href="<?php echo $base_url ?? ''; ?>inventory.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-boxes <?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>สินค้าคงคลัง</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>receive_goods.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'receive_goods.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-truck <?php echo basename($_SERVER['PHP_SELF']) == 'receive_goods.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>รับเข้าสินค้า</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>distribute_goods.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'distribute_goods.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-dolly <?php echo basename($_SERVER['PHP_SELF']) == 'distribute_goods.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>เบิกจ่ายสินค้า</span>
            </a>
        </div>
        
        <!-- รายงาน Section -->
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                รายงาน
            </h3>
            
            <a href="<?php echo $base_url ?? ''; ?>reports.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-chart-bar <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>รายงานการซื้อ</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>stock_reports.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'stock_reports.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-warehouse <?php echo basename($_SERVER['PHP_SELF']) == 'stock_reports.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>รายงานสต็อก</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>financial_reports.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'financial_reports.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-dollar-sign <?php echo basename($_SERVER['PHP_SELF']) == 'financial_reports.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>รายงานการเงิน</span>
            </a>
        </div>
        
        <!-- ผู้ใช้งาน Section -->
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                ผู้ใช้งาน
            </h3>
            
            <a href="<?php echo $base_url ?? ''; ?>users.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-users <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>จัดการผู้ใช้</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>user_permissions.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'user_permissions.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-user-shield <?php echo basename($_SERVER['PHP_SELF']) == 'user_permissions.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>สิทธิ์การใช้งาน</span>
            </a>
        </div>
        <?php endif; ?>
        
        <!-- ตั้งค่า Section -->
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                ตั้งค่า
            </h3>
            
            <a href="<?php echo $base_url ?? ''; ?>system_settings.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'system_settings.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-cogs <?php echo basename($_SERVER['PHP_SELF']) == 'system_settings.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>ตั้งค่าระบบ</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>hospital_info.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'hospital_info.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-hospital <?php echo basename($_SERVER['PHP_SELF']) == 'hospital_info.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>ข้อมูลโรงพยาบาล</span>
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Approval Section for Department Head and Admin -->
        <?php if ($_SESSION['user_role'] == 'department_head' || $_SESSION['user_role'] == 'admin'): ?>
        <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                อนุมัติ
            </h3>
            
            <a href="<?php echo $base_url ?? ''; ?>approve_request.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'bg-green-100 text-green-700 border-r-4 border-green-500' : 'text-gray-600 hover:bg-green-50 hover:text-green-700'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-check-circle <?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'text-green-500' : 'text-gray-400 group-hover:text-green-500'; ?> mr-3 w-5"></i>
                <span>อนุมัติคำขอ</span>
            </a>
        </div>
        <?php endif; ?>
        
        <!-- System Management for Admin -->
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <div class="pt-4 pb-6">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                จัดการระบบ
            </h3>
            
            <a href="<?php echo $base_url ?? ''; ?>admin/departments/" 
               class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-building <?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>แผนก</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>admin/items/" 
               class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-laptop <?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>รายการครุภัณฑ์</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>logs.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-history <?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>บันทึกกิจกรรม</span>
            </a>
            
            <a href="<?php echo $base_url ?? ''; ?>test_system.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'test_system.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                <i class="fas fa-tools <?php echo basename($_SERVER['PHP_SELF']) == 'test_system.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                <span>ทดสอบระบบ</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
</div>