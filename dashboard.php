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
$base_url = '';
include 'includes/sidebar.php';
?>
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600">ภาพรวมระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</p>
                </div>

                <!-- Current Budget Year Info -->
                <?php if ($current_budget_year): ?>
                <div class="mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-blue-600 text-xl mr-3"></i>
                            <div>
                                <h5 class="font-semibold text-blue-900">ปีงบประมาณ <?php echo $current_budget_year->year_be; ?></h5>
                                <p class="text-blue-700 text-sm">
                                    ช่วงการขอ: <?php echo formatThaiDate($current_budget_year->request_start_date); ?> - <?php echo formatThaiDate($current_budget_year->request_end_date); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <div class="p-3 rounded-full bg-blue-100">
                                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <h6 class="text-sm text-gray-600 mb-2">คำขอทั้งหมด</h6>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo number_format($statistics['total_requests']); ?></h2>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <div class="p-3 rounded-full bg-yellow-100">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                        <h6 class="text-sm text-gray-600 mb-2">รอการอนุมัติ</h6>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo number_format($statistics['pending_requests']); ?></h2>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <div class="p-3 rounded-full bg-green-100">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <h6 class="text-sm text-gray-600 mb-2">อนุมัติแล้ว</h6>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo number_format($statistics['approved_requests']); ?></h2>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <div class="p-3 rounded-full bg-red-100">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                        </div>
                        <h6 class="text-sm text-gray-600 mb-2">ปฏิเสธ</h6>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo number_format($statistics['rejected_requests']); ?></h2>
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900">คำขอล่าสุด</h5>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="recent-requests-table" class="data-table min-w-full divide-y divide-gray-200">
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
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['request_number']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['item_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['department_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">฿<?php echo number_format($row['total_price'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo formatThaiDate($row['created_at']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

