// Hospital Procurement System - Main JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }
    
    // User dropdown toggle
    const userMenu = document.getElementById('user-menu');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userMenu) {
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (userDropdown && userMenu) {
            if (!userMenu.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        }
    });
    
    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });
    });
    
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const itemName = this.getAttribute('data-name') || 'รายการนี้';
            
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: `คุณแน่ใจหรือไม่ที่จะลบ ${itemName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }, 300));
    }
    
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseenter', showTooltip);
        tooltip.addEventListener('mouseleave', hideTooltip);
    });
});

// Form validation function
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        const errorElement = input.parentElement.querySelector('.form-error');
        
        if (!input.value.trim()) {
            showFieldError(input, 'กรุณากรอกข้อมูลในช่องนี้');
            isValid = false;
        } else {
            hideFieldError(input);
            
            // Additional validation based on input type
            if (input.type === 'email' && !isValidEmail(input.value)) {
                showFieldError(input, 'กรุณากรอกอีเมลที่ถูกต้อง');
                isValid = false;
            } else if (input.type === 'number' && isNaN(input.value)) {
                showFieldError(input, 'กรุณากรอกตัวเลขที่ถูกต้อง');
                isValid = false;
            } else if (input.type === 'tel' && !isValidPhone(input.value)) {
                showFieldError(input, 'กรุณากรอกเบอร์โทรศัพท์ที่ถูกต้อง');
                isValid = false;
            }
        }
    });
    
    return isValid;
}

// Show field error
function showFieldError(input, message) {
    input.classList.add('error');
    let errorElement = input.parentElement.querySelector('.form-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'form-error';
        input.parentElement.appendChild(errorElement);
    }
    errorElement.textContent = message;
}

// Hide field error
function hideFieldError(input) {
    input.classList.remove('error');
    const errorElement = input.parentElement.querySelector('.form-error');
    if (errorElement) {
        errorElement.remove();
    }
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Phone validation
function isValidPhone(phone) {
    const phoneRegex = /^[0-9]{9,10}$/;
    return phoneRegex.test(phone.replace(/[-\s]/g, ''));
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Tooltip functions
function showTooltip(e) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = e.target.getAttribute('data-tooltip');
    tooltip.style.cssText = `
        position: absolute;
        background: #1f2937;
        color: white;
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        z-index: 1000;
        pointer-events: none;
        top: ${e.pageY - 35}px;
        left: ${e.pageX}px;
        transform: translateX(-50%);
    `;
    document.body.appendChild(tooltip);
    e.target.tooltipElement = tooltip;
}

function hideTooltip(e) {
    if (e.target.tooltipElement) {
        e.target.tooltipElement.remove();
        e.target.tooltipElement = null;
    }
}

// AJAX helper functions
function sendAjaxRequest(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        
        if (method === 'POST' || method === 'PUT') {
            xhr.setRequestHeader('Content-Type', 'application/json');
        }
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        resolve(response);
                    } catch (e) {
                        resolve(xhr.responseText);
                    }
                } else {
                    reject(new Error(`HTTP ${xhr.status}: ${xhr.statusText}`));
                }
            }
        };
        
        xhr.onerror = function() {
            reject(new Error('Network error'));
        };
        
        if (data) {
            xhr.send(JSON.stringify(data));
        } else {
            xhr.send();
        }
    });
}

// Loading spinner
function showLoading(element) {
    const spinner = document.createElement('div');
    spinner.className = 'spinner';
    spinner.style.cssText = 'margin: 0 auto; display: block;';
    element.innerHTML = '';
    element.appendChild(spinner);
}

function hideLoading(element, content) {
    element.innerHTML = content;
}

// Success/Error message display
function showMessage(message, type = 'success') {
    Swal.fire({
        icon: type,
        title: type === 'success' ? 'สำเร็จ' : 'เกิดข้อผิดพลาด',
        text: message,
        confirmButtonText: 'ตกลง'
    });
}

// Auto-resize textarea
function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Export functions for global use
window.HospitalProcurement = {
    openModal,
    closeModal,
    showMessage,
    sendAjaxRequest,
    validateForm,
    formatCurrency,
    formatDate,
    autoResize
};