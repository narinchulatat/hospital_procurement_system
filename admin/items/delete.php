<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get item ID
$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    $_SESSION['error'] = 'ไม่พบข้อมูลรายการครุภัณฑ์';
    header('Location: index.php');
    exit();
}

// Get item data
$item = new Item($db);
$item->id = $item_id;
if (!$item->readOne()) {
    $_SESSION['error'] = 'ไม่พบข้อมูลรายการครุภัณฑ์';
    header('Location: index.php');
    exit();
}

// Check if item has procurement requests
$query = "SELECT COUNT(*) as count FROM procurement_requests WHERE item_id = :item_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':item_id', $item_id);
$stmt->execute();
$request_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($request_count > 0) {
    $_SESSION['error'] = 'ไม่สามารถลบรายการครุภัณฑ์ที่มีคำขอจัดซื้อในระบบได้ (มีคำขอ ' . $request_count . ' คำขอ)';
    header('Location: index.php');
    exit();
}

// Perform deletion
$item_name = $item->name;

if ($item->delete()) {
    // Log activity
    logActivity($_SESSION['user_id'], 'DELETE', 'ลบรายการครุภัณฑ์: ' . $item_name, 'items', $item_id);
    
    $_SESSION['success'] = 'ลบรายการครุภัณฑ์ ' . $item_name . ' เรียบร้อยแล้ว';
} else {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการลบรายการครุภัณฑ์';
}

header('Location: index.php');
exit();
?>