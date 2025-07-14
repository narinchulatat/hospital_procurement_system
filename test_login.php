<?php
// Debug login process
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/functions.php';

echo "Testing login process...\n";

// Test database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection failed\n");
}

// Test user query directly
$query = "SELECT u.id, u.username, u.password_hash, u.first_name, u.last_name, 
                 u.role_id, u.department_id, u.sub_department_id, u.is_active,
                 r.name as role_name, d.name as department_name, sd.name as sub_department_name
          FROM users u
          LEFT JOIN roles r ON u.role_id = r.id
          LEFT JOIN departments d ON u.department_id = d.id
          LEFT JOIN sub_departments sd ON u.sub_department_id = sd.id
          WHERE u.username = :username";

$stmt = $db->prepare($query);
$username = 'admin';
$stmt->bindParam(':username', $username);
$stmt->execute();

echo "Query executed. Row count: " . $stmt->rowCount() . "\n";

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "User found:\n";
    print_r($row);
    
    $password_check = password_verify('admin123', $row['password_hash']);
    echo "Password verification: " . ($password_check ? 'TRUE' : 'FALSE') . "\n";
} else {
    echo "No user found with username 'admin' and is_active = 1\n";
}

// Test user authentication
$user = new User($db);
$result = $user->authenticate('admin', 'admin123');

if ($result) {
    echo "Authentication successful!\n";
    print_r($result);
} else {
    echo "Authentication failed\n";
}
?>