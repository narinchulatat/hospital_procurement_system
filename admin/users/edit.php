<?php
require_once '../../includes/functions.php';
checkLogin();
checkRole(['admin']);

$page_title = 'แก้ไขผู้ใช้งาน';
$base_url = '../../';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get user ID
$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้งาน';
    header('Location: index.php');
    exit();
}

// Get user data
$user = new User($db);
$user->id = $user_id;
if (!$user->readOne()) {
    $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้งาน';
    header('Location: index.php');
    exit();
}

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
        header('Location: edit.php?id=' . $user_id);
        exit();
    }
    
    $user->username = trim($_POST['username']);
    $user->email = trim($_POST['email']);
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
    } elseif ($user->checkUsernameExists($user->username, $user_id)) {
        $errors[] = 'ชื่อผู้ใช้นี้มีอยู่แล้ว';
    }
    
    if (empty($user->email)) {
        $errors[] = 'กรุณากรอกอีเมล';
    } elseif (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
    } elseif ($user->checkEmailExists($user->email, $user_id)) {
        $errors[] = 'อีเมลนี้มีอยู่แล้ว';
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
    
    // Handle password change if provided
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) {
            $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
        } elseif ($_POST['password'] !== $_POST['confirm_password']) {
            $errors[] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
        }
    }
    
    if (empty($errors)) {
        // Update password if provided
        if (!empty($_POST['password'])) {
            $user->changePassword($_POST['password']);
        }
        
        if ($user->update()) {
            // Log activity
            logActivity($_SESSION['user_id'], 'UPDATE', 'อัปเดตผู้ใช้งาน: ' . $user->first_name . ' ' . $user->last_name, 'users', $user->id);
            
            $_SESSION['success'] = 'อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูลผู้ใช้งาน';
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
    <h1 class="h3 mb-0">แก้ไขผู้ใช้งาน</h1>
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
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user->username); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">รหัสผ่านใหม่</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="ปล่อยว่างหากไม่ต้องการเปลี่ยน">
                        <div class="form-text">ปล่อยว่างหากไม่ต้องการเปลี่ยนรหัสผ่าน</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="ปล่อยว่างหากไม่ต้องการเปลี่ยน">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">ชื่อ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user->first_name); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user->last_name); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>">
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
                                <option value="<?php echo $dept['id']; ?>" <?php echo $user->department_id == $dept['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['name']); ?>
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
                                <option value="<?php echo $role_data['id']; ?>" <?php echo $user->role_id == $role_data['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role_data['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo $user->is_active ? 'checked' : ''; ?>>
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

