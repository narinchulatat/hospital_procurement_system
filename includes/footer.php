            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-2 md:mb-0">
                    <i class="fas fa-hospital mr-2"></i>
                    <span>ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์ - โรงพยาบาล</span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-user mr-1"></i>
                    <span><?php echo $_SESSION['user_name']; ?></span>
                    <span class="mx-2">|</span>
                    <i class="fas fa-calendar mr-1"></i>
                    <span><?php echo date('d/m/Y H:i:s'); ?></span>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
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
    </script>
</body>
</html>