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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .nav-item-active {
            background: rgba(255, 255, 255, 0.1);
            border-right: 3px solid #fff;
        }
        
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
        }
    </style>
    
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
    
    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 sidebar">
        <!-- Header -->
        <div class="flex items-center justify-between h-16 px-4 lg:justify-center lg:h-20">
            <div class="flex items-center text-white">
                <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-20 rounded-xl mr-3">
                    <i class="fas fa-hospital text-lg"></i>
                </div>
                <div class="hidden lg:block">
                    <h1 class="text-lg font-bold">ระบบจัดซื้อครุภัณฑ์</h1>
                    <p class="text-sm text-blue-100">โรงพยาบาล</p>
                </div>
                <div class="lg:hidden">
                    <h1 class="text-base font-bold">ระบบจัดซื้อครุภัณฑ์</h1>
                </div>
            </div>
            <button onclick="closeSidebar()" class="lg:hidden text-white p-2">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- User Profile -->
        <div class="px-4 py-4">
            <div class="bg-white bg-opacity-10 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="user-avatar w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-white font-medium text-sm"><?php echo $_SESSION['user_name']; ?></p>
                        <p class="text-blue-100 text-xs capitalize"><?php echo $_SESSION['user_role']; ?></p>
                    </div>
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                </div>
                
                <!-- User Menu -->
                <div class="mt-3">
                    <button onclick="toggleUserMenu()" class="w-full flex items-center justify-center text-white hover:text-blue-100 px-3 py-2 rounded-lg text-sm">
                        <span class="mr-2">เมนูผู้ใช้</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    
                    <div id="user-menu" class="hidden mt-2 bg-white rounded-lg shadow-lg py-1">
                        <a href="<?php echo $base_url; ?>profile.php" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>ข้อมูลส่วนตัว
                        </a>
                        <a href="<?php echo $base_url; ?>change_password.php" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-key mr-2"></i>เปลี่ยนรหัสผ่าน
                        </a>
                        <hr class="my-1">
                        <a href="<?php echo $base_url; ?>logout.php" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>ออกจากระบบ
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="px-4 py-2 space-y-1 flex-1 overflow-y-auto">
            <!-- Main Menu -->
            <div class="mb-6">
                <p class="text-xs font-semibold text-blue-100 uppercase mb-3 px-3">เมนูหลัก</p>
                
                <!-- Dashboard -->
                <a href="<?php echo $base_url; ?>dashboard.php" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-chart-line mr-3 w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- New Request -->
                <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
                <a href="<?php echo $base_url; ?>request_form.php" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'request_form.php' ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-plus-circle mr-3 w-5 text-center"></i>
                    <span>ยื่นคำขอใหม่</span>
                    <span class="ml-auto bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">New</span>
                </a>
                <?php endif; ?>
                
                <!-- Requests List -->
                <a href="<?php echo $base_url; ?>requests.php" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-list-alt mr-3 w-5 text-center"></i>
                    <span>รายการคำขอ</span>
                </a>
                
                <!-- Approve Request -->
                <?php if ($_SESSION['user_role'] == 'department_head' || $_SESSION['user_role'] == 'admin'): ?>
                <a href="<?php echo $base_url; ?>approve_request.php" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'approve_request.php' ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-check-circle mr-3 w-5 text-center"></i>
                    <span>อนุมัติคำขอ</span>
                    <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full">3</span>
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Admin Menu -->
            <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <div>
                <p class="text-xs font-semibold text-blue-100 uppercase mb-3 px-3">จัดการระบบ</p>
                
                <a href="<?php echo $base_url; ?>admin/users/" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo strpos($_SERVER['PHP_SELF'], '/admin/users/') !== false ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>
                    <span>ผู้ใช้งาน</span>
                </a>
                
                <a href="<?php echo $base_url; ?>admin/departments/" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo strpos($_SERVER['PHP_SELF'], '/admin/departments/') !== false ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-building mr-3 w-5 text-center"></i>
                    <span>แผนก</span>
                </a>
                
                <a href="<?php echo $base_url; ?>admin/items/" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo strpos($_SERVER['PHP_SELF'], '/admin/items/') !== false ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-laptop mr-3 w-5 text-center"></i>
                    <span>รายการครุภัณฑ์</span>
                </a>
                
                <a href="<?php echo $base_url; ?>admin/roles/" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo strpos($_SERVER['PHP_SELF'], '/admin/roles/') !== false ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-user-tag mr-3 w-5 text-center"></i>
                    <span>บทบาท</span>
                </a>
                
                <a href="<?php echo $base_url; ?>admin/status/" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo strpos($_SERVER['PHP_SELF'], '/admin/status/') !== false ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-flag mr-3 w-5 text-center"></i>
                    <span>สถานะ</span>
                </a>
                
                <a href="<?php echo $base_url; ?>admin/budget_years/" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo strpos($_SERVER['PHP_SELF'], '/admin/budget_years/') !== false ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-calendar-alt mr-3 w-5 text-center"></i>
                    <span>ปีงบประมาณ</span>
                </a>
                
                <a href="<?php echo $base_url; ?>admin/reports/" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-white rounded-lg nav-item <?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports/') !== false ? 'nav-item-active' : ''; ?>">
                    <i class="fas fa-chart-bar mr-3 w-5 text-center"></i>
                    <span>รายงาน</span>
                </a>
            </div>
            <?php endif; ?>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="lg:ml-64 min-h-screen flex flex-col">
        <!-- Mobile Header -->
        <div class="lg:hidden bg-white shadow-sm border-b">
            <div class="flex items-center justify-between px-4 py-3">
                <button onclick="openSidebar()" class="text-gray-600 hover:text-gray-900 p-2">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <h1 class="text-lg font-semibold text-gray-900">ระบบจัดซื้อครุภัณฑ์</h1>
                <div class="w-10"></div>
            </div>
        </div>
        
        <!-- Page Content -->
        <main class="flex-1 p-4 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Flash Messages -->
                <?php
                $flash_types = ['success', 'error', 'warning', 'info'];
                $flash_colors = [
                    'success' => 'bg-green-50 border-green-200 text-green-800',
                    'error' => 'bg-red-50 border-red-200 text-red-800',
                    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                    'info' => 'bg-blue-50 border-blue-200 text-blue-800'
                ];
                $flash_icons = [
                    'success' => 'fas fa-check-circle',
                    'error' => 'fas fa-exclamation-triangle',
                    'warning' => 'fas fa-exclamation-triangle',
                    'info' => 'fas fa-info-circle'
                ];
                
                foreach ($flash_types as $type) {
                    if (isset($_SESSION[$type])) {
                        echo '<div class="' . $flash_colors[$type] . ' border rounded-lg p-4 mb-6 flex items-start">';
                        echo '<i class="' . $flash_icons[$type] . ' mr-3 mt-0.5 flex-shrink-0"></i>';
                        echo '<div class="flex-1">' . $_SESSION[$type] . '</div>';
                        echo '<button onclick="this.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600 flex-shrink-0">';
                        echo '<i class="fas fa-times"></i>';
                        echo '</button>';
                        echo '</div>';
                        unset($_SESSION[$type]);
                    }
                }
                ?>
                
                <!-- Content -->
                <?php echo $content; ?>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8 mt-auto">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- System Info -->
                    <div>
                        <div class="flex items-center mb-4">
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
                    <div>
                        <h3 class="font-semibold text-lg mb-4 text-blue-400">ลิงค์ด่วน</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="<?php echo $base_url; ?>dashboard.php" class="text-gray-300 hover:text-white">Dashboard</a></li>
                            <li><a href="<?php echo $base_url; ?>requests.php" class="text-gray-300 hover:text-white">รายการคำขอ</a></li>
                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <li><a href="<?php echo $base_url; ?>admin/users/" class="text-gray-300 hover:text-white">จัดการผู้ใช้</a></li>
                            <li><a href="<?php echo $base_url; ?>admin/reports/" class="text-gray-300 hover:text-white">รายงาน</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- User Info -->
                    <div>
                        <h3 class="font-semibold text-lg mb-4 text-blue-400">ข้อมูลผู้ใช้</h3>
                        <div class="bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user-circle text-blue-400 mr-3"></i>
                                <div>
                                    <p class="font-medium"><?php echo $_SESSION['user_name']; ?></p>
                                    <p class="text-sm text-gray-400 capitalize"><?php echo $_SESSION['user_role']; ?></p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-300">
                                <p><i class="fas fa-calendar mr-2"></i>เข้าสู่ระบบ: <?php echo date('d/m/Y H:i:s'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 mt-8 pt-6 text-center">
                    <p class="text-gray-400 text-sm">
                        © <?php echo date('Y'); ?> Hospital Procurement System. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.tailwindcss.min.js"></script>
    
    <script>
        // Sidebar Functions
        function openSidebar() {
            document.getElementById('sidebar').classList.add('mobile-open');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        }
        
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('mobile-open');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
        
        // User Menu Toggle
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }
        
        // Close menus when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const userMenuButton = event.target.closest('[onclick="toggleUserMenu()"]');
            
            if (!userMenuButton && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
        
        // Close sidebar when clicking overlay
        document.getElementById('sidebar-overlay').addEventListener('click', closeSidebar);
        
        // Auto-hide flash messages
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('.bg-green-50, .bg-red-50, .bg-yellow-50, .bg-blue-50');
            flashMessages.forEach(msg => {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            });
        }, 5000);
        
        // CSRF token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Add CSRF token to all forms
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                if (csrfToken && !form.querySelector('input[name="csrf_token"]')) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }
            });
        });
    </script>
</body>
</html>
<?php
}
?>