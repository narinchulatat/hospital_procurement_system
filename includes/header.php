<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Hospital Procurement System'; ?> - ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <link href="<?php echo $base_url ?? ''; ?>css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
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
                <a href="<?php echo $base_url ?? ''; ?>dashboard.php" class="flex items-center text-white hover:text-blue-200 transition-colors">
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
                        <a href="<?php echo $base_url ?? ''; ?>profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>ข้อมูลส่วนตัว
                        </a>
                        <a href="<?php echo $base_url ?? ''; ?>change_password.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-key mr-2"></i>เปลี่ยนรหัสผ่าน
                        </a>
                        <hr class="border-gray-200 my-1">
                        <a href="<?php echo $base_url ?? ''; ?>logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
        <?php include 'sidebar.php'; ?>
        
        <!-- Main content -->
        <div class="flex-1 lg:ml-64">
            <!-- Breadcrumb -->
            <nav class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <ol class="flex items-center space-x-2 py-3 text-sm">
                        <li>
                            <a href="<?php echo $base_url ?? ''; ?>dashboard.php" class="text-blue-600 hover:text-blue-800">
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
                
    <script src="<?php echo $base_url ?? ''; ?>js/main.js"></script>
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
    </script>
    <!-- Page content starts here -->