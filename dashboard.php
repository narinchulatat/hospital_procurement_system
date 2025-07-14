<?php
require_once 'includes/functions.php';
checkLogin();

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get statistics
$procurement_request = new ProcurementRequest($db);
$statistics = $procurement_request->getStatistics();

// Get department statistics if user is department head
$department_stats = null;
if ($_SESSION['user_role'] == 'department_head' && $_SESSION['department_id']) {
    $department_stats = $procurement_request->getStatisticsByDepartment($_SESSION['department_id']);
}

// Get recent requests
$recent_requests = null;
if ($_SESSION['user_role'] == 'admin') {
    $recent_requests = $procurement_request->readAll();
} elseif ($_SESSION['user_role'] == 'department_head') {
    $recent_requests = $procurement_request->readByDepartment($_SESSION['department_id']);
} else {
    $recent_requests = $procurement_request->readByUser($_SESSION['user_id']);
}

// Get current budget year
$budget_year = new BudgetYear($db);
$current_budget_year = $budget_year->getCurrentBudgetYear();

$page_title = 'Dashboard';
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-hospital text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-white text-lg font-semibold">ระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</div>
                        <div class="text-blue-200 text-sm">โรงพยาบาล</div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="text-white">
                        <span class="text-sm">สวัสดี, <?php echo $_SESSION['user_name']; ?></span>
                        <span class="text-blue-200 text-xs ml-2">(<?php echo $_SESSION['user_role']; ?>)</span>
                    </div>
                    <div class="relative">
                        <button id="user-menu" class="flex items-center text-white hover:text-blue-200 focus:outline-none">
                            <i class="fas fa-user-circle text-xl"></i>
                            <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                        <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>ข้อมูลส่วนตัว
                            </a>
                            <a href="change_password.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-key mr-2"></i>เปลี่ยนรหัสผ่าน
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>ออกจากระบบ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="flex h-screen">
        <div class="w-64 bg-white shadow-lg">
            <div class="p-4">
                <nav class="space-y-2">
                    <a href="dashboard.php" class="flex items-center px-4 py-2 text-gray-700 bg-blue-50 border-r-4 border-blue-500 font-medium">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    
                    <?php if ($_SESSION['user_role'] == 'staff' || $_SESSION['user_role'] == 'department_head'): ?>
                    <a href="request_form.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                        <i class="fas fa-plus-circle mr-3"></i>
                        ยื่นคำขอใหม่
                    </a>
                    <?php endif; ?>
                    
                    <a href="requests.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                        <i class="fas fa-list mr-3"></i>
                        รายการคำขอ
                    </a>
                    
                    <?php if ($_SESSION['user_role'] == 'department_head' || $_SESSION['user_role'] == 'admin'): ?>
                    <a href="approvals.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                        <i class="fas fa-check-circle mr-3"></i>
                        อนุมัติคำขอ
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['user_role'] == 'admin'): ?>
                    <div class="pt-4">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">จัดการระบบ</div>
                        <a href="users.php" class="flex items-center px-4 py-2 mt-2 text-gray-700 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-users mr-3"></i>
                            ผู้ใช้งาน
                        </a>
                        <a href="departments.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-building mr-3"></i>
                            แผนก
                        </a>
                        <a href="items.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-boxes mr-3"></i>
                            รายการครุภัณฑ์
                        </a>
                        <a href="budget_years.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-calendar-alt mr-3"></i>
                            ปีงบประมาณ
                        </a>
                        <a href="reports.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-chart-bar mr-3"></i>
                            รายงาน
                        </a>
                        <a href="logs.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-history mr-3"></i>
                            บันทึกกิจกรรม
                        </a>
                    </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600">ภาพรวมระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</p>
                </div>

                <!-- Current Budget Year Info -->
                <?php if ($current_budget_year): ?>
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900">ปีงบประมาณ <?php echo $budget_year->year_be; ?></h3>
                            <p class="text-blue-700">
                                ช่วงการขอ: <?php echo formatThaiDate($budget_year->request_start_date); ?> - <?php echo formatThaiDate($budget_year->request_end_date); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-file-alt text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-500">คำขอทั้งหมด</h4>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($statistics['total_requests']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-500">รอการอนุมัติ</h4>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($statistics['pending_requests']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-500">อนุมัติแล้ว</h4>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($statistics['approved_requests']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <i class="fas fa-times-circle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-500">ปฏิเสธ</h4>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($statistics['rejected_requests']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">คำขอล่าสุด</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เลขที่คำขอ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รายการ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แผนก</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวนเงิน</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $count = 0;
                                while ($row = $recent_requests->fetch(PDO::FETCH_ASSOC)) : 
                                    if ($count >= 10) break;
                                    $count++;
                                    
                                    $status = new Status($db);
                                    $status_class = $status->getStatusBadgeClass($row['status_name']);
                                    $status_text = $status->getStatusTranslation($row['status_name']);
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['request_number']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($row['item_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($row['department_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ฿<?php echo number_format($row['total_price'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo formatThaiDate($row['created_at']); ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // User dropdown toggle
        document.getElementById('user-menu').addEventListener('click', function() {
            document.getElementById('user-dropdown').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('user-dropdown');
            const button = document.getElementById('user-menu');
            
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Display success message
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonText: 'ตกลง'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </script>
</body>
</html>