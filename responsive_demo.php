<?php
// Demo page for responsive design - converted to use proper PHP includes structure
// This shows the dashboard with proper component structure

// Mock session data for demo purposes
session_start();
if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'ผู้ดูแลระบบ';
    $_SESSION['user_role'] = 'admin';
    $_SESSION['user_id'] = 1;
    $_SESSION['department_id'] = 1;
}

$page_title = 'Dashboard (Responsive Demo)';
$base_url = '';
include 'includes/sidebar.php';
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600">ภาพรวมระบบจัดซื้อครุภัณฑ์คอมพิวเตอร์</p>
</div>

<!-- Current Budget Year Info -->
<div class="mb-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-calendar-alt text-blue-600 text-xl mr-3"></i>
            <div>
                <h5 class="font-semibold text-blue-900">ปีงบประมาณ 2567</h5>
                <p class="text-blue-700">
                    ช่วงการขอ: 1 ตุลาคม 2566 - 30 กันยายน 2567
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">คำขอทั้งหมด</p>
                <p class="text-2xl font-bold text-gray-900">128</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-full">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">รอการอนุมัติ</p>
                <p class="text-2xl font-bold text-gray-900">24</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">อนุมัติแล้ว</p>
                <p class="text-2xl font-bold text-gray-900">89</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-red-100 rounded-full">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">ปฏิเสธ</p>
                <p class="text-2xl font-bold text-gray-900">15</p>
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
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">REQ-2024-001</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">เครื่องคอมพิวเตอร์ตั้งโต๊ะ</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">แผนกไอที</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">฿25,000.00</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            รอการอนุมัติ
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">15 ม.ค. 2567</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">REQ-2024-002</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">เครื่องพิมพ์เลเซอร์</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">แผนกการเงิน</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">฿15,000.00</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            อนุมัติแล้ว
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">14 ม.ค. 2567</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">REQ-2024-003</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">หน้าจอ LCD 24 นิ้ว</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">แผนกบุคคล</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">฿8,500.00</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            อนุมัติแล้ว
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">13 ม.ค. 2567</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

