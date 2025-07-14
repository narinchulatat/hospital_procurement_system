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
                            <a href="<?php echo $base_url ?? ''; ?>dashboard.php" 
                               class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                                <i class="fas fa-chart-line mr-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $base_url ?? ''; ?>requests.php" 
                               class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                                <i class="fas fa-file-invoice mr-2"></i>รายการคำขอ
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $base_url ?? ''; ?>reports.php" 
                               class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                                <i class="fas fa-chart-bar mr-2"></i>รายงาน
                            </a>
                        </li>
                        <?php if ($_SESSION['user_role'] == 'admin'): ?>
                        <li>
                            <a href="<?php echo $base_url ?? ''; ?>users.php" 
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
    <script src="<?php echo $base_url ?? ''; ?>js/main.js"></script>
    
    <script>
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