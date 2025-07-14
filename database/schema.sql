-- Hospital Procurement System Database Schema
-- Character set: utf8mb4 for full UTF-8 support including Thai characters

CREATE DATABASE IF NOT EXISTS hospital_procurement CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hospital_procurement;

-- Roles table
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Departments table (main departments)
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sub-departments table
CREATE TABLE sub_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role_id INT NOT NULL,
    department_id INT,
    sub_department_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (sub_department_id) REFERENCES sub_departments(id)
);

-- Budget years table
CREATE TABLE budget_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year_be INT NOT NULL UNIQUE, -- Buddhist Era year (พ.ศ.)
    year_ad INT NOT NULL, -- Gregorian year (ค.ศ.)
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    request_start_date DATE,
    request_end_date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Items table (central pricing)
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    unit VARCHAR(50),
    unit_price DECIMAL(12,2) NOT NULL,
    category VARCHAR(100),
    specifications TEXT,
    budget_year_id INT NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (budget_year_id) REFERENCES budget_years(id)
);

-- Statuses table
CREATE TABLE statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Procurement requests table
CREATE TABLE procurement_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    department_id INT NOT NULL,
    sub_department_id INT,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    request_type ENUM('new', 'replacement') NOT NULL,
    old_equipment_code VARCHAR(100), -- for replacement requests
    reason TEXT NOT NULL,
    budget_year_id INT NOT NULL,
    status_id INT NOT NULL,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    approval_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (sub_department_id) REFERENCES sub_departments(id),
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (budget_year_id) REFERENCES budget_years(id),
    FOREIGN KEY (status_id) REFERENCES statuses(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- Attachments table
CREATE TABLE attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    procurement_request_id INT NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (procurement_request_id) REFERENCES procurement_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Activity logs table (audit trail)
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('admin', 'ผู้ดูแลระบบ - มีสิทธิ์เข้าถึงข้อมูลทั้งหมด'),
('department_head', 'หัวหน้าแผนก - เห็นเฉพาะข้อมูลแผนกตนเอง'),
('procurement_staff', 'เจ้าหน้าที่จัดซื้อ - เห็นเฉพาะรายการอนุมัติแล้ว'),
('staff', 'เจ้าหน้าที่ทั่วไป - สามารถยื่นคำขอได้');

-- Insert default statuses
INSERT INTO statuses (name, description, sort_order) VALUES
('pending', 'รอการอนุมัติ', 1),
('approved', 'อนุมัติแล้ว', 2),
('rejected', 'ปฏิเสธ', 3),
('procurement_pending', 'รอจัดซื้อ', 4),
('document_preparation', 'จัดทำเอกสาร', 5),
('quotation_request', 'ขอใบเสนอราคา', 6),
('purchased', 'จัดซื้อแล้ว', 7),
('delivery_pending', 'รอส่งมอบ', 8),
('delivered', 'ส่งมอบแล้ว', 9);

-- Insert sample departments
INSERT INTO departments (name, code, description) VALUES
('แผนกเวชศาสตร์ฟื้นฟู', 'REHAB', 'แผนกเวชศาสตร์ฟื้นฟู'),
('แผนกศัลยกรรม', 'SURG', 'แผนกศัลยกรรม'),
('แผนกอายุรกรรม', 'MED', 'แผนกอายุรกรรม'),
('แผนกเทคโนโลยีสารสนเทศ', 'IT', 'แผนกเทคโนโลยีสารสนเทศ'),
('แผนกเภสัชกรรม', 'PHARM', 'แผนกเภสัชกรรม');

-- Insert sample sub-departments
INSERT INTO sub_departments (department_id, name, code, description) VALUES
(1, 'กายภาพบำบัด', 'PT', 'หน่วยกายภาพบำบัด'),
(1, 'กิจกรรมบำบัด', 'OT', 'หน่วยกิจกรรมบำบัด'),
(2, 'ศัลยกรรมทั่วไป', 'GS', 'หน่วยศัลยกรรมทั่วไป'),
(2, 'ศัลยกรรมกระดูก', 'ORTHO', 'หน่วยศัลยกรรมกระดูก'),
(4, 'ระบบเครือข่าย', 'NET', 'หน่วยระบบเครือข่าย'),
(4, 'ฮาร์ดแวร์', 'HW', 'หน่วยฮาร์ดแวร์');

-- Insert sample budget year
INSERT INTO budget_years (year_be, year_ad, start_date, end_date, request_start_date, request_end_date, is_active) VALUES
(2567, 2024, '2023-10-01', '2024-09-30', '2023-10-01', '2024-03-31', TRUE),
(2568, 2025, '2024-10-01', '2025-09-30', '2024-10-01', '2025-03-31', FALSE);

-- Insert sample items
INSERT INTO items (code, name, description, unit, unit_price, category, specifications, budget_year_id, is_available) VALUES
('COMP-001', 'คอมพิวเตอร์ตั้งโต๊ะ', 'คอมพิวเตอร์ตั้งโต๊ะสำหรับงานทั่วไป', 'เครื่อง', 25000.00, 'คอมพิวเตอร์', 'CPU: Intel Core i5, RAM: 8GB, SSD: 256GB', 1, TRUE),
('COMP-002', 'คอมพิวเตอร์โน๊ตบุ๊ค', 'คอมพิวเตอร์โน๊ตบุ๊คสำหรับงานเคลื่อนที่', 'เครื่อง', 30000.00, 'คอมพิวเตอร์', 'CPU: Intel Core i5, RAM: 8GB, SSD: 512GB', 1, TRUE),
('PRINT-001', 'เครื่องพิมพ์เลเซอร์', 'เครื่องพิมพ์เลเซอร์ขาวดำ', 'เครื่อง', 8000.00, 'เครื่องพิมพ์', 'ความเร็ว: 25 หน้า/นาที, ความละเอียด: 1200x1200 dpi', 1, TRUE),
('PRINT-002', 'เครื่องพิมพ์อิงค์เจ็ท', 'เครื่องพิมพ์อิงค์เจ็ทสี', 'เครื่อง', 12000.00, 'เครื่องพิมพ์', 'ความเร็ว: 15 หน้า/นาที, พิมพ์สีได้', 1, TRUE),
('MON-001', 'จอภาพ LCD', 'จอภาพ LCD ขนาด 24 นิ้ว', 'เครื่อง', 7000.00, 'จอภาพ', 'ขนาด: 24", ความละเอียด: 1920x1080', 1, TRUE);

-- Insert admin user (password: admin123)
INSERT INTO users (username, password_hash, email, first_name, last_name, role_id, department_id) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hospital.com', 'ผู้ดูแลระบบ', 'Admin', 1, 4);

-- Create indexes for better performance
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_users_department_id ON users(department_id);
CREATE INDEX idx_procurement_requests_user_id ON procurement_requests(user_id);
CREATE INDEX idx_procurement_requests_department_id ON procurement_requests(department_id);
CREATE INDEX idx_procurement_requests_status_id ON procurement_requests(status_id);
CREATE INDEX idx_procurement_requests_budget_year_id ON procurement_requests(budget_year_id);
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at);