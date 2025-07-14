<?php
require_once 'includes/functions.php';

// Log the logout activity
if (isset($_SESSION['user_id'])) {
    logActivity($_SESSION['user_id'], 'logout', 'ออกจากระบบ');
}

// Destroy session
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: login.php');
exit();
?>