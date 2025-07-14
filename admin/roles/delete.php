<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get role ID
$role_id = $_GET['id'] ?? null;
if (!$role_id) {
    $_SESSION['error'] = 'ไม่พบข้อมูลบทบาท';
    header('Location: index.php');
    exit();
}

// Prevent deletion of default roles
if ($role_id <= 3) {
    $_SESSION['error'] = 'ไม่สามารถลบบทบาทเริ่มต้นของระบบได้';
    header('Location: index.php');
    exit();
}

// Get role data
$role = new Role($db);
$role->id = $role_id;
if (!$role->readOne()) {
    $_SESSION['error'] = 'ไม่พบข้อมูลบทบาท';
    header('Location: index.php');
    exit();
}

// Check if role has users
$query = "SELECT COUNT(*) as count FROM users WHERE role_id = :role_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':role_id', $role_id);
$stmt->execute();
$user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($user_count > 0) {
    $_SESSION['error'] = 'ไม่สามารถลบบทบาทที่มีผู้ใช้งานอยู่ได้ (มีผู้ใช้งาน ' . $user_count . ' คน)';
    header('Location: index.php');
    exit();
}

// Perform deletion
$role_name = $role->name;

if ($role->delete()) {
    // Log activity
    logActivity($_SESSION['user_id'], 'DELETE', 'ลบบทบาท: ' . $role_name, 'roles', $role_id);
    
    $_SESSION['success'] = 'ลบบทบาท ' . $role_name . ' เรียบร้อยแล้ว';
} else {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการลบบทบาท';
}

header('Location: index.php');
exit();
?>