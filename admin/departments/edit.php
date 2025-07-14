<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

$page_title = 'แก้ไขแผนก';
$base_url = '../../';

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: edit.php?id=' . $department_id);
        exit();
    }
    
    $department->name = trim($_POST['name']);
    $department->code = trim($_POST['code']);
    $department->description = trim($_POST['description']);
    
    // Validation
    $errors = [];
    
    if (empty($department->name)) {
        $errors[] = 'กรุณากรอกชื่อแผนก';
    } elseif ($department->checkNameExists($department->name, $department_id)) {
        $errors[] = 'ชื่อแผนกนี้มีอยู่แล้ว';
    }
    
    if (!empty($department->code) && $department->checkCodeExists($department->code, $department_id)) {
        $errors[] = 'รหัสแผนกนี้มีอยู่แล้ว';
    }
    
    if (empty($errors)) {
        if ($department->update()) {
            // Log activity
            logActivity($_SESSION['user_id'], 'UPDATE', 'อัปเดตแผนก: ' . $department->name, 'departments', $department->id);
            
            $_SESSION['success'] = 'อัปเดตข้อมูลแผนกเรียบร้อยแล้ว';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูลแผนก';
        }
    }
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">แก้ไขแผนก</h1>
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
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อแผนก <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($department->name); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code" class="form-label">รหัสแผนก</label>
                        <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($department->code); ?>" placeholder="เช่น IT, HR, FIN">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบาย</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="อธิบายรายละเอียดของแผนก"><?php echo htmlspecialchars($department->description); ?></textarea>
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

<?php include '../../includes/footer.php'; ?>