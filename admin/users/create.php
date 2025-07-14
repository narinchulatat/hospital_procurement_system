<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

$page_title = 'เพิ่มผู้ใช้งาน';
$base_url = '../../';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get departments and roles
$department = new Department($db);
$departments = $department->readAll();

$role = new Role($db);
$roles = $role->readAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: create.php');
        exit();
    }
    
    $user = new User($db);
    $user->username = trim($_POST['username']);
    $user->email = trim($_POST['email']);
    $user->password = $_POST['password'];
    $user->first_name = trim($_POST['first_name']);
    $user->last_name = trim($_POST['last_name']);
    $user->phone = trim($_POST['phone']);
    $user->department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : null;
    $user->role_id = $_POST['role_id'];
    $user->is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    $errors = [];
    
    if (empty($user->username)) {
        $errors[] = 'กรุณากรอกชื่อผู้ใช้';
    } elseif (strlen($user->username) < 3) {
        $errors[] = 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร';
    } elseif ($user->checkUsernameExists($user->username)) {
        $errors[] = 'ชื่อผู้ใช้นี้มีอยู่แล้ว';
    }
    
    if (empty($user->email)) {
        $errors[] = 'กรุณากรอกอีเมล';
    } elseif (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
    } elseif ($user->checkEmailExists($user->email)) {
        $errors[] = 'อีเมลนี้มีอยู่แล้ว';
    }
    
    if (empty($user->password)) {
        $errors[] = 'กรุณากรอกรหัสผ่าน';
    } elseif (strlen($user->password) < 6) {
        $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
    }
    
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
    }
    
    if (empty($user->first_name)) {
        $errors[] = 'กรุณากรอกชื่อ';
    }
    
    if (empty($user->last_name)) {
        $errors[] = 'กรุณากรอกนามสกุล';
    }
    
    if (empty($user->role_id)) {
        $errors[] = 'กรุณาเลือกบทบาท';
    }
    
    if (empty($errors)) {
        if ($user->create()) {
            // Log activity
            logActivity($_SESSION['user_id'], 'CREATE', 'สร้างผู้ใช้งาน: ' . $user->first_name . ' ' . $user->last_name, 'users', $user->id);
            
            $_SESSION['success'] = 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการเพิ่มผู้ใช้งาน';
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
    <h1 class="h3 mb-0">เพิ่มผู้ใช้งาน</h1>
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
                        <label for="username" class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">ชื่อ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="department_id" class="form-label">แผนก</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">-- เลือกแผนก --</option>
                            <?php 
                            $departments->execute();
                            while ($dept = $departments->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?php echo $dept['id']; ?>" <?php echo ($_POST['department_id'] ?? '') == $dept['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['department_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="role_id" class="form-label">บทบาท <span class="text-danger">*</span></label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">-- เลือกบทบาท --</option>
                            <?php 
                            $roles->execute();
                            while ($role_data = $roles->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?php echo $role_data['id']; ?>" <?php echo ($_POST['role_id'] ?? '') == $role_data['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role_data['role_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo ($_POST['is_active'] ?? '1') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">
                                เปิดใช้งาน
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

