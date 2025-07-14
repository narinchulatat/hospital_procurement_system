<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get user ID
$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้งาน';
    header('Location: index.php');
    exit();
}

// Prevent self-deletion
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = 'ไม่สามารถลบผู้ใช้งานตัวเองได้';
    header('Location: index.php');
    exit();
}

// Get user data
$user = new User($db);
$user->id = $user_id;
if (!$user->readOne()) {
    $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้งาน';
    header('Location: index.php');
    exit();
}

// Check if user has procurement requests
$procurement_request = new ProcurementRequest($db);
$has_requests = $procurement_request->checkUserHasRequests($user_id);

if ($has_requests) {
    $_SESSION['error'] = 'ไม่สามารถลบผู้ใช้งานที่มีคำขอจัดซื้อในระบบได้';
    header('Location: index.php');
    exit();
}

// Perform deletion
$user_name = $user->first_name . ' ' . $user->last_name;

if ($user->delete()) {
    // Log activity
    logActivity($_SESSION['user_id'], 'DELETE', 'ลบผู้ใช้งาน: ' . $user_name, 'users', $user_id);
    
    $_SESSION['success'] = 'ลบผู้ใช้งาน ' . $user_name . ' เรียบร้อยแล้ว';
} else {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการลบผู้ใช้งาน';
}

header('Location: index.php');
exit();
?>