# Hướng Dẫn Sử Dụng Chức Năng Làm Bài Kiểm Tra

## Tổng Quan
Chức năng làm bài kiểm tra cho phép sinh viên tham gia các bài kiểm tra trực tuyến được tạo bởi giảng viên. Hệ thống hỗ trợ bài kiểm tra dạng trắc nghiệm với 4 đáp án A, B, C, D.

## Các Tính Năng Chính

### 1. Danh Sách Bài Kiểm Tra
- **URL**: `/bai-kiem-tra`
- **Truy cập**: Sidebar → "Bài kiểm tra trắc nghiệm"
- **Mô tả**: Hiển thị tất cả bài kiểm tra trong các lớp học phần mà sinh viên đã tham gia
- **Tính năng**:
  - Xem thông tin chi tiết bài kiểm tra
  - Kiểm tra trạng thái bài kiểm tra (chưa bắt đầu, đang diễn ra, đã kết thúc)
  - Kiểm tra sinh viên đã làm bài hay chưa
  - Nút làm bài hoặc xem kết quả

### 2. Làm Bài Kiểm Tra
- **URL**: `/bai-kiem-tra/{maBaiKiemTra}/lam-bai`
- **Mô tả**: Giao diện làm bài kiểm tra với timer đếm ngược
- **Tính năng**:
  - Hiển thị câu hỏi trắc nghiệm
  - Timer đếm ngược thời gian làm bài
  - Validation đảm bảo chọn đáp án cho tất cả câu hỏi
  - Modal xác nhận trước khi nộp bài
  - Tự động nộp bài khi hết thời gian

### 3. Kết Quả Bài Kiểm Tra
- **URL**: `/bai-kiem-tra/{maBaiKiemTra}/ket-qua`
- **Mô tả**: Hiển thị kết quả chi tiết sau khi làm bài
- **Tính năng**:
  - Điểm số và tỷ lệ đúng
  - Chi tiết từng câu hỏi (đáp án đã chọn vs đáp án đúng)
  - Màu sắc phân biệt câu đúng/sai
  - Thời gian nộp bài

## Cấu Trúc Database

### Bảng chính:
- `bai_kiem_tra`: Thông tin bài kiểm tra
- `cau_hoi_bai_kiem_tra`: Câu hỏi và đáp án
- `ket_qua_bai_kiem_tra`: Kết quả tổng quan
- `chi_tiet_ket_qua`: Chi tiết từng câu trả lời

### Relationships:
- Bài kiểm tra thuộc về lớp học phần
- Bài kiểm tra được tạo bởi giảng viên
- Kết quả thuộc về sinh viên và bài kiểm tra
- Chi tiết kết quả liên kết với câu hỏi

## Quy Trình Hoạt Động

### 1. Giảng viên tạo bài kiểm tra
1. Đăng nhập với tài khoản giảng viên
2. Vào quản lý bài kiểm tra
3. Tạo bài kiểm tra mới với thời gian bắt đầu/kết thúc
4. Thêm câu hỏi trắc nghiệm

### 2. Sinh viên làm bài kiểm tra
1. Đăng nhập với tài khoản sinh viên
2. Vào lớp học phần → Sidebar → "Bài kiểm tra trắc nghiệm"
3. Chọn bài kiểm tra đang diễn ra
4. Làm bài và nộp trước khi hết thời gian

### 3. Xem kết quả
1. Sau khi nộp bài, hệ thống tự động chuyển đến trang kết quả
2. Có thể xem lại kết quả từ danh sách bài kiểm tra

## Bảo Mật và Kiểm Soát

### Kiểm tra thời gian:
- Không cho phép làm bài trước thời gian bắt đầu
- Không cho phép làm bài sau thời gian kết thúc
- Tự động nộp bài khi hết thời gian

### Kiểm tra quyền truy cập:
- Chỉ sinh viên mới có thể truy cập
- Chỉ sinh viên trong lớp học phần mới thấy bài kiểm tra
- Không cho phép làm lại bài đã nộp

### Validation:
- Bắt buộc chọn đáp án cho tất cả câu hỏi
- Kiểm tra dữ liệu đầu vào
- Xác nhận trước khi nộp bài

## Giao Diện

### Navigation:
- **Sidebar**: Link "Bài kiểm tra trắc nghiệm" trong sidebar của lớp học phần
- **Sidebar riêng**: Khi vào trang bài kiểm tra, hiển thị sidebar riêng với hướng dẫn
- **Responsive**: Tương thích với desktop, tablet, mobile

### UX/UI:
- Màu sắc phân biệt trạng thái
- Animation cho timer
- Modal xác nhận
- Thông báo lỗi/thành công
- Hướng dẫn trong sidebar

## Cài Đặt và Sử Dụng

### Yêu cầu hệ thống:
- Laravel 8+
- PHP 8.0+
- MySQL 5.7+
- Bootstrap 5

### Cài đặt:
1. Clone repository
2. Chạy `composer install`
3. Cấu hình database trong `.env`
4. Chạy migrations: `php artisan migrate`
5. Chạy seeders: `php artisan db:seed`

### Sử dụng:
1. Tạo tài khoản giảng viên và sinh viên
2. Giảng viên tạo bài kiểm tra
3. Sinh viên làm bài kiểm tra
4. Xem kết quả

## Troubleshooting

### Lỗi thường gặp:
1. **Không thấy bài kiểm tra**: Kiểm tra sinh viên đã tham gia lớp học phần chưa
2. **Không thể làm bài**: Kiểm tra thời gian và trạng thái bài kiểm tra
3. **Lỗi nộp bài**: Kiểm tra kết nối mạng và thử lại

### Logs:
- Kiểm tra logs trong `storage/logs/laravel.log`
- Xem lỗi validation và database

## Phát Triển Tương Lai

### Tính năng có thể thêm:
- Bài kiểm tra tự luận
- Upload file đính kèm
- Chế độ xem lại bài làm
- Export kết quả PDF
- Thống kê chi tiết
- Giao diện dark mode 