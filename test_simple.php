<?php
require_once 'includes/functions.php';

$database = new Database();
$db = $database->getConnection();

// Test with full join
$stmt = $db->prepare("SELECT u.id, u.username, u.password_hash, u.first_name, u.last_name, 
                             u.role_id, u.department_id, u.sub_department_id, u.is_active,
                             r.name as role_name, d.name as department_name, sd.name as sub_department_name
                      FROM users u
                      LEFT JOIN roles r ON u.role_id = r.id
                      LEFT JOIN departments d ON u.department_id = d.id
                      LEFT JOIN sub_departments sd ON u.sub_department_id = sd.id
                      WHERE u.username = ?");
$stmt->execute(['admin']);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Full join query result:\n";
print_r($result);
?>