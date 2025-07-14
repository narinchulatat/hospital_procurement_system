<?php
// Simple test script to verify system functionality
echo "<!DOCTYPE html>\n<html lang='th'>\n<head>\n<meta charset='UTF-8'>\n<title>System Test</title>\n<style>body{font-family:'Sarabun',sans-serif;margin:20px;}</style>\n</head>\n<body>\n";

echo "<h1>ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์ - การทดสอบระบบ</h1>\n";

// Test 1: Database connection
echo "<h2>1. ทดสอบการเชื่อมต่อฐานข้อมูล</h2>\n";
try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    echo "<p class='text-green-600'>✓ เชื่อมต่อฐานข้อมูลสำเร็จ</p>\n";
} catch (Exception $e) {
    echo "<p class='text-red-600'>✗ เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $e->getMessage() . "</p>\n";
}

// Test 2: Check required tables
echo "<h2>2. ทดสอบโครงสร้างฐานข้อมูล</h2>\n";
$required_tables = ['users', 'roles', 'departments', 'sub_departments', 'items', 'budget_years', 'procurement_requests', 'statuses', 'attachments', 'activity_logs'];
foreach ($required_tables as $table) {
    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "<p class='text-green-600'>✓ ตาราง $table: $count รายการ</p>\n";
    } catch (Exception $e) {
        echo "<p class='text-red-600'>✗ ตาราง $table: ไม่พบ</p>\n";
    }
}

// Test 3: Check models
echo "<h2>3. ทดสอบ Models</h2>\n";
$models = ['User', 'Role', 'Department', 'Item', 'BudgetYear', 'ProcurementRequest', 'Status', 'Attachment', 'ActivityLog'];
foreach ($models as $model) {
    try {
        require_once "models/$model.php";
        $obj = new $model($db);
        echo "<p class='text-green-600'>✓ Model $model: โหลดสำเร็จ</p>\n";
    } catch (Exception $e) {
        echo "<p class='text-red-600'>✗ Model $model: " . $e->getMessage() . "</p>\n";
    }
}

// Test 4: Test authentication
echo "<h2>4. ทดสอบระบบ Authentication</h2>\n";
try {
    $user = new User($db);
    $auth_result = $user->authenticate('admin', 'admin123');
    if ($auth_result) {
        echo "<p class='text-green-600'>✓ ทดสอบ login ด้วย admin/admin123: สำเร็จ</p>\n";
    } else {
        echo "<p style='color: red;'>✗ ทดสอบ login ด้วย admin/admin123: ล้มเหลว</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ ทดสอบ Authentication: " . $e->getMessage() . "</p>\n";
}

// Test 5: Check essential functions
echo "<h2>5. ทดสอบฟังก์ชันสำคัญ</h2>\n";
try {
    require_once 'includes/functions.php';
    
    // Test CSRF token
    $token = generateCSRFToken();
    echo "<p style='color: green;'>✓ CSRF Token: " . substr($token, 0, 10) . "...</p>\n";
    
    // Test date formatting
    $thai_date = formatThaiDate('2024-01-15');
    echo "<p style='color: green;'>✓ Thai Date Format: $thai_date</p>\n";
    
    // Test input sanitization
    $clean_input = sanitizeInput('<script>alert("test")</script>');
    echo "<p style='color: green;'>✓ Input Sanitization: $clean_input</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ ฟังก์ชันสำคัญ: " . $e->getMessage() . "</p>\n";
}

// Test 6: Check file permissions
echo "<h2>6. ทดสอบสิทธิ์ไฟล์</h2>\n";
$directories = ['uploads/', 'uploads/requests/'];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    if (is_writable($dir)) {
        echo "<p style='color: green;'>✓ ไดเรกทอรี $dir: สามารถเขียนได้</p>\n";
    } else {
        echo "<p style='color: red;'>✗ ไดเรกทอรี $dir: ไม่สามารถเขียนได้</p>\n";
    }
}

// Test 7: Check PHP extensions
echo "<h2>7. ทดสอบ PHP Extensions</h2>\n";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'fileinfo'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ Extension $ext: ติดตั้งแล้ว</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Extension $ext: ไม่ได้ติดตั้ง</p>\n";
    }
}

echo "<hr>\n";
echo "<h2>สรุปผลการทดสอบ</h2>\n";
echo "<p>หากทุกข้อแสดงเครื่องหมาย ✓ (สีเขียว) แสดงว่าระบบพร้อมใช้งาน</p>\n";
echo "<p>หากมีข้อใดแสดงเครื่องหมาย ✗ (สีแดง) กรุณาแก้ไขก่อนใช้งาน</p>\n";
echo "<p><a href='login.php' style='color: blue;'>→ เข้าสู่ระบบ</a></p>\n";

echo "</body>\n</html>";
?>