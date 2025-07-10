# HeThongQuanLyBaiGiang

Website quản lý bài giảng sử dụng ngôn ngữ PHP Framework Laravel.

## Hướng dẫn triển khai dự án

### 1. Clone dự án về máy

```bash
git clone https://github.com/huuphuc1464/HeThongQuanLyBaiGiang.git
cd .\HeThongQuanLyBaiGiang\
cd .\HeThongQuanLyBaiGiang\
```

### 2. Cài đặt các package cần thiết

```bash
npm install
composer install
```

### 3. Khởi động XAMPP

- Mở XAMPP Control Panel.
- Run **Apache** và **MySQL**.

### 4. Cấu hình file `.env`

- Copy file `.env.example` thành `.env`:
  ```bash
  cp .env.example .env
  ```
- Cập nhật các thông tin kết nối cơ sở dữ liệu trong file `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD) cho phù hợp với cấu hình MySQL của bạn.
- **Cấu hình dịch vụ gửi mail:**  
  Điền thông tin SMTP Gmail ở các biến sau:
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=your_email@gmail.com
  MAIL_PASSWORD=your_app_password
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=your_email@gmail.com
  MAIL_FROM_NAME="Hệ Thống Quản Lý Bài Giảng"
  ```
- **Cấu hình Zoom API:**  
  ```env
  ZOOM_CLIENT_ID="your_zoom_api_key"
  ZOOM_CLIENT_SECRET="your_zoom_api_secret"
  ZOOM_ACCOUNT_ID="your_zoom_account_id"
  ZOOM_HOST_KEY="your_zoom_host_key"
  ```
- **Cấu hình hàng đợi (Queue):**  
  ```env
  QUEUE_CONNECTION=database
  ```

- Chạy lệnh tạo key ứng dụng:
  ```bash
  php artisan key:generate
  ```

### 5. Tạo bảng và cấu trúc cơ sở dữ liệu

Để khởi tạo các bảng trong cơ sở dữ liệu, chạy lệnh sau:

  ```bash
  php artisan migrate
  ```

- Nếu **chưa tạo database**, hệ thống có thể hỏi bạn có muốn tạo không. Gõ `yes` và nhấn `Enter` (hoặc chỉ cần nhấn `Enter` để tiếp tục nếu mặc định tạo mới).
- Nếu muốn chạy các Seeder (tạo dữ liệu mẫu), chạy tiếp lệnh:

  ```bash
  php artisan db:seed
  ```

> Có thể kết hợp chạy cả `migrate` và `seed` bằng lệnh:
> ```bash
> php artisan migrate --seed
> ```


### 6. Khởi chạy web server ảo

```bash
php artisan serve
```
- Truy cập địa chỉ hiển thị trên terminal, mặc định: [http://127.0.0.1:8000](http://127.0.0.1:8000)

### 7. Khởi chạy hàng đợi (queue)

```bash
php artisan queue:work
```

## Thông tin thêm

- Đảm bảo PHP = 8.2, Composer đã được cài đặt trên máy.
- Nếu chưa có Composer, cài đặt tại: [https://getcomposer.org/download/](https://getcomposer.org/download/)
- Đảm bảo đã tạo database trước khi chạy migrate!
- Để sử dụng tính năng gửi mail, cần bật xác thực 2 lớp và tạo App Password cho Gmail.
- Để sử dụng Zoom API, bạn cần đăng ký ứng dụng trên marketplace của Zoom để lấy Client ID và Secret.

---

Chúc bạn triển khai dự án thành công!
