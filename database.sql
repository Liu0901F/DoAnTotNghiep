-- ==========================================
-- Cơ sở dữ liệu: quan_ly_phong_cho_thue
-- ==========================================
CREATE DATABASE IF NOT EXISTS `quan_ly_phong_cho_thue` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `quan_ly_phong_cho_thue`;

-- ==========================================
-- Xóa bảng nếu đã tồn tại (để tránh lỗi khi chạy lại file)
-- Thứ tự xóa ngược với thứ tự tạo (xóa bảng con trước)
-- ==========================================
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `meter_readings`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `contract_members`;
DROP TABLE IF EXISTS `contracts`;
DROP TABLE IF EXISTS `tenants`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `room_types`;
DROP TABLE IF EXISTS `users`;

-- ==========================================
-- TẠO BẢNG
-- ==========================================

-- 1. Bảng users (Tài khoản người dùng)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(15),
  `role` ENUM('admin', 'tenant') DEFAULT 'tenant',
  `status` TINYINT(1) DEFAULT 1 COMMENT '1: Hoạt động, 0: Khóa',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng room_types (Loại phòng)
CREATE TABLE `room_types` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `base_price` DECIMAL(10,2) NOT NULL,
  `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng rooms (Danh sách phòng)
CREATE TABLE `rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_number` VARCHAR(20) NOT NULL UNIQUE,
  `room_type_id` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `status` ENUM('empty', 'rented', 'maintenance') DEFAULT 'empty',
  `max_occupants` INT DEFAULT 2,
  `description` TEXT,
  FOREIGN KEY (`room_type_id`) REFERENCES `room_types`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng tenants (Thông tin chi tiết khách thuê)
CREATE TABLE `tenants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `id_card` VARCHAR(20) NOT NULL UNIQUE,
  `phone` VARCHAR(15),
  `hometown` VARCHAR(100),
  `date_of_birth` DATE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng contracts (Hợp đồng thuê phòng)
CREATE TABLE `contracts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT NOT NULL,
  `tenant_id` INT NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `rental_price` DECIMAL(10,2) NOT NULL,
  `deposit` DECIMAL(10,2) DEFAULT 0,
  `status` ENUM('active', 'terminated', 'expired') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng contract_members (Danh sách người ở cùng phòng)
CREATE TABLE `contract_members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `id_card` VARCHAR(20),
  `phone` VARCHAR(15),
  FOREIGN KEY (`contract_id`) REFERENCES `contracts`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bảng services (Danh mục dịch vụ)
CREATE TABLE `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `unit_name` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Bảng meter_readings (Ghi chỉ số điện nước theo tháng)
CREATE TABLE `meter_readings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT NOT NULL,
  `month` TINYINT NOT NULL,
  `year` INT NOT NULL,
  `old_electricity` INT NOT NULL DEFAULT 0,
  `new_electricity` INT NOT NULL,
  `old_water` INT NOT NULL DEFAULT 0,
  `new_water` INT NOT NULL,
  `recorded_at` DATE NOT NULL,
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Bảng invoices (Hóa đơn tiền phòng hàng tháng)
CREATE TABLE `invoices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  `month` TINYINT NOT NULL,
  `year` INT NOT NULL,
  `room_amount` DECIMAL(10,2) NOT NULL,
  `electricity_amount` DECIMAL(10,2) NOT NULL,
  `water_amount` DECIMAL(10,2) NOT NULL,
  `other_services_amount` DECIMAL(10,2) DEFAULT 0,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `paid_amount` DECIMAL(10,2) DEFAULT 0,
  `status` ENUM('unpaid', 'partially_paid', 'paid') DEFAULT 'unpaid',
  `payment_date` DATETIME DEFAULT NULL,
  `note` TEXT,
  FOREIGN KEY (`contract_id`) REFERENCES `contracts`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- DỮ LIỆU MẪU (SEED DATA)
-- ==========================================

-- Seed Users (Mật khẩu mặc định là '123456' sử dụng mã hóa bcrypt cho PHP)
INSERT INTO `users` (`username`, `password`, `fullname`, `phone`, `role`, `status`) VALUES
('admin', '$2y$10$TeeBBCYSZpWu8xdjL6xh1Of8tYCt5nw5sXihbHA7ckXSa8AZzttyi', 'Quản Trị Viên', '0901234567', 'admin', 1),
('khach1', '$2y$10$TeeBBCYSZpWu8xdjL6xh1Of8tYCt5nw5sXihbHA7ckXSa8AZzttyi', 'Nguyễn Văn A', '0912345678', 'tenant', 1),
('khach2', '$2y$10$TeeBBCYSZpWu8xdjL6xh1Of8tYCt5nw5sXihbHA7ckXSa8AZzttyi', 'Trần Thị B', '0987654321', 'tenant', 1);

-- Seed Room Types
INSERT INTO `room_types` (`name`, `base_price`, `description`) VALUES
('Phòng đơn', 2000000.00, 'Phòng dành cho 1-2 người, không có gác lửng.'),
('Phòng gác lửng', 2500000.00, 'Phòng rộng có gác lửng, phù hợp 2-3 người.'),
('Studio', 4000000.00, 'Phòng Studio đầy đủ nội thất tiện nghi.');

-- Seed Rooms
INSERT INTO `rooms` (`room_number`, `room_type_id`, `price`, `status`, `max_occupants`, `description`) VALUES
('P101', 1, 2000000.00, 'rented', 2, 'Phòng ở tầng trệt'),
('P102', 1, 2000000.00, 'empty', 2, 'Phòng ở tầng trệt'),
('P201', 2, 2500000.00, 'rented', 3, 'Phòng ở tầng 2 có ban công'),
('P202', 2, 2500000.00, 'maintenance', 3, 'Đang sửa chữa rò rỉ nước'),
('S301', 3, 4000000.00, 'empty', 4, 'Studio tầng 3 nội thất cơ bản');

-- Seed Tenants
INSERT INTO `tenants` (`user_id`, `fullname`, `id_card`, `phone`, `hometown`, `date_of_birth`) VALUES
(2, 'Nguyễn Văn A', '001099001234', '0912345678', 'Hà Nội', '1995-05-12'),
(3, 'Trần Thị B', '034198005678', '0987654321', 'Nam Định', '1998-10-25');

-- Seed Contracts
INSERT INTO `contracts` (`room_id`, `tenant_id`, `start_date`, `end_date`, `rental_price`, `deposit`, `status`) VALUES
(1, 1, '2023-01-01', '2023-12-31', 2000000.00, 2000000.00, 'active'),
(3, 2, '2023-06-01', '2024-06-01', 2500000.00, 2500000.00, 'active');

-- Seed Contract Members
INSERT INTO `contract_members` (`contract_id`, `fullname`, `id_card`, `phone`) VALUES
(1, 'Phạm Văn C', '001099009876', '0933445566'),
(2, 'Lê Thị D', '034198001122', '0966778899');

-- Seed Services
INSERT INTO `services` (`name`, `unit_price`, `unit_name`) VALUES
('Điện', 3500.00, 'kWh'),
('Nước', 20000.00, 'khối'),
('Wifi', 100000.00, 'phòng/tháng'),
('Rác', 30000.00, 'phòng/tháng');

-- Seed Meter Readings (Ghi số điện nước tháng 10 năm 2023)
INSERT INTO `meter_readings` (`room_id`, `month`, `year`, `old_electricity`, `new_electricity`, `old_water`, `new_water`, `recorded_at`) VALUES
(1, 10, 2023, 1500, 1620, 300, 315, '2023-10-25'),
(3, 10, 2023, 850, 960, 150, 158, '2023-10-25');

-- Seed Invoices (Hóa đơn tháng 10)
INSERT INTO `invoices` (`contract_id`, `month`, `year`, `room_amount`, `electricity_amount`, `water_amount`, `other_services_amount`, `total_amount`, `paid_amount`, `status`, `note`) VALUES
(1, 10, 2023, 2000000.00, 420000.00, 300000.00, 130000.00, 2850000.00, 2850000.00, 'paid', 'Đã thanh toán đủ'),
(2, 10, 2023, 2500000.00, 385000.00, 160000.00, 130000.00, 3175000.00, 0.00, 'unpaid', 'Khách hẹn tuần sau thanh toán');
