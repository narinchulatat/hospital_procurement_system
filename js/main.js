// Hospital Procurement System - Main JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables for all tables with class 'data-table'
    initializeDataTables();
    
    // Initialize mobile menu
    initializeMobileMenu();
    
    // Initialize user dropdown
    initializeUserDropdown();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize delete confirmations
    initializeDeleteConfirmations();
    
    // Auto-hide alerts
    initializeAlerts();
    
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize fade-in animations
    initializeFadeAnimations();
});

// DataTables initialization function
function initializeDataTables() {
    // Check if DataTables is available
    if (typeof $.fn.DataTable !== 'undefined') {
        const dataTables = document.querySelectorAll('.data-table');
        
        dataTables.forEach(table => {
            const tableId = table.getAttribute('id') || 'table_' + Math.random().toString(36).substr(2, 9);
            table.setAttribute('id', tableId);
            
            // Get configuration from data attributes
            const config = {
                responsive: true,
                processing: true,
                language: {
                    search: 'ค้นหา:',
                    lengthMenu: 'แสดง _MENU_ รายการต่อหน้า',
                    info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
                    infoEmpty: 'ไม่มีข้อมูล',
                    infoFiltered: '(กรองจากทั้งหมด _MAX_ รายการ)',
                    paginate: {
                        first: 'หน้าแรก',
                        last: 'หน้าสุดท้าย',
                        next: 'ถัดไป',
                        previous: 'ก่อนหน้า'
                    },
                    processing: 'กำลังประมวลผล...',
                    emptyTable: 'ไม่มีข้อมูลในตาราง',
                    zeroRecords: 'ไม่พบข้อมูลที่ค้นหา'
                },
                pageLength: parseInt(table.getAttribute('data-page-length')) || 10,
                order: [],
                columnDefs: [
                    {
                        targets: 'no-sort',
                        orderable: false
                    }
                ],
                dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"mb-2 sm:mb-0"l><"mb-2 sm:mb-0"f>>t<"flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4"<"mb-2 sm:mb-0"i><"mb-2 sm:mb-0"p>>',
                drawCallback: function(settings) {
                    // Apply Tailwind classes to pagination buttons
                    const wrapper = document.getElementById(tableId + '_wrapper');
                    if (wrapper) {
                        const paginateButtons = wrapper.querySelectorAll('.paginate_button');
                        paginateButtons.forEach(button => {
                            button.classList.add('px-3', 'py-2', 'ml-1', 'text-sm', 'border', 'border-gray-300', 'rounded-md', 'text-gray-700', 'bg-white', 'hover:bg-gray-50', 'hover:text-gray-900', 'focus:outline-none', 'focus:ring-2', 'focus:ring-blue-500', 'focus:border-transparent');
                        });
                        
                        const currentButtons = wrapper.querySelectorAll('.current');
                        currentButtons.forEach(button => {
                            button.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                        });
                        
                        const disabledButtons = wrapper.querySelectorAll('.disabled');
                        disabledButtons.forEach(button => {
                            button.classList.add('opacity-50', 'cursor-not-allowed');
                        });
                    }
                },
                initComplete: function(settings, json) {
                    // Apply Tailwind classes to search input and length select
                    const wrapper = document.getElementById(tableId + '_wrapper');
                    if (wrapper) {
                        const searchInput = wrapper.querySelector('input[type="search"]');
                        if (searchInput) {
                            searchInput.classList.add('form-input', 'w-auto', 'inline-block', 'ml-2');
                        }
                        
                        const lengthSelect = wrapper.querySelector('select[name*="length"]');
                        if (lengthSelect) {
                            lengthSelect.classList.add('form-select', 'w-auto', 'inline-block', 'ml-2');
                        }
                    }
                }
            };
            
            // Apply server-side processing if specified
            if (table.hasAttribute('data-server-side')) {
                config.serverSide = true;
                config.ajax = {
                    url: table.getAttribute('data-ajax-url') || '',
                    type: 'POST',
                    data: function(d) {
                        // Add any additional parameters here
                        return d;
                    }
                };
            }
            
            // Initialize DataTable
            try {
                $(table).DataTable(config);
            } catch (error) {
                console.error('Error initializing DataTable:', error);
            }
        });
    }
}

// Initialize mobile menu
function initializeMobileMenu() {
    const mobileSidebarButton = document.getElementById('mobile-sidebar-button');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    
    if (mobileSidebarButton && sidebar && sidebarOverlay) {
        mobileSidebarButton.addEventListener('click', function() {
            sidebar.classList.toggle('translate-x-0');
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });
        
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            sidebarOverlay.classList.add('hidden');
        });
    }
}

// Initialize user dropdown
function initializeUserDropdown() {
    const userMenu = document.getElementById('sidebar-user-menu');
    const userDropdown = document.getElementById('sidebar-user-dropdown');
    
    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!userMenu.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }
}

// Initialize form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });
    });
}

// Initialize delete confirmations
function initializeDeleteConfirmations() {
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
}

// Initialize alerts
function initializeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Add close button functionality
        const closeBtn = alert.querySelector('.alert-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                alert.classList.add('opacity-0');
                setTimeout(() => alert.remove(), 300);
            });
        }
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (alert.parentElement) {
                alert.classList.add('opacity-0');
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    });
}

// Initialize tooltips
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseenter', showTooltip);
        tooltip.addEventListener('mouseleave', hideTooltip);
    });
}

// Initialize fade-in animations
function initializeFadeAnimations() {
    const fadeElements = document.querySelectorAll('.fade-in');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }
        });
    });
    
    fadeElements.forEach(element => {
        observer.observe(element);
    });
}

// Function to reinitialize DataTables (useful for dynamic content)
function reinitializeDataTables() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.data-table').each(function() {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().destroy();
            }
        });
        initializeDataTables();
    }
}

// Function to add a new row to DataTable
function addRowToDataTable(tableId, rowData) {
    if (typeof $.fn.DataTable !== 'undefined') {
        const table = $('#' + tableId).DataTable();
        table.row.add(rowData).draw();
    }
}

// Function to remove a row from DataTable
function removeRowFromDataTable(tableId, rowIndex) {
    if (typeof $.fn.DataTable !== 'undefined') {
        const table = $('#' + tableId).DataTable();
        table.row(rowIndex).remove().draw();
    }
}

// Function to update a row in DataTable
function updateRowInDataTable(tableId, rowIndex, rowData) {
    if (typeof $.fn.DataTable !== 'undefined') {
        const table = $('#' + tableId).DataTable();
        table.row(rowIndex).data(rowData).draw();
    }
}

// Function to refresh DataTable
function refreshDataTable(tableId) {
    if (typeof $.fn.DataTable !== 'undefined') {
        const table = $('#' + tableId).DataTable();
        table.ajax.reload(null, false);
    }
}

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
    autoResize,
    initializeDataTables,
    reinitializeDataTables,
    addRowToDataTable,
    removeRowFromDataTable,
    updateRowInDataTable,
    refreshDataTable
};