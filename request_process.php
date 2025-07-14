<?php
require_once 'includes/functions.php';
checkLogin();
checkRole(['staff', 'department_head']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: request_form.php');
        exit();
    }

    // Get database connection
    $database = new Database();
    $db = $database->getConnection();

    // Validate input
    $department_id = (int)$_POST['department_id'];
    $sub_department_id = !empty($_POST['sub_department_id']) ? (int)$_POST['sub_department_id'] : null;
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    $request_type = sanitizeInput($_POST['request_type']);
    $old_equipment_code = sanitizeInput($_POST['old_equipment_code']);
    $reason = sanitizeInput($_POST['reason']);

    // Validate required fields
    if (empty($department_id) || empty($item_id) || empty($quantity) || empty($reason)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        header('Location: request_form.php');
        exit();
    }

    // Validate request type
    if (!in_array($request_type, ['new', 'replacement'])) {
        $_SESSION['error'] = 'ประเภทการขอไม่ถูกต้อง';
        header('Location: request_form.php');
        exit();
    }

    // Check if old equipment code is provided for replacement
    if ($request_type == 'replacement' && empty($old_equipment_code)) {
        $_SESSION['error'] = 'กรุณากรอกเลขครุภัณฑ์เดิมสำหรับการขอทดแทน';
        header('Location: request_form.php');
        exit();
    }

    // Check if request period is open
    $budget_year = new BudgetYear($db);
    $current_budget_year = $budget_year->getCurrentBudgetYear();
    if (!$current_budget_year || !$budget_year->isRequestPeriodOpen($budget_year->id)) {
        $_SESSION['error'] = 'ไม่อยู่ในช่วงเวลาการยื่นคำขอ';
        header('Location: request_form.php');
        exit();
    }

    // Get item details
    $item = new Item($db);
    $item->id = $item_id;
    if (!$item->readOne()) {
        $_SESSION['error'] = 'ไม่พบรายการครุภัณฑ์ที่เลือก';
        header('Location: request_form.php');
        exit();
    }

    // Calculate total price
    $unit_price = $item->unit_price;
    $total_price = $unit_price * $quantity;

    // Create procurement request
    $procurement_request = new ProcurementRequest($db);
    $procurement_request->user_id = $_SESSION['user_id'];
    $procurement_request->department_id = $department_id;
    $procurement_request->sub_department_id = $sub_department_id;
    $procurement_request->item_id = $item_id;
    $procurement_request->quantity = $quantity;
    $procurement_request->unit_price = $unit_price;
    $procurement_request->total_price = $total_price;
    $procurement_request->request_type = $request_type;
    $procurement_request->old_equipment_code = $old_equipment_code;
    $procurement_request->reason = $reason;
    $procurement_request->budget_year_id = $budget_year->id;
    $procurement_request->status_id = 1; // pending status

    if ($procurement_request->create()) {
        $request_id = $procurement_request->id;
        
        // Handle file attachments
        if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
            $upload_dir = 'uploads/requests/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $attachment = new Attachment($db);
            
            for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
                if ($_FILES['attachments']['error'][$i] == UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['attachments']['name'][$i],
                        'type' => $_FILES['attachments']['type'][$i],
                        'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                        'size' => $_FILES['attachments']['size'][$i]
                    ];
                    
                    if (validateFileUpload($file)) {
                        $stored_filename = uploadFile($file, $upload_dir);
                        
                        if ($stored_filename) {
                            $attachment->procurement_request_id = $request_id;
                            $attachment->original_filename = $file['name'];
                            $attachment->stored_filename = $stored_filename;
                            $attachment->file_size = $file['size'];
                            $attachment->file_type = $file['type'];
                            $attachment->uploaded_by = $_SESSION['user_id'];
                            $attachment->create();
                        }
                    }
                }
            }
        }

        // Log activity
        logActivity($_SESSION['user_id'], 'create_request', 
                   'ยื่นคำขอใหม่เลขที่: ' . $procurement_request->request_number, 
                   'procurement_requests', $request_id);

        $_SESSION['success'] = 'ยื่นคำขอสำเร็จ เลขที่คำขอ: ' . $procurement_request->request_number;
        header('Location: request_detail.php?id=' . $request_id);
        exit();
    } else {
        $_SESSION['error'] = 'เกิดข้อผิดพลาดในการยื่นคำขอ';
        header('Location: request_form.php');
        exit();
    }
} else {
    header('Location: request_form.php');
    exit();
}
?>