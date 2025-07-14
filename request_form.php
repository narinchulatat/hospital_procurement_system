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
$base_url = '';
include 'includes/header.php';
?>
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h2 text-dark fw-bold">ยื่นคำขอใหม่</h1>
        <p class="text-muted">กรอกข้อมูลคำขอซื้อครุภัณฑ์คอมพิวเตอร์</p>
    </div>

    <!-- Request Period Status -->
    <?php if ($request_status): ?>
    <div class="mb-4">
        <?php if ($request_status['status'] == 'not_started'): ?>
        <div class="alert alert-warning">
            <div class="d-flex align-items-center">
                <i class="fas fa-clock text-warning me-3"></i>
                <div>
                    <h5 class="alert-heading">ยังไม่ถึงช่วงเวลาการขอ</h5>
                    <p class="mb-0">
                        การขอจะเปิดในวันที่ <?php echo formatThaiDate($request_status['request_start_date']); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php elseif ($request_status['status'] == 'closed'): ?>
        <div class="alert alert-danger">
            <div class="d-flex align-items-center">
                <i class="fas fa-times-circle text-danger me-3"></i>
                <div>
                    <h5 class="alert-heading">ปิดช่วงเวลาการขอแล้ว</h5>
                    <p class="mb-0">
                        การขอปิดเมื่อวันที่ <?php echo formatThaiDate($request_status['request_end_date']); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-success">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle text-success me-3"></i>
                <div>
                    <h5 class="alert-heading">ช่วงเวลาการขอเปิดอยู่</h5>
                    <p class="mb-0">
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
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">ข้อมูลคำขอ</h5>
            </div>
            
            <div class="card-body">
                <form action="request_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="row g-3">
                        <!-- Department -->
                        <div class="col-md-6">
                            <label for="department_id" class="form-label">แผนก</label>
                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <select id="department_id" name="department_id" required class="form-select">
                                <option value="">เลือกแผนก</option>
                                <?php while ($dept = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                            <?php else: ?>
                            <input type="hidden" name="department_id" value="<?php echo $_SESSION['department_id']; ?>">
                            <input type="text" value="<?php echo htmlspecialchars($_SESSION['department_name']); ?>" 
                                   class="form-control" readonly>
                            <?php endif; ?>
                        </div>

                        <!-- Sub-department -->
                        <div class="col-md-6">
                            <label for="sub_department_id" class="form-label">หน่วยงาน</label>
                            <select id="sub_department_id" name="sub_department_id" class="form-select">
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
                    <div class="col-12">
                        <label for="item_id" class="form-label">รายการครุภัณฑ์</label>
                        <select id="item_id" name="item_id" required class="form-select">
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
                    <div id="item-details" class="col-12 d-none">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">รายละเอียดครุภัณฑ์</h6>
                            <div id="item-specifications" class="small"></div>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-6">
                        <label for="quantity" class="form-label">จำนวน</label>
                        <input type="number" id="quantity" name="quantity" min="1" required class="form-control">
                        <div class="form-text">
                            หน่วย: <span id="unit-display">-</span>
                        </div>
                    </div>

                    <!-- Total Price -->
                    <div class="col-md-6">
                        <label class="form-label">ราคารวม</label>
                        <div class="fs-5 fw-bold text-primary" id="total-price">0.00 บาท</div>
                    </div>

                    <!-- Request Type -->
                    <div class="col-12">
                        <label class="form-label">ประเภทการขอ</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="request_type" value="new" checked id="request_new">
                                <label class="form-check-label" for="request_new">
                                    ขอใหม่
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="request_type" value="replacement" id="request_replacement">
                                <label class="form-check-label" for="request_replacement">
                                    ขอทดแทน
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Old Equipment Code (for replacement) -->
                    <div id="old-equipment-section" class="col-12 d-none">
                        <label for="old_equipment_code" class="form-label">เลขครุภัณฑ์เดิม</label>
                        <input type="text" id="old_equipment_code" name="old_equipment_code" 
                               class="form-control"
                               placeholder="กรอกเลขครุภัณฑ์เดิมที่ต้องการทดแทน">
                    </div>

                    <!-- Reason -->
                    <div class="col-12">
                        <label for="reason" class="form-label">เหตุผลความจำเป็น</label>
                        <textarea id="reason" name="reason" rows="4" required 
                                  class="form-control"
                                  placeholder="กรอกเหตุผลความจำเป็นในการขอครุภัณฑ์"></textarea>
                    </div>

                    <!-- File Attachment -->
                    <div class="col-12">
                        <label for="attachments" class="form-label">ไฟล์แนบ</label>
                        <input type="file" id="attachments" name="attachments[]" multiple 
                               accept=".pdf,.jpg,.jpeg,.png" 
                               class="form-control">
                        <div class="form-text">
                            รองรับไฟล์ PDF, JPG, PNG เท่านั้น (ขนาดไม่เกิน 10MB ต่อไฟล์)
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="dashboard.php" class="btn btn-secondary">
                        ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-primary">
                        ยื่นคำขอ
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Item selection handler
        document.getElementById('item_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const itemDetails = document.getElementById('item-details');
            const itemSpecs = document.getElementById('item-specifications');
            const unitDisplay = document.getElementById('unit-display');
            
            if (selectedOption.value) {
                itemDetails.classList.remove('d-none');
                itemSpecs.textContent = selectedOption.dataset.specs || 'ไม่มีข้อมูลรายละเอียด';
                unitDisplay.textContent = selectedOption.dataset.unit || '-';
                
                // Calculate total price
                calculateTotal();
            } else {
                itemDetails.classList.add('d-none');
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
                    oldEquipmentSection.classList.remove('d-none');
                    oldEquipmentInput.required = true;
                } else {
                    oldEquipmentSection.classList.add('d-none');
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

<?php include 'includes/footer.php'; ?>