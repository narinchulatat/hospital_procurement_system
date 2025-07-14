<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get department ID
$department_id = $_GET['id'] ?? null;
if (!$department_id) {
    $_SESSION['error'] = 'ไม่พบข้อมูลแผนก';
    header('Location: index.php');
    exit();
}

// Get department data
$department = new Department($db);
$department->id = $department_id;
if (!$department->readOne()) {
    $_SESSION['error'] = 'ไม่พบข้อมูลแผนก';
    header('Location: index.php');
    exit();
}

// Check if department has users
$user = new User($db);
$query = "SELECT COUNT(*) as count FROM users WHERE department_id = :department_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':department_id', $department_id);
$stmt->execute();
$user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($user_count > 0) {
    $_SESSION['error'] = 'ไม่สามารถลบแผนกที่มีผู้ใช้งานอยู่ได้ (มีผู้ใช้งาน ' . $user_count . ' คน)';
    header('Location: index.php');
    exit();
}

// Check if department has sub-departments
$query = "SELECT COUNT(*) as count FROM sub_departments WHERE department_id = :department_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':department_id', $department_id);
$stmt->execute();
$sub_dept_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($sub_dept_count > 0) {
    $_SESSION['error'] = 'ไม่สามารถลบแผนกที่มีหน่วยงานย่อยอยู่ได้ (มีหน่วยงานย่อย ' . $sub_dept_count . ' หน่วย)';
    header('Location: index.php');
    exit();
}

// Perform deletion
$department_name = $department->name;

if ($department->delete()) {
    // Log activity
    logActivity($_SESSION['user_id'], 'DELETE', 'ลบแผนก: ' . $department_name, 'departments', $department_id);
    
    $_SESSION['success'] = 'ลบแผนก ' . $department_name . ' เรียบร้อยแล้ว';
} else {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการลบแผนก';
}

header('Location: index.php');
exit();
?>