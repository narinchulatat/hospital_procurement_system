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
                <a href="<?php echo $base_url; ?>dashboard.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                    <i class="fas fa-chart-line <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                    <span>📊 Dashboard</span>
                </a>
                
                <!-- ยื่นคำขอใหม่ - for staff and department_head -->
                <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
                <a href="<?php echo $base_url; ?>request_form.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                    <i class="fas fa-plus-circle <?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                    <span>🟢 ยื่นคำขอใหม่</span>
                </a>
                <?php endif; ?>
                
                <!-- รายการคำขอ -->
                <a href="<?php echo $base_url; ?>requests.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                    <i class="fas fa-list-alt <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                    <span>📋 รายการคำขอ</span>
                </a>
                
                <!-- อนุมัติคำขอ - for department_head and admin -->
                <?php if ($_SESSION['user_role'] == 'department_head' || $_SESSION['user_role'] == 'admin'): ?>
                <a href="<?php echo $base_url; ?>approve_request.php" 
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
                    
                    <a href="<?php echo $base_url; ?>admin/users/" 
                       class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-users <?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                        <span>👥 ผู้ใช้งาน</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>admin/departments/" 
                       class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-building <?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                        <span>🏢 แผนก</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>admin/items/" 
                       class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-laptop <?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                        <span>📦 รายการครุภัณฑ์</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>admin/roles/" 
                       class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-user-tag <?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                        <span>🏷️ บทบาท</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>admin/status/" 
                       class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-flag <?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                        <span>📊 สถานะ</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>admin/budget_years/" 
                       class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/budget_years/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-calendar-alt <?php echo strpos($_SERVER['PHP_SELF'], '/admin/budget_years/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                        <span>💰 ปีงบประมาณ</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>admin/reports/" 
                       class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports/') !== false ? 'bg-blue-100 text-blue-700 border-r-4 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-chart-bar <?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 w-5"></i>
                        <span>📈 รายงาน</span>
                    </a>
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
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                overlay.classList.add('hidden');
            }
        }
        
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