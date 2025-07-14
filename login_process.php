<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: login.php');
        exit();
    }

    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อผู้ใช้งานและรหัสผ่าน';
        header('Location: login.php');
        exit();
    }

    // Database connection
    $database = new Database();
    $db = $database->getConnection();

    // User authentication
    $user = new User($db);
    $user_data = $user->authenticate($username, $password);

    if ($user_data) {
        // Set session variables
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['user_name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
        $_SESSION['user_role'] = $user_data['role_name'];
        $_SESSION['department_id'] = $user_data['department_id'];
        $_SESSION['sub_department_id'] = $user_data['sub_department_id'];
        $_SESSION['department_name'] = $user_data['department_name'];
        $_SESSION['sub_department_name'] = $user_data['sub_department_name'];

        // Log the login activity
        logActivity($user_data['id'], 'login', 'เข้าสู่ระบบ');

        // Remember me functionality
        if (isset($_POST['remember_me'])) {
            $remember_token = bin2hex(random_bytes(32));
            setcookie('remember_token', $remember_token, time() + (86400 * 30), '/'); // 30 days
            
            // Store remember token in database (you might want to add this to user table)
            // For now, we'll just set a longer session
            ini_set('session.cookie_lifetime', 86400 * 30);
        }

        $_SESSION['success'] = 'เข้าสู่ระบบสำเร็จ';
        header('Location: dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = 'ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง';
        
        // Log failed login attempt
        logActivity(null, 'login_failed', 'พยายามเข้าสู่ระบบด้วยชื่อผู้ใช้งาน: ' . $username);
        
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>