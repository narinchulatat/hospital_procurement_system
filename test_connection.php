<?php
// Test script to check basic functionality
require_once 'includes/functions.php';

// Test database connection
echo "Testing database connection...\n";
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✓ Database connection successful\n";
    
    // Test user query
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ Found " . $result['count'] . " users in database\n";
    
    // Test roles
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM roles");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ Found " . $result['count'] . " roles in database\n";
    
    // Test departments
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM departments");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ Found " . $result['count'] . " departments in database\n";
    
    // Test items
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM items");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ Found " . $result['count'] . " items in database\n";
    
} else {
    echo "✗ Database connection failed\n";
}

echo "\nTesting model loading...\n";
try {
    $user = new User($db);
    echo "✓ User model loaded successfully\n";
    
    $role = new Role($db);
    echo "✓ Role model loaded successfully\n";
    
    $department = new Department($db);
    echo "✓ Department model loaded successfully\n";
    
    $item = new Item($db);
    echo "✓ Item model loaded successfully\n";
    
} catch (Exception $e) {
    echo "✗ Error loading models: " . $e->getMessage() . "\n";
}

echo "\nAll tests completed.\n";
?>