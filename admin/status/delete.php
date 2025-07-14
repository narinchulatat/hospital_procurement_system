<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get status ID
$status_id = $_GET['id'] ?? null;
if (!$status_id) {
    $_SESSION['error'] = 'ไม่พบข้อมูลสถานะ';
    header('Location: index.php');
    exit();
}

// Prevent deletion of default statuses
if ($status_id <= 9) {
    $_SESSION['error'] = 'ไม่สามารถลบสถานะเริ่มต้นของระบบได้';
    header('Location: index.php');
    exit();
}

// Get status data
$status = new Status($db);
$status->id = $status_id;
if (!$status->readOne()) {
    $_SESSION['error'] = 'ไม่พบข้อมูลสถานะ';
    header('Location: index.php');
    exit();
}

// Check if status has procurement requests
$query = "SELECT COUNT(*) as count FROM procurement_requests WHERE status_id = :status_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':status_id', $status_id);
$stmt->execute();
$request_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($request_count > 0) {
    $_SESSION['error'] = 'ไม่สามารถลบสถานะที่มีคำขอจัดซื้อในระบบได้ (มีคำขอ ' . $request_count . ' คำขอ)';
    header('Location: index.php');
    exit();
}

// Perform deletion
$status_name = $status->name;

if ($status->delete()) {
    // Log activity
    logActivity($_SESSION['user_id'], 'DELETE', 'ลบสถานะ: ' . $status_name, 'statuses', $status_id);
    
    $_SESSION['success'] = 'ลบสถานะ ' . $status_name . ' เรียบร้อยแล้ว';
} else {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการลบสถานะ';
}

header('Location: index.php');
exit();
?>