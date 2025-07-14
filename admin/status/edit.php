<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

$page_title = 'แก้ไขสถานะ';
$base_url = '../../';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get status ID
$status_id = $_GET['id'] ?? null;
if (!$status_id) {
    $_SESSION['error'] = 'ไม่พบข้อมูลสถานะ';
    header('Location: index.php');
    exit();
}

// Get status data
$status = new Status($db);
$status->id = $status_id;
if (!$status->readOne()) {
    $_SESSION['error'] = 'ไม่พบข้อมูลสถานะ';
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: edit.php?id=' . $status_id);
        exit();
    }
    
    $status->name = trim($_POST['name']);
    $status->description = trim($_POST['description']);
    $status->sort_order = intval($_POST['sort_order']);
    $status->is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    $errors = [];
    
    if (empty($status->name)) {
        $errors[] = 'กรุณากรอกชื่อสถานะ';
    } elseif ($status->checkNameExists($status->name, $status_id)) {
        $errors[] = 'ชื่อสถานะนี้มีอยู่แล้ว';
    }
    
    if (empty($status->sort_order) || $status->sort_order <= 0) {
        $errors[] = 'กรุณากรอกลำดับการแสดงที่ถูกต้อง';
    }
    
    if (empty($errors)) {
        if ($status->update()) {
            // Log activity
            logActivity($_SESSION['user_id'], 'UPDATE', 'อัปเดตสถานะ: ' . $status->name, 'statuses', $status->id);
            
            $_SESSION['success'] = 'อัปเดตข้อมูลสถานะเรียบร้อยแล้ว';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูลสถานะ';
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
    <h1 class="h3 mb-0">แก้ไขสถานะ</h1>
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
                        <label for="name" class="form-label">ชื่อสถานะ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($status->name); ?>" required>
                        <div class="form-text">ใช้ชื่อภาษาอังกฤษ เช่น pending, approved, rejected</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">ลำดับการแสดง <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($status->sort_order); ?>" min="1" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบาย</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="อธิบายรายละเอียดของสถานะนี้"><?php echo htmlspecialchars($status->description); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">สถานะ</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo $status->is_active ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">
                        เปิดใช้งาน
                    </label>
                </div>
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

