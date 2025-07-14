<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

$page_title = 'แก้ไขบทบาท';
$base_url = '../../';

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

// Get role data
$role = new Role($db);
$role->id = $role_id;
if (!$role->readOne()) {
    $_SESSION['error'] = 'ไม่พบข้อมูลบทบาท';
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: edit.php?id=' . $role_id);
        exit();
    }
    
    $role->name = trim($_POST['name']);
    $role->description = trim($_POST['description']);
    
    // Validation
    $errors = [];
    
    if (empty($role->name)) {
        $errors[] = 'กรุณากรอกชื่อบทบาท';
    } elseif ($role->checkNameExists($role->name, $role_id)) {
        $errors[] = 'ชื่อบทบาทนี้มีอยู่แล้ว';
    }
    
    if (empty($errors)) {
        if ($role->update()) {
            // Log activity
            logActivity($_SESSION['user_id'], 'UPDATE', 'อัปเดตบทบาท: ' . $role->name, 'roles', $role->id);
            
            $_SESSION['success'] = 'อัปเดตข้อมูลบทบาทเรียบร้อยแล้ว';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูลบทบาท';
        }
    }
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include '../../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">แก้ไขบทบาท</h1>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>กลับ
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>เกิดข้อผิดพลาด:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" data-validate>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อบทบาท <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($role->name); ?>" required>
                <div class="form-text">เช่น พนักงาน, หัวหน้าแผนก, ผู้ดูแลระบบ</div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบาย</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="อธิบายหน้าที่และความรับผิดชอบของบทบาทนี้"><?php echo htmlspecialchars($role->description); ?></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>บันทึก
                </button>
            </div>
        </form>
    </div>
</div>

