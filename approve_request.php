<?php
require_once 'includes/functions.php';
checkLogin();
checkRole(['admin', 'department_head']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: requests.php');
        exit();
    }

    $request_id = (int)$_POST['request_id'];
    $action = sanitizeInput($_POST['action']);
    $notes = sanitizeInput($_POST['notes']);

    // Get database connection
    $database = new Database();
    $db = $database->getConnection();

    // Get request details
    $procurement_request = new ProcurementRequest($db);
    $procurement_request->id = $request_id;
    if (!$procurement_request->readOne()) {
        $_SESSION['error'] = 'ไม่พบคำขอที่ระบุ';
        header('Location: requests.php');
        exit();
    }

    // Check permission
    if ($_SESSION['user_role'] == 'department_head' && $procurement_request->department_id != $_SESSION['department_id']) {
        $_SESSION['error'] = 'คุณไม่มีสิทธิ์อนุมัติคำขอนี้';
        header('Location: requests.php');
        exit();
    }

    // Check if request is still pending
    if ($procurement_request->status_id != 1) {
        $_SESSION['error'] = 'คำขอนี้ได้รับการดำเนินการแล้ว';
        header('Location: request_detail.php?id=' . $request_id);
        exit();
    }

    $success = false;
    $message = '';

    if ($action == 'approve') {
        if ($procurement_request->approve($_SESSION['user_id'], $notes)) {
            $success = true;
            $message = 'อนุมัติคำขอเรียบร้อยแล้ว';
            
            // Log activity
            logActivity($_SESSION['user_id'], 'approve_request', 
                       'อนุมัติคำขอเลขที่: ' . $procurement_request->request_number, 
                       'procurement_requests', $request_id);
        } else {
            $message = 'เกิดข้อผิดพลาดในการอนุมัติคำขอ';
        }
    } elseif ($action == 'reject') {
        if (empty($notes)) {
            $_SESSION['error'] = 'กรุณากรอกเหตุผลในการปฏิเสธ';
            header('Location: request_detail.php?id=' . $request_id);
            exit();
        }
        
        if ($procurement_request->reject($_SESSION['user_id'], $notes)) {
            $success = true;
            $message = 'ปฏิเสธคำขอเรียบร้อยแล้ว';
            
            // Log activity
            logActivity($_SESSION['user_id'], 'reject_request', 
                       'ปฏิเสธคำขอเลขที่: ' . $procurement_request->request_number . ' เหตุผล: ' . $notes, 
                       'procurement_requests', $request_id);
        } else {
            $message = 'เกิดข้อผิดพลาดในการปฏิเสธคำขอ';
        }
    } else {
        $_SESSION['error'] = 'การดำเนินการไม่ถูกต้อง';
        header('Location: request_detail.php?id=' . $request_id);
        exit();
    }

    if ($success) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = $message;
    }

    header('Location: request_detail.php?id=' . $request_id);
    exit();
} else {
    header('Location: requests.php');
    exit();
}
?>