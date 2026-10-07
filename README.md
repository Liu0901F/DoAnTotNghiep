# Đồ Án Tốt Nghiệp - Quản Lý Phòng Cho Thuê

Dự án xây dựng phần mềm quản lý phòng trọ, căn hộ dịch vụ dành cho Đồ Án Tốt Nghiệp. Phần mềm được viết bằng **PHP (MVC Thuần)**, kết hợp **MySQL**, **Bootstrap 5** và **Chart.js**.

## 🌟 Các chức năng chính (Đã hoàn thiện)
1. **Phân quyền người dùng**: Admin (Chủ trọ) và Tenant (Khách thuê).
2. **Dashboard Thống kê**: Biểu đồ doanh thu, tỷ lệ lấp đầy, nhắc nhở hợp đồng sắp hết hạn.
3. **Quản lý Phòng & Loại Phòng**: Thêm, sửa, xóa, tìm kiếm phòng trống.
4. **Quản lý Hợp Đồng**: Lập hợp đồng mới, xem biên bản chi tiết, thanh lý hợp đồng. 
5. **Quản lý Hóa Đơn & Điện Nước**: Chốt số điện nước tự động tính tiền, theo dõi công nợ, in phiếu thu.
6. **Quản lý Khách Thuê**: Lưu hồ sơ khách, xem lịch sử các phòng khách đã thuê.
7. **Báo Hỏng & Sửa Chữa (Maintenance)**: Khách thuê gửi yêu cầu, Admin tiếp nhận và cập nhật tiến độ xử lý.
8. **Cổng Thông Tin Khách Thuê (Tenant Portal)**: Dành riêng cho khách thuê theo dõi hóa đơn và hợp đồng của mình.

## 🛠 Hướng dẫn cài đặt cho thành viên nhóm
Để chạy được dự án này trên máy tính cá nhân, các bạn làm theo các bước sau:

**Bước 1: Clone Project**
- Clone code này về và đặt vào thư mục `htdocs` của XAMPP. (Ví dụ: `C:\xampp\htdocs\DoAnTotNghiep`).

**Bước 2: Cài đặt Cơ sở dữ liệu**
- Mở XAMPP, bật **Apache** và **MySQL**.
- Truy cập vào [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/).
- Tạo một Database mới có tên là: `quan_ly_phong_cho_thue` (Chọn Collation là `utf8mb4_general_ci`).
- Chọn Database vừa tạo, chọn tab **Import (Nhập)** và tải lên file `database.sql` có sẵn trong thư mục gốc của dự án.

**Bước 3: Chạy ứng dụng**
- Truy cập trình duyệt theo đường dẫn: [http://localhost/DoAnTotNghiep](http://localhost/DoAnTotNghiep)

### 🔑 Thông tin Đăng nhập mẫu:
- **Tài khoản Admin (Chủ trọ):** 
  - Tên đăng nhập: `admin`
  - Mật khẩu: `123456`
- **Tài khoản Khách thuê (Tenant):**
  - Tên đăng nhập: `khach1` (hoặc khach2)
  - Mật khẩu: `123456`

---
*Dự án vẫn đang được hoàn thiện, các thành viên hãy review và đóng góp ý kiến để chốt luồng tính năng!*
