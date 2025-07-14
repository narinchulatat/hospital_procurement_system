<?php
// Start output buffering to capture content
ob_start();

// This function will be called at the end to insert the content
function layout_content() {
    return ob_get_clean();
}

// Check if this is the start of the layout (prevent double inclusion)
if (!defined('LAYOUT_STARTED')) {
    define('LAYOUT_STARTED', true);
    
    // At the end of the file, we'll output the complete HTML
    register_shutdown_function('output_layout');
}

function output_layout() {
    $content = layout_content();
    
    // Get the current page title and base URL
    global $page_title, $base_url;
    $page_title = $page_title ?? 'Hospital Procurement System';
    $base_url = $base_url ?? '';
    
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</title>
    <link rel="icon" type="image/x-icon" href="<?php echo $base_url; ?>favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-mobile {
            transform: translateX(-100%);
        }
        
        .sidebar-mobile.active {
            transform: translateX(0);
        }
        
        /* Modern sidebar enhancements */
        .sidebar-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .sidebar-nav-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-nav-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transform: scaleY(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0 4px 4px 0;
        }
        
        .sidebar-nav-item:hover::before,
        .sidebar-nav-item.active::before {
            transform: scaleY(1);
        }
        
        .sidebar-nav-icon {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-nav-item:hover .sidebar-nav-icon {
            transform: scale(1.2);
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }
        
        .sidebar-group {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .sidebar-group::after {
            content: '';
            position: absolute;
            bottom: -1rem;
            left: 1rem;
            right: 1rem;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(156, 163, 175, 0.3), transparent);
        }
        
        .sidebar-user-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.1) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .nav-badge {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            animation: pulse 2s infinite;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }
        
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3); }
            50% { transform: scale(1.05); box-shadow: 0 4px 16px rgba(255, 107, 107, 0.4); }
            100% { transform: scale(1); box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3); }
        }
        
        .sidebar-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: shimmer 4s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .mobile-menu-animation {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .nav-text {
            font-weight: 500;
            font-size: 0.9rem;
            letter-spacing: 0.025em;
        }
        
        .collapsible-section {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .collapsible-section.open {
            max-height: 600px;
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .user-avatar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        }
        
        .status-indicator {
            position: relative;
            animation: statusPulse 3s infinite;
        }
        
        @keyframes statusPulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
        
        .sidebar-nav-item.active {
            background: linear-gradient(135deg, rgba(103, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-right: 4px solid transparent;
            border-image: linear-gradient(135deg, #667eea, #764ba2) 1;
        }
        
        .section-title {
            position: relative;
            padding-left: 1rem;
        }
        
        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        
        .nav-item-glow {
            position: relative;
            overflow: hidden;
        }
        
        .nav-item-glow::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .nav-item-glow:hover::after {
            left: 100%;
        }
    </style>
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Mobile menu button -->
                <button class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" 
                        type="button" id="mobile-menu-button">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Brand -->
                <a href="<?php echo $base_url; ?>dashboard.php" class="flex items-center text-white hover:text-blue-200 transition-colors">
                    <i class="fas fa-hospital mr-3 text-xl"></i>
                    <div>
                        <div class="font-semibold text-lg">ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</div>
                        <div class="text-sm text-blue-200">โรงพยาบาล</div>
                    </div>
                </a>
                
                <!-- User menu -->
                <div class="relative">
                    <button class="flex items-center text-white hover:bg-blue-700 px-3 py-2 rounded-md transition-colors" 
                            type="button" id="user-menu" onclick="toggleUserMenu()">
                        <i class="fas fa-user-circle mr-2 text-lg"></i>
                        <div class="text-left">
                            <div class="text-sm">สวัสดี, <?php echo $_SESSION['user_name']; ?></div>
                            <div class="text-xs text-blue-200"><?php echo $_SESSION['user_role']; ?></div>
                        </div>
                        <i class="fas fa-chevron-down ml-2"></i>
                    </button>
                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="<?php echo $base_url; ?>profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>ข้อมูลส่วนตัว
                        </a>
                        <a href="<?php echo $base_url; ?>change_password.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-key mr-2"></i>เปลี่ยนรหัสผ่าน
                        </a>
                        <hr class="border-gray-200 my-1">
                        <a href="<?php echo $base_url; ?>logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>ออกจากระบบ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Mobile sidebar overlay -->
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-40 hidden" id="sidebar-overlay"></div>
    
    <!-- Main container -->
    <div class="flex">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0" id="sidebar">
            <!-- Mobile header -->
            <div class="flex items-center justify-between h-16 sidebar-header px-4 lg:hidden">
                <div class="flex items-center text-white relative z-10">
                    <div class="p-2 bg-white bg-opacity-20 rounded-lg mr-3">
                        <i class="fas fa-hospital text-xl"></i>
                    </div>
                    <div>
                        <div class="font-bold text-lg">Hospital System</div>
                        <div class="text-sm text-blue-100">Procurement</div>
                    </div>
                </div>
                <button class="text-white hover:text-gray-200 transition-colors relative z-10" onclick="document.getElementById('sidebar-overlay').click()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Desktop header -->
            <div class="hidden lg:block sidebar-header">
                <div class="flex items-center justify-center h-20 px-4 relative z-10">
                    <div class="flex items-center text-white">
                        <div class="p-3 bg-white bg-opacity-20 rounded-xl mr-3 shadow-lg">
                            <i class="fas fa-hospital text-2xl"></i>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-xl">Hospital</div>
                            <div class="text-sm text-blue-100 font-medium">Procurement System</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Profile Card -->
            <div class="px-4 py-6">
                <div class="sidebar-user-card rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <div class="user-avatar w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="font-semibold text-gray-800 text-sm"><?php echo $_SESSION['user_name']; ?></div>
                            <div class="text-xs text-gray-500 capitalize"><?php echo $_SESSION['user_role']; ?></div>
                        </div>
                        <div class="status-indicator w-3 h-3 bg-green-400 rounded-full shadow-sm"></div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="px-3 space-y-2 h-full overflow-y-auto pb-6">
                <!-- Main Navigation Group -->
                <div class="sidebar-group">
                    <div class="px-3 mb-4">
                        <span class="section-title text-xs font-semibold text-gray-400 uppercase tracking-wider">หลัก</span>
                    </div>
                    
                    <!-- Dashboard -->
                    <a href="<?php echo $base_url; ?>dashboard.php" 
                       class="sidebar-nav-item nav-item-glow <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                        <div class="sidebar-nav-icon <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    
                    <!-- ยื่นคำขอใหม่ - for staff and department_head -->
                    <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
                    <a href="<?php echo $base_url; ?>request_form.php" 
                       class="sidebar-nav-item nav-item-glow <?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'active bg-gradient-to-r from-green-50 to-blue-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                        <div class="sidebar-nav-icon <?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'text-green-500' : 'text-gray-400 group-hover:text-green-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <span class="nav-text">ยื่นคำขอใหม่</span>
                        <span class="nav-badge ml-auto px-2 py-1 text-xs text-white rounded-full shadow-sm">New</span>
                    </a>
                    <?php endif; ?>
                    
                    <!-- รายการคำขอ -->
                    <a href="<?php echo $base_url; ?>requests.php" 
                       class="sidebar-nav-item nav-item-glow <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'active bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                        <div class="sidebar-nav-icon <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <span class="nav-text">รายการคำขอ</span>
                    </a>
                    
                    <!-- อนุมัติคำขอ - for department_head and admin -->
                    <?php if ($_SESSION['user_role'] == 'department_head' || $_SESSION['user_role'] == 'admin'): ?>
                    <a href="<?php echo $base_url; ?>approve_request.php" 
                       class="sidebar-nav-item nav-item-glow <?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'active bg-gradient-to-r from-green-50 to-emerald-50 text-green-700' : 'text-gray-600 hover:bg-green-50 hover:text-green-700'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                        <div class="sidebar-nav-icon <?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'text-green-500' : 'text-gray-400 group-hover:text-green-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span class="nav-text">อนุมัติคำขอ</span>
                        <span class="nav-badge ml-auto px-2 py-1 text-xs text-white rounded-full shadow-sm">3</span>
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- จัดการระบบ - for admin only -->
                <?php if ($_SESSION['user_role'] == 'admin'): ?>
                <div class="sidebar-group">
                    <div class="px-3 mb-4">
                        <button class="flex items-center justify-between w-full text-xs font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors section-title" 
                                onclick="toggleAdminSection()">
                            <span>จัดการระบบ</span>
                            <i class="fas fa-chevron-down transition-transform duration-300" id="admin-chevron"></i>
                        </button>
                    </div>
                    
                    <div class="collapsible-section open" id="admin-section">
                        <a href="<?php echo $base_url; ?>admin/users/" 
                           class="sidebar-nav-item nav-item-glow <?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'active bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                            <div class="sidebar-nav-icon <?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="nav-text">ผู้ใช้งาน</span>
                        </a>
                        
                        <a href="<?php echo $base_url; ?>admin/departments/" 
                           class="sidebar-nav-item nav-item-glow <?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'active bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                            <div class="sidebar-nav-icon <?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-building"></i>
                            </div>
                            <span class="nav-text">แผนก</span>
                        </a>
                        
                        <a href="<?php echo $base_url; ?>admin/items/" 
                           class="sidebar-nav-item nav-item-glow <?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'active bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                            <div class="sidebar-nav-icon <?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-laptop"></i>
                            </div>
                            <span class="nav-text">รายการครุภัณฑ์</span>
                        </a>
                        
                        <a href="<?php echo $base_url; ?>admin/roles/" 
                           class="sidebar-nav-item nav-item-glow <?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'active bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                            <div class="sidebar-nav-icon <?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-user-tag"></i>
                            </div>
                            <span class="nav-text">บทบาท</span>
                        </a>
                        
                        <a href="<?php echo $base_url; ?>admin/status/" 
                           class="sidebar-nav-item nav-item-glow <?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'active bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                            <div class="sidebar-nav-icon <?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-flag"></i>
                            </div>
                            <span class="nav-text">สถานะ</span>
                        </a>
                        
                        <a href="<?php echo $base_url; ?>admin/budget_years/" 
                           class="sidebar-nav-item nav-item-glow <?php echo strpos($_SERVER['PHP_SELF'], '/admin/budget_years/') !== false ? 'active bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                            <div class="sidebar-nav-icon <?php echo strpos($_SERVER['PHP_SELF'], '/admin/budget_years/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span class="nav-text">ปีงบประมาณ</span>
                        </a>
                        
                        <a href="<?php echo $base_url; ?>admin/reports/" 
                           class="sidebar-nav-item nav-item-glow <?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports/') !== false ? 'active bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-300">
                            <div class="sidebar-nav-icon <?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <span class="nav-text">รายงาน</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </nav>
        </div>
        
        <!-- Main content -->
        <div class="flex-1 lg:ml-64">
            <!-- Breadcrumb -->
            <nav class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <ol class="flex items-center space-x-2 py-3 text-sm">
                        <li>
                            <a href="<?php echo $base_url; ?>dashboard.php" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li>
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        </li>
                        <li class="text-gray-500">
                            <?php echo $page_title ?? 'หน้าหลัก'; ?>
                        </li>
                    </ol>
                </div>
            </nav>
            
            <!-- Page content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <?php
                // Display flash messages
                if (isset($_SESSION['success'])) {
                    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">';
                    echo '<i class="fas fa-check-circle mr-2"></i>' . $_SESSION['success'];
                    echo '<button type="button" class="ml-auto" onclick="this.parentElement.style.display=\'none\'">';
                    echo '<i class="fas fa-times"></i>';
                    echo '</button>';
                    echo '</div>';
                    unset($_SESSION['success']);
                }
                
                if (isset($_SESSION['error'])) {
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">';
                    echo '<i class="fas fa-exclamation-triangle mr-2"></i>' . $_SESSION['error'];
                    echo '<button type="button" class="ml-auto" onclick="this.parentElement.style.display=\'none\'">';
                    echo '<i class="fas fa-times"></i>';
                    echo '</button>';
                    echo '</div>';
                    unset($_SESSION['error']);
                }
                
                if (isset($_SESSION['warning'])) {
                    echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 flex items-center">';
                    echo '<i class="fas fa-exclamation-triangle mr-2"></i>' . $_SESSION['warning'];
                    echo '<button type="button" class="ml-auto" onclick="this.parentElement.style.display=\'none\'">';
                    echo '<i class="fas fa-times"></i>';
                    echo '</button>';
                    echo '</div>';
                    unset($_SESSION['warning']);
                }
                
                if (isset($_SESSION['info'])) {
                    echo '<div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 flex items-center">';
                    echo '<i class="fas fa-info-circle mr-2"></i>' . $_SESSION['info'];
                    echo '<button type="button" class="ml-auto" onclick="this.parentElement.style.display=\'none\'">';
                    echo '<i class="fas fa-times"></i>';
                    echo '</button>';
                    echo '</div>';
                    unset($_SESSION['info']);
                }
                ?>
                
                <!-- Page Content -->
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Hospital Information -->
                <div class="space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-hospital text-2xl mr-3 text-blue-400"></i>
                        <div>
                            <h3 class="font-semibold text-lg">Hospital Procurement System</h3>
                            <p class="text-gray-400 text-sm">ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</p>
                        </div>
                    </div>
                    <p class="text-gray-300 text-sm">
                        ระบบจัดการการจัดซื้อครุภัณฑ์คอมพิวเตอร์สำหรับโรงพยาบาล 
                        ที่ช่วยให้การดำเนินงานมีประสิทธิภาพและโปร่งใส
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-lg text-blue-400">ลิงค์ด่วน</h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="<?php echo $base_url; ?>dashboard.php" 
                               class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                                <i class="fas fa-chart-line mr-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $base_url; ?>requests.php" 
                               class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                                <i class="fas fa-file-invoice mr-2"></i>รายการคำขอ
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $base_url; ?>reports.php" 
                               class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                                <i class="fas fa-chart-bar mr-2"></i>รายงาน
                            </a>
                        </li>
                        <?php if ($_SESSION['user_role'] == 'admin'): ?>
                        <li>
                            <a href="<?php echo $base_url; ?>users.php" 
                               class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                                <i class="fas fa-users mr-2"></i>จัดการผู้ใช้
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- User Information -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-lg text-blue-400">ข้อมูลผู้ใช้</h3>
                    <div class="bg-gray-700 rounded-lg p-4 space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-user-circle text-blue-400 mr-3"></i>
                            <div>
                                <p class="font-medium"><?php echo $_SESSION['user_name']; ?></p>
                                <p class="text-sm text-gray-400"><?php echo $_SESSION['user_role']; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-300">
                            <i class="fas fa-calendar mr-2"></i>
                            <span>เข้าสู่ระบบ: <?php echo date('d/m/Y H:i:s'); ?></span>
                        </div>
                        <div class="flex items-center text-sm text-gray-300">
                            <i class="fas fa-clock mr-2"></i>
                            <span>เวลาเซิร์ฟเวอร์: <?php echo date('d/m/Y H:i:s'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="border-t border-gray-700 mt-8 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="text-center md:text-left">
                        <p class="text-gray-400 text-sm">
                            © <?php echo date('Y'); ?> Hospital Procurement System. All rights reserved.
                        </p>
                        <p class="text-gray-500 text-xs mt-1">
                            Developed with ❤️ for healthcare efficiency
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fas fa-question-circle"></i>
                            <span class="ml-1 text-sm">ช่วยเหลือ</span>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fas fa-shield-alt"></i>
                            <span class="ml-1 text-sm">ความปลอดภัย</span>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fas fa-file-alt"></i>
                            <span class="ml-1 text-sm">เอกสาร</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.tailwindcss.min.js"></script>
    <script src="<?php echo $base_url; ?>js/main.js"></script>
    
    <script>
        // Toggle user menu
        function toggleUserMenu() {
            const dropdown = document.getElementById('user-dropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const dropdown = document.getElementById('user-dropdown');
            
            if (!userMenu.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('translate-x-0');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
        
        // Close mobile menu when clicking overlay
        document.getElementById('sidebar-overlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
        });
        
        // CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Set CSRF token for all AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            // Add CSRF token to all forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                if (!form.querySelector('input[name="csrf_token"]')) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }
            });
        });
        
        // Enhanced sidebar toggle with smooth animations
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                sidebar.classList.add('mobile-menu-animation');
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                overlay.classList.add('hidden');
                sidebar.classList.remove('mobile-menu-animation');
            }
        }
        
        // Toggle admin section
        function toggleAdminSection() {
            const section = document.getElementById('admin-section');
            const chevron = document.getElementById('admin-chevron');
            
            if (section.classList.contains('open')) {
                section.classList.remove('open');
                chevron.style.transform = 'rotate(-90deg)';
            } else {
                section.classList.add('open');
                chevron.style.transform = 'rotate(0deg)';
            }
        }
        
        // Add hover effects to navigation items
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.sidebar-nav-item');
            
            navItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(4px)';
                });
                
                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateX(0)';
                    }
                });
            });
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-yellow-100, .bg-blue-100');
            flashMessages.forEach(msg => {
                if (msg.style.display !== 'none') {
                    msg.style.opacity = '0';
                    msg.style.transition = 'opacity 0.5s ease-out';
                    setTimeout(() => msg.style.display = 'none', 500);
                }
            });
        }, 5000);
    </script>
</body>
</html>
<?php
}
?>