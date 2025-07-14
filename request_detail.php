<?php
require_once 'includes/functions.php';
checkLogin();

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get request ID
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get request details
$procurement_request = new ProcurementRequest($db);
$procurement_request->id = $request_id;
if (!$procurement_request->readOne()) {
    $_SESSION['error'] = 'ไม่พบคำขอที่ระบุ';
    header('Location: requests.php');
    exit();
}

// Check permission
if ($_SESSION['user_role'] == 'staff' && $procurement_request->user_id != $_SESSION['user_id']) {
    $_SESSION['error'] = 'คุณไม่มีสิทธิ์เข้าถึงคำขอนี้';
    header('Location: requests.php');
    exit();
}

if ($_SESSION['user_role'] == 'department_head' && $procurement_request->department_id != $_SESSION['department_id']) {
    $_SESSION['error'] = 'คุณไม่มีสิทธิ์เข้าถึงคำขอนี้';
    header('Location: requests.php');
    exit();
}

// Get additional details
$stmt = $db->prepare("SELECT pr.*, u.first_name, u.last_name, u.email, d.name as department_name, 
                             sd.name as sub_department_name, i.name as item_name, i.code as item_code,
                             i.unit, i.specifications, s.name as status_name, b.year_be,
                             approver.first_name as approver_first_name, approver.last_name as approver_last_name
                      FROM procurement_requests pr
                      LEFT JOIN users u ON pr.user_id = u.id
                      LEFT JOIN departments d ON pr.department_id = d.id
                      LEFT JOIN sub_departments sd ON pr.sub_department_id = sd.id
                      LEFT JOIN items i ON pr.item_id = i.id
                      LEFT JOIN statuses s ON pr.status_id = s.id
                      LEFT JOIN budget_years b ON pr.budget_year_id = b.id
                      LEFT JOIN users approver ON pr.approved_by = approver.id
                      WHERE pr.id = ?");
$stmt->execute([$request_id]);
$request_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Get attachments
$attachment = new Attachment($db);
$attachments = $attachment->readByRequest($request_id);

// Get activity logs
$activity_log = new ActivityLog($db);
$logs = $activity_log->readByTable('procurement_requests', $request_id);

// Get available next statuses
$status = new Status($db);
$next_statuses = $status->getNextStatuses($request_data['status_id']);

$page_title = 'รายละเอียดคำขอ';
$base_url = '';
include 'includes/sidebar.php';
?>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">รายละเอียดคำขอ</h1>
                    <p class="text-gray-600">เลขที่คำขอ: <?php echo htmlspecialchars($request_data['request_number']); ?></p>
                </div>
                <div class="flex items-center space-x-3">
                    <?php 
                    $status_class = $status->getStatusBadgeClass($request_data['status_name']);
                    $status_text = $status->getStatusTranslation($request_data['status_name']);
                    ?>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo $status_class; ?>">
                        <?php echo $status_text; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Request Details -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">ข้อมูลคำขอ</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">ผู้ยื่นคำขอ</h4>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($request_data['first_name'] . ' ' . $request_data['last_name']); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">แผนก</h4>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($request_data['department_name']); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">หน่วยงาน</h4>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($request_data['sub_department_name'] ?: '-'); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">ปีงบประมาณ</h4>
                                <p class="mt-1 text-gray-900"><?php echo $request_data['year_be']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Details -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">รายละเอียดครุภัณฑ์</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">รายการ</h4>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($request_data['item_name']); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">รหัสสินค้า</h4>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($request_data['item_code']); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">จำนวน</h4>
                                <p class="mt-1 text-gray-900"><?php echo number_format($request_data['quantity']); ?> <?php echo htmlspecialchars($request_data['unit']); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">ราคาต่อหน่วย</h4>
                                <p class="mt-1 text-gray-900">฿<?php echo number_format($request_data['unit_price'], 2); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">ราคารวม</h4>
                                <p class="mt-1 text-lg font-semibold text-blue-600">฿<?php echo number_format($request_data['total_price'], 2); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">ประเภทการขอ</h4>
                                <p class="mt-1 text-gray-900">
                                    <?php echo $request_data['request_type'] == 'new' ? 'ขอใหม่' : 'ขอทดแทน'; ?>
                                </p>
                            </div>
                            <?php if ($request_data['request_type'] == 'replacement'): ?>
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-medium text-gray-500">เลขครุภัณฑ์เดิม</h4>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($request_data['old_equipment_code']); ?></p>
                            </div>
                            <?php endif; ?>
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-medium text-gray-500">คุณสมบัติ</h4>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($request_data['specifications']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reason -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">เหตุผลความจำเป็น</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-900"><?php echo nl2br(htmlspecialchars($request_data['reason'])); ?></p>
                    </div>
                </div>

                <!-- Attachments -->
                <?php if ($attachments->rowCount() > 0): ?>
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">ไฟล์แนบ</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <?php while ($attachment_data = $attachments->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="<?php echo $attachment->getFileIcon($attachment_data['file_type']); ?> mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($attachment_data['original_filename']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo $attachment->formatFileSize($attachment_data['file_size']); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="download.php?id=<?php echo $attachment_data['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_id'] == $request_data['user_id']): ?>
                                    <button onclick="deleteAttachment(<?php echo $attachment_data['id']; ?>)" 
                                            class="text-red-600 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Approval Section -->
                <?php if ($request_data['approved_by']): ?>
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">ข้อมูลการอนุมัติ</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">ผู้อนุมัติ</h4>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($request_data['approver_first_name'] . ' ' . $request_data['approver_last_name']); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">วันที่อนุมัติ</h4>
                                <p class="mt-1 text-gray-900"><?php echo formatThaiDateTime($request_data['approved_at']); ?></p>
                            </div>
                            <?php if ($request_data['approval_notes']): ?>
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-medium text-gray-500">หมายเหตุ</h4>
                                <p class="mt-1 text-gray-900"><?php echo nl2br(htmlspecialchars($request_data['approval_notes'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Actions -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">การดำเนินการ</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <?php if ($_SESSION['user_role'] == 'admin' || 
                                     ($_SESSION['user_role'] == 'department_head' && $request_data['department_id'] == $_SESSION['department_id'])): ?>
                                <?php if ($request_data['status_id'] == 1): // pending ?>
                                <button onclick="approveRequest()" 
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    <i class="fas fa-check mr-2"></i>อนุมัติ
                                </button>
                                <button onclick="rejectRequest()" 
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    <i class="fas fa-times mr-2"></i>ปฏิเสธ
                                </button>
                                <?php endif; ?>
                                
                                <?php if (!empty($next_statuses)): ?>
                                <button onclick="updateStatus()" 
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <i class="fas fa-arrow-right mr-2"></i>เปลี่ยนสถานะ
                                </button>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <button onclick="rollbackStatus()" 
                                    class="w-full px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                                <i class="fas fa-undo mr-2"></i>ย้อนกลับสถานะ
                            </button>
                            <?php endif; ?>
                            
                            <a href="requests.php" 
                               class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-center block">
                                <i class="fas fa-arrow-left mr-2"></i>กลับรายการ
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">ประวัติการดำเนินการ</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php while ($log = $logs->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-blue-600 text-xs"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-gray-900"><?php echo htmlspecialchars($log['description']); ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        โดย <?php echo htmlspecialchars($log['first_name'] . ' ' . $log['last_name']); ?> 
                                        - <?php echo formatThaiDateTime($log['created_at']); ?>
                                    </p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function approveRequest() {
            Swal.fire({
                title: 'อนุมัติคำขอ',
                text: 'คุณต้องการอนุมัติคำขอนี้หรือไม่?',
                icon: 'question',
                input: 'textarea',
                inputLabel: 'หมายเหตุ (ถ้ามี)',
                inputPlaceholder: 'กรอกหมายเหตุ...',
                showCancelButton: true,
                confirmButtonText: 'อนุมัติ',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#10b981'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'approve_request.php';
                    
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'request_id';
                    idInput.value = <?php echo $request_id; ?>;
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'approve';
                    
                    const notesInput = document.createElement('input');
                    notesInput.type = 'hidden';
                    notesInput.name = 'notes';
                    notesInput.value = result.value || '';
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = '<?php echo generateCSRFToken(); ?>';
                    
                    form.appendChild(idInput);
                    form.appendChild(actionInput);
                    form.appendChild(notesInput);
                    form.appendChild(csrfInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function rejectRequest() {
            Swal.fire({
                title: 'ปฏิเสธคำขอ',
                text: 'คุณต้องการปฏิเสธคำขอนี้หรือไม่?',
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'เหตุผลในการปฏิเสธ',
                inputPlaceholder: 'กรอกเหตุผล...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'กรุณากรอกเหตุผลในการปฏิเสธ';
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'ปฏิเสธ',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'approve_request.php';
                    
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'request_id';
                    idInput.value = <?php echo $request_id; ?>;
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'reject';
                    
                    const notesInput = document.createElement('input');
                    notesInput.type = 'hidden';
                    notesInput.name = 'notes';
                    notesInput.value = result.value;
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = '<?php echo generateCSRFToken(); ?>';
                    
                    form.appendChild(idInput);
                    form.appendChild(actionInput);
                    form.appendChild(notesInput);
                    form.appendChild(csrfInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function updateStatus() {
            const nextStatuses = <?php echo json_encode($next_statuses); ?>;
            const statusOptions = {};
            
            // You would need to get status names from PHP
            // This is a simplified version
            nextStatuses.forEach(id => {
                statusOptions[id] = 'Status ' + id;
            });
            
            Swal.fire({
                title: 'เปลี่ยนสถานะ',
                input: 'select',
                inputOptions: statusOptions,
                inputPlaceholder: 'เลือกสถานะใหม่',
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form to update status
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'update_status.php';
                    
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'request_id';
                    idInput.value = <?php echo $request_id; ?>;
                    
                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status_id';
                    statusInput.value = result.value;
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = '<?php echo generateCSRFToken(); ?>';
                    
                    form.appendChild(idInput);
                    form.appendChild(statusInput);
                    form.appendChild(csrfInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function deleteAttachment(attachmentId) {
            Swal.fire({
                title: 'ลบไฟล์แนบ',
                text: 'คุณต้องการลบไฟล์แนบนี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_attachment.php?id=' + attachmentId + '&request_id=<?php echo $request_id; ?>';
                }
            });
        }

        // Display messages
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonText: 'ตกลง'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

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

