<?php
session_start();

// Include database connection
require_once __DIR__ . '/../config/database_sqlite.php';

// Include models
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/BudgetYear.php';
require_once __DIR__ . '/../models/ProcurementRequest.php';
require_once __DIR__ . '/../models/Status.php';
require_once __DIR__ . '/../models/Attachment.php';
require_once __DIR__ . '/../models/ActivityLog.php';

// Security functions
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function checkLogin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        header('Location: login.php');
        exit();
    }
}

function checkRole($required_roles) {
    if (!in_array($_SESSION['user_role'], $required_roles)) {
        header('Location: unauthorized.php');
        exit();
    }
}

function logActivity($user_id, $action, $description, $table_name = null, $record_id = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $activity_log = new ActivityLog($db);
    $activity_log->user_id = $user_id;
    $activity_log->action = $action;
    $activity_log->description = $description;
    $activity_log->table_name_ref = $table_name;
    $activity_log->record_id = $record_id;
    $activity_log->ip_address = $_SERVER['REMOTE_ADDR'];
    $activity_log->user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $activity_log->create();
}

// File upload security
function validateFileUpload($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size = 10 * 1024 * 1024; // 10MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    if ($file['size'] > $max_size) {
        return false;
    }
    
    return true;
}

function uploadFile($file, $destination) {
    if (!validateFileUpload($file)) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $upload_path = $destination . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $filename;
    }
    
    return false;
}

// Date and time functions
function formatThaiDate($date) {
    $months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
        4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
        7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
        10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp) + 543;
    
    return "$day $month $year";
}

function formatThaiDateTime($datetime) {
    $date_part = formatThaiDate($datetime);
    $time_part = date('H:i', strtotime($datetime));
    return "$date_part เวลา $time_part น.";
}
?>