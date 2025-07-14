            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-3 mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">
                        <i class="fas fa-hospital me-2"></i>
                        ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์ - โรงพยาบาล
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="fas fa-user me-1"></i>
                        <?php echo $_SESSION['user_name']; ?> | 
                        <i class="fas fa-calendar me-1"></i>
                        <?php echo date('d/m/Y H:i:s'); ?>
                    </p>
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