<?php
require_once 'includes/functions.php';
checkLogin();
checkRole(['staff', 'department_head']);

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if request period is open
$budget_year = new BudgetYear($db);
$current_budget_year = $budget_year->getCurrentBudgetYear();
$request_status = null;
if ($current_budget_year) {
    $request_status = $budget_year->getRequestPeriodStatus($budget_year->id);
}

// Get available items
$item = new Item($db);
$items = $item->readByBudgetYear($budget_year->id);

// Get departments for dropdown
$department = new Department($db);
$departments = $department->readAll();

// Get sub-departments for current user's department
$sub_departments = null;
if ($_SESSION['department_id']) {
    $sub_departments = $department->getSubDepartments();
}

$page_title = 'ยื่นคำขอใหม่';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">ยื่นคำขอใหม่</h1>
            <p class="text-gray-600">กรอกข้อมูลคำขอซื้อครุภัณฑ์คอมพิวเตอร์</p>
        </div>

        <!-- Request Period Status -->
        <?php if ($request_status): ?>
        <div class="mb-6">
            <?php if ($request_status['status'] == 'not_started'): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-600 mr-3"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-900">ยังไม่ถึงช่วงเวลาการขอ</h3>
                        <p class="text-yellow-700">
                            การขอจะเปิดในวันที่ <?php echo formatThaiDate($request_status['request_start_date']); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php elseif ($request_status['status'] == 'closed'): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-3"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-red-900">ปิดช่วงเวลาการขอแล้ว</h3>
                        <p class="text-red-700">
                            การขอปิดเมื่อวันที่ <?php echo formatThaiDate($request_status['request_end_date']); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-green-900">ช่วงเวลาการขอเปิดอยู่</h3>
                        <p class="text-green-700">
                            สามารถยื่นคำขอได้ถึงวันที่ <?php echo formatThaiDate($request_status['request_end_date']); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Request Form -->
        <?php if ($request_status && $request_status['status'] == 'open'): ?>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">ข้อมูลคำขอ</h3>
            </div>
            
            <form action="request_process.php" method="POST" enctype="multipart/form-data" class="p-6">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">แผนก</label>
                        <?php if ($_SESSION['user_role'] == 'admin'): ?>
                        <select id="department_id" name="department_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกแผนก</option>
                            <?php while ($dept = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                        <?php else: ?>
                        <input type="hidden" name="department_id" value="<?php echo $_SESSION['department_id']; ?>">
                        <input type="text" value="<?php echo htmlspecialchars($_SESSION['department_name']); ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                        <?php endif; ?>
                    </div>

                    <!-- Sub-department -->
                    <div>
                        <label for="sub_department_id" class="block text-sm font-medium text-gray-700 mb-2">หน่วยงาน</label>
                        <select id="sub_department_id" name="sub_department_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกหน่วยงาน (ถ้ามี)</option>
                            <?php if ($sub_departments): ?>
                            <?php while ($sub_dept = $sub_departments->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $sub_dept['id']; ?>" 
                                    <?php echo ($sub_dept['id'] == $_SESSION['sub_department_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sub_dept['name']); ?>
                            </option>
                            <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Item -->
                    <div class="md:col-span-2">
                        <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">รายการครุภัณฑ์</label>
                        <select id="item_id" name="item_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกรายการครุภัณฑ์</option>
                            <?php while ($item_row = $items->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $item_row['id']; ?>" 
                                    data-price="<?php echo $item_row['unit_price']; ?>" 
                                    data-unit="<?php echo htmlspecialchars($item_row['unit']); ?>"
                                    data-specs="<?php echo htmlspecialchars($item_row['specifications']); ?>">
                                <?php echo htmlspecialchars($item_row['name']); ?> 
                                (<?php echo number_format($item_row['unit_price'], 2); ?> บาท/<?php echo htmlspecialchars($item_row['unit']); ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Item Details -->
                    <div id="item-details" class="md:col-span-2 hidden">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-2">รายละเอียดครุภัณฑ์</h4>
                            <div id="item-specifications" class="text-sm text-gray-600"></div>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">จำนวน</label>
                        <input type="number" id="quantity" name="quantity" min="1" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="mt-1 text-sm text-gray-500">
                            หน่วย: <span id="unit-display">-</span>
                        </div>
                    </div>

                    <!-- Total Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ราคารวม</label>
                        <div class="text-lg font-semibold text-blue-600" id="total-price">0.00 บาท</div>
                    </div>

                    <!-- Request Type -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ประเภทการขอ</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="request_type" value="new" checked 
                                       class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span>ขอใหม่</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="request_type" value="replacement" 
                                       class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span>ขอทดแทน</span>
                            </label>
                        </div>
                    </div>

                    <!-- Old Equipment Code (for replacement) -->
                    <div id="old-equipment-section" class="md:col-span-2 hidden">
                        <label for="old_equipment_code" class="block text-sm font-medium text-gray-700 mb-2">เลขครุภัณฑ์เดิม</label>
                        <input type="text" id="old_equipment_code" name="old_equipment_code" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="กรอกเลขครุภัณฑ์เดิมที่ต้องการทดแทน">
                    </div>

                    <!-- Reason -->
                    <div class="md:col-span-2">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">เหตุผลความจำเป็น</label>
                        <textarea id="reason" name="reason" rows="4" required 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="กรอกเหตุผลความจำเป็นในการขอครุภัณฑ์"></textarea>
                    </div>

                    <!-- File Attachment -->
                    <div class="md:col-span-2">
                        <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">ไฟล์แนบ</label>
                        <input type="file" id="attachments" name="attachments[]" multiple 
                               accept=".pdf,.jpg,.jpeg,.png" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">
                            รองรับไฟล์ PDF, JPG, PNG เท่านั้น (ขนาดไม่เกิน 10MB ต่อไฟล์)
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="dashboard.php" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        ยกเลิก
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        ยื่นคำขอ
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Item selection handler
        document.getElementById('item_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const itemDetails = document.getElementById('item-details');
            const itemSpecs = document.getElementById('item-specifications');
            const unitDisplay = document.getElementById('unit-display');
            
            if (selectedOption.value) {
                itemDetails.classList.remove('hidden');
                itemSpecs.textContent = selectedOption.dataset.specs || 'ไม่มีข้อมูลรายละเอียด';
                unitDisplay.textContent = selectedOption.dataset.unit || '-';
                
                // Calculate total price
                calculateTotal();
            } else {
                itemDetails.classList.add('hidden');
                unitDisplay.textContent = '-';
                document.getElementById('total-price').textContent = '0.00 บาท';
            }
        });

        // Quantity change handler
        document.getElementById('quantity').addEventListener('input', calculateTotal);

        // Calculate total price
        function calculateTotal() {
            const itemSelect = document.getElementById('item_id');
            const quantityInput = document.getElementById('quantity');
            const totalPriceElement = document.getElementById('total-price');
            
            if (itemSelect.value && quantityInput.value) {
                const unitPrice = parseFloat(itemSelect.options[itemSelect.selectedIndex].dataset.price || 0);
                const quantity = parseInt(quantityInput.value || 0);
                const total = unitPrice * quantity;
                
                totalPriceElement.textContent = new Intl.NumberFormat('th-TH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(total) + ' บาท';
            } else {
                totalPriceElement.textContent = '0.00 บาท';
            }
        }

        // Request type change handler
        document.querySelectorAll('input[name="request_type"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const oldEquipmentSection = document.getElementById('old-equipment-section');
                const oldEquipmentInput = document.getElementById('old_equipment_code');
                
                if (this.value === 'replacement') {
                    oldEquipmentSection.classList.remove('hidden');
                    oldEquipmentInput.required = true;
                } else {
                    oldEquipmentSection.classList.add('hidden');
                    oldEquipmentInput.required = false;
                    oldEquipmentInput.value = '';
                }
            });
        });

        // File upload validation
        document.getElementById('attachments').addEventListener('change', function() {
            const files = this.files;
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไฟล์ขนาดใหญ่เกินไป',
                        text: `ไฟล์ "${file.name}" มีขนาดใหญ่กว่า 10MB`,
                        confirmButtonText: 'ตกลง'
                    });
                    this.value = '';
                    return;
                }
                
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ประเภทไฟล์ไม่ถูกต้อง',
                        text: `ไฟล์ "${file.name}" ไม่ใช่ประเภทที่รองรับ`,
                        confirmButtonText: 'ตกลง'
                    });
                    this.value = '';
                    return;
                }
            }
        });

        // Display messages
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'ตกลง'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>