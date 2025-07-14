<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

$page_title = 'แก้ไขรายการครุภัณฑ์';
$base_url = '../../';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get item ID
$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    $_SESSION['error'] = 'ไม่พบข้อมูลรายการครุภัณฑ์';
    header('Location: index.php');
    exit();
}

// Get item data
$item = new Item($db);
$item->id = $item_id;
if (!$item->readOne()) {
    $_SESSION['error'] = 'ไม่พบข้อมูลรายการครุภัณฑ์';
    header('Location: index.php');
    exit();
}

// Get budget years
$budget_year = new BudgetYear($db);
$budget_years = $budget_year->readAll();

// Get categories
$categories = $item->getCategories();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: edit.php?id=' . $item_id);
        exit();
    }
    
    $item->code = trim($_POST['code']);
    $item->name = trim($_POST['name']);
    $item->description = trim($_POST['description']);
    $item->unit = trim($_POST['unit']);
    $item->unit_price = floatval($_POST['unit_price']);
    $item->category = trim($_POST['category']);
    $item->specifications = trim($_POST['specifications']);
    $item->budget_year_id = !empty($_POST['budget_year_id']) ? $_POST['budget_year_id'] : null;
    $item->is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Validation
    $errors = [];
    
    if (empty($item->code)) {
        $errors[] = 'กรุณากรอกรหัสรายการ';
    } elseif ($item->checkCodeExists($item->code, $item_id)) {
        $errors[] = 'รหัสรายการนี้มีอยู่แล้ว';
    }
    
    if (empty($item->name)) {
        $errors[] = 'กรุณากรอกชื่อรายการ';
    } elseif ($item->checkNameExists($item->name, $item_id)) {
        $errors[] = 'ชื่อรายการนี้มีอยู่แล้ว';
    }
    
    if (empty($item->unit)) {
        $errors[] = 'กรุณากรอกหน่วย';
    }
    
    if (empty($item->unit_price) || $item->unit_price <= 0) {
        $errors[] = 'กรุณากรอกราคาต่อหน่วยที่ถูกต้อง';
    }
    
    if (empty($item->category)) {
        $errors[] = 'กรุณากรอกหมวดหมู่';
    }
    
    if (empty($errors)) {
        if ($item->update()) {
            // Log activity
            logActivity($_SESSION['user_id'], 'UPDATE', 'อัปเดตรายการครุภัณฑ์: ' . $item->name, 'items', $item->id);
            
            $_SESSION['success'] = 'อัปเดตข้อมูลรายการครุภัณฑ์เรียบร้อยแล้ว';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูลรายการครุภัณฑ์';
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
    <h1 class="h3 mb-0">แก้ไขรายการครุภัณฑ์</h1>
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
                        <label for="code" class="form-label">รหัสรายการ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($item->code); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อรายการ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($item->name); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบาย</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($item->description); ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="unit" class="form-label">หน่วย <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="unit" name="unit" value="<?php echo htmlspecialchars($item->unit); ?>" placeholder="เช่น ชิ้น, เครื่อง, ชุด" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="unit_price" class="form-label">ราคาต่อหน่วย <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="unit_price" name="unit_price" value="<?php echo htmlspecialchars($item->unit_price); ?>" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="category" class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($item->category); ?>" list="categories" required>
                        <datalist id="categories">
                            <?php 
                            $categories->execute();
                            while ($cat = $categories->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>">
                            <?php endwhile; ?>
                        </datalist>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="specifications" class="form-label">คุณสมบัติ/สเปค</label>
                <textarea class="form-control" id="specifications" name="specifications" rows="4" placeholder="รายละเอียดคุณสมบัติและสเปคของรายการ"><?php echo htmlspecialchars($item->specifications); ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="budget_year_id" class="form-label">ปีงบประมาณ</label>
                        <select class="form-select" id="budget_year_id" name="budget_year_id">
                            <option value="">-- เลือกปีงบประมาณ --</option>
                            <?php 
                            $budget_years->execute();
                            while ($year = $budget_years->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?php echo $year['id']; ?>" <?php echo $item->budget_year_id == $year['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($year['year_be']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" <?php echo $item->is_available ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_available">
                                พร้อมใช้งาน
                            </label>
                        </div>
                    </div>
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

<?php include '../../includes/footer.php'; ?>