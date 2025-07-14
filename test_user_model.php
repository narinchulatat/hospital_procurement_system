<?php
require_once 'includes/functions.php';

$database = new Database();
$db = $database->getConnection();

echo "Database connection: " . ($db ? "SUCCESS" : "FAILED") . "\n";

// Test direct query on the connection
$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute(['admin']);
$direct_result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Direct query result: " . ($direct_result ? "SUCCESS" : "FAILED") . "\n";

$user = new User($db);
$result = $user->authenticate('admin', 'admin123');

if ($result) {
    echo "SUCCESS: Authentication worked!\n";
    print_r($result);
} else {
    echo "FAILED: Authentication failed\n";
}
?>