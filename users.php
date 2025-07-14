<?php
require_once 'includes/functions.php';
checkLogin();
checkRole(['admin']);

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get all users
$user = new User($db);
$users = $user->readAll();

// Get all roles
$role = new Role($db);
$roles = $role->readAll();

// Get all departments
$department = new Department($db);
$departments = $department->readAll();

$page_title = 'จัดการผู้ใช้งาน';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">จัดการผู้ใช้งาน</h1>
                    <p class="text-gray-600">จัดการข้อมูลผู้ใช้งานในระบบ</p>
                </div>
                <button onclick="addUser()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>เพิ่มผู้ใช้ใหม่
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">รายชื่อผู้ใช้งาน</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อผู้ใช้งาน</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">บทบาท</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แผนก</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เข้าใช้งานล่าสุด</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($user_row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($user_row['username']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($user_row['first_name'] . ' ' . $user_row['last_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($user_row['role_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($user_row['department_name'] ?: '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $user_row['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $user_row['is_active'] ? 'ใช้งาน' : 'ปิดใช้งาน'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $user_row['last_login'] ? formatThaiDateTime($user_row['last_login']) : '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="editUser(<?php echo $user_row['id']; ?>)" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="toggleUserStatus(<?php echo $user_row['id']; ?>, <?php echo $user_row['is_active'] ? 'false' : 'true'; ?>)" 
                                            class="text-orange-600 hover:text-orange-900">
                                        <i class="fas fa-<?php echo $user_row['is_active'] ? 'ban' : 'check'; ?>"></i>
                                    </button>
                                    <button onclick="resetPassword(<?php echo $user_row['id']; ?>)" 
                                            class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function addUser() {
            Swal.fire({
                title: 'เพิ่มผู้ใช้ใหม่',
                html: `
                    <div class="grid grid-cols-1 gap-4 text-left">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อผู้ใช้งาน</label>
                            <input type="text" id="username" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน</label>
                            <input type="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ</label>
                            <input type="text" id="first_name" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">นามสกุล</label>
                            <input type="text" id="last_name" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                            <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">บทบาท</label>
                            <select id="role_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">เลือกบทบาท</option>
                                <?php 
                                $roles->execute();
                                while ($role_row = $roles->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                <option value="<?php echo $role_row['id']; ?>"><?php echo htmlspecialchars($role_row['description']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">แผนก</label>
                            <select id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">เลือกแผนก</option>
                                <?php 
                                $departments->execute();
                                while ($dept_row = $departments->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                <option value="<?php echo $dept_row['id']; ?>"><?php echo htmlspecialchars($dept_row['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                preConfirm: () => {
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    const first_name = document.getElementById('first_name').value;
                    const last_name = document.getElementById('last_name').value;
                    const email = document.getElementById('email').value;
                    const role_id = document.getElementById('role_id').value;
                    const department_id = document.getElementById('department_id').value;
                    
                    if (!username || !password || !first_name || !last_name || !role_id) {
                        Swal.showValidationMessage('กรุณากรอกข้อมูลให้ครบถ้วน');
                        return false;
                    }
                    
                    return {
                        username, password, first_name, last_name, email, role_id, department_id
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form to add user
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'user_actions.php';
                    
                    Object.keys(result.value).forEach(key => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = result.value[key];
                        form.appendChild(input);
                    });
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'add';
                    form.appendChild(actionInput);
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = '<?php echo generateCSRFToken(); ?>';
                    form.appendChild(csrfInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function editUser(userId) {
            Swal.fire({
                title: 'แก้ไขผู้ใช้งาน',
                text: 'คุณต้องการแก้ไขข้อมูลผู้ใช้งานนี้หรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'แก้ไข',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'edit_user.php?id=' + userId;
                }
            });
        }

        function toggleUserStatus(userId, newStatus) {
            const action = newStatus === 'true' ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            
            Swal.fire({
                title: action + 'ผู้ใช้งาน',
                text: 'คุณต้องการ' + action + 'ผู้ใช้งานนี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ดำเนินการ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'user_actions.php?action=toggle&id=' + userId + '&status=' + newStatus;
                }
            });
        }

        function resetPassword(userId) {
            Swal.fire({
                title: 'รีเซ็ตรหัสผ่าน',
                text: 'คุณต้องการรีเซ็ตรหัสผ่านของผู้ใช้งานนี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'รีเซ็ต',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'user_actions.php?action=reset_password&id=' + userId;
                }
            });
        }

        // Display messages
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonText: 'ตกลง'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'ตกลง'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>