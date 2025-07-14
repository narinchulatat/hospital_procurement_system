<!-- Navigation -->
<nav class="bg-blue-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-hospital text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <div class="text-white text-lg font-semibold">ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</div>
                    <div class="text-blue-200 text-sm">โรงพยาบาล</div>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="text-white">
                    <span class="text-sm">สวัสดี, <?php echo $_SESSION['user_name']; ?></span>
                    <span class="text-blue-200 text-xs ml-2">(<?php echo $_SESSION['user_role']; ?>)</span>
                </div>
                <div class="relative">
                    <button id="user-menu" class="flex items-center text-white hover:text-blue-200 focus:outline-none">
                        <i class="fas fa-user-circle text-xl"></i>
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>ข้อมูลส่วนตัว
                        </a>
                        <a href="change_password.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-key mr-2"></i>เปลี่ยนรหัสผ่าน
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>ออกจากระบบ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Breadcrumb -->
<nav class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-12">
            <div class="flex items-center space-x-4">
                <a href="dashboard.php" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-home"></i>
                </a>
                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                <span class="text-gray-700 font-medium"><?php echo $page_title; ?></span>
            </div>
            
            <div class="flex items-center space-x-4">
                <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
                <a href="request_form.php" class="text-blue-600 hover:text-blue-700">
                    <i class="fas fa-plus mr-1"></i>ยื่นคำขอใหม่
                </a>
                <?php endif; ?>
                
                <a href="requests.php" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-list mr-1"></i>รายการคำขอ
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
// User dropdown toggle
document.getElementById('user-menu').addEventListener('click', function() {
    document.getElementById('user-dropdown').classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('user-dropdown');
    const button = document.getElementById('user-menu');
    
    if (!button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>