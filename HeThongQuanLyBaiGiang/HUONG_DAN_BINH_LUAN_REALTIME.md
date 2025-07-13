# Hướng dẫn cải thiện chức năng bình luận realtime

## Tổng quan

Hệ thống bình luận đã được cải thiện với các tính năng mới:
- **Realtime**: Bình luận hiển thị ngay lập tức cho tất cả người dùng
- **Upvote/Downvote**: Hệ thống đánh giá bình luận
- **TinyMCE**: Editor rich text cho bình luận
- **Sắp xếp**: Nhiều tùy chọn sắp xếp bình luận
- **Cấu trúc tốt hơn**: Hỗ trợ bình luận con và chỉnh sửa

## Cài đặt

### 1. Chạy Migration

```bash
php artisan migrate
```

Migration sẽ:
- Tạo bảng `binh_luan_upvotes` cho hệ thống vote
- Cập nhật bảng `binh_luan_bai_giang` với các trường mới
- Thêm index để tối ưu hiệu suất

### 2. Cài đặt Laravel Echo (cho realtime)

```bash
composer require pusher/pusher-php-server
npm install laravel-echo pusher-js
```

Cấu hình trong `config/broadcasting.php`:

```php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true
    ],
],
```

### 3. Cấu hình TinyMCE

Thêm vào layout chính:

```html
<script src="/js/tinymce/tinymce.min.js"></script>
<link rel="stylesheet" href="/css/binh-luan-realtime.css">
```

## Sử dụng

### 1. Sử dụng component realtime

```php
// Trong controller
public function chiTietBaiGiang($maBaiGiang)
{
    $baiGiang = BaiGiang::findOrFail($maBaiGiang);
    return view('sinhvien.chiTietBaiGiang', compact('baiGiang'));
}
```

```blade
{{-- Trong view --}}
<x-binh-luan :baiGiang="$baiGiang" :useRealtime="true" />
```

### 2. Khởi tạo Vue.js component

```html
<script src="/js/components/BinhLuanRealtime.js"></script>
<script>
new Vue({
    el: '#binh-luan-realtime',
    mixins: [BinhLuanRealtime],
    props: {
        maBai: {{ $baiGiang->MaBai }},
        maNguoiDung: {{ Auth::id() }}
    }
});
</script>
```

## Tính năng mới

### 1. Upvote/Downvote

- Người dùng có thể upvote/downvote bình luận
- Mỗi người chỉ có thể vote một lần cho mỗi bình luận
- Có thể thay đổi vote hoặc hủy vote

### 2. TinyMCE Editor

- Hỗ trợ định dạng text (bold, italic, lists, etc.)
- Upload hình ảnh
- Preview nội dung
- Hỗ trợ tiếng Việt

### 3. Sắp xếp bình luận

- **Mới nhất**: Bình luận mới nhất lên đầu
- **Cũ nhất**: Bình luận cũ nhất lên đầu  
- **Nhiều upvote nhất**: Sắp xếp theo số upvote
- **Ít upvote nhất**: Sắp xếp theo số upvote tăng dần

### 4. Realtime

- Bình luận mới hiển thị ngay lập tức
- Sử dụng Laravel Echo và Pusher
- Broadcast events khi có bình luận mới

### 5. Chỉnh sửa bình luận

- Người dùng có thể chỉnh sửa bình luận của mình
- Ghi lại lý do chỉnh sửa
- Hiển thị badge "Đã chỉnh sửa"

## API Endpoints

### 1. Gửi bình luận
```
POST /binh-luan/gui-binh-luan
{
    "MaBai": 1,
    "NoiDung": "Nội dung bình luận"
}
```

### 2. Trả lời bình luận
```
POST /binh-luan/tra-loi-binh-luan
{
    "MaBinhLuan": 1,
    "MaBai": 1,
    "NoiDung": "Nội dung trả lời"
}
```

### 3. Vote bình luận
```
POST /binh-luan/vote
{
    "MaBinhLuan": 1,
    "LoaiVote": "upvote" // hoặc "downvote"
}
```

### 4. Lấy danh sách bình luận
```
GET /binh-luan/danh-sach?MaBai=1&SapXep=moi_nhat&page=1
```

### 5. Chỉnh sửa bình luận
```
PUT /binh-luan/cap-nhat
{
    "MaBinhLuan": 1,
    "NoiDung": "Nội dung mới",
    "LyDoChinhSua": "Lý do chỉnh sửa"
}
```

### 6. Xóa bình luận
```
DELETE /binh-luan/xoa/{id}
```

## Cấu trúc Database

### Bảng `binh_luan_bai_giang`
- `MaBinhLuan` (Primary Key)
- `MaNguoiGui` (Foreign Key)
- `MaBai` (Foreign Key)
- `MaBinhLuanCha` (Foreign Key, nullable)
- `NoiDung` (longText)
- `DaChinhSua` (boolean)
- `SoUpvote` (integer)
- `SoDownvote` (integer)
- `DaAn` (boolean)
- `ThoiGianChinhSua` (timestamp, nullable)
- `LyDoChinhSua` (string, nullable)
- `created_at`, `updated_at`

### Bảng `binh_luan_upvotes`
- `id` (Primary Key)
- `MaBinhLuan` (Foreign Key)
- `MaNguoiDung` (Foreign Key)
- `LoaiUpvote` (enum: 'upvote', 'downvote')
- `created_at`, `updated_at`

## Events

### BinhLuanMoi
- Broadcast khi có bình luận mới
- Channel: `binh-luan-bai-{MaBai}`
- Event: `binh-luan-moi`

## Tùy chỉnh

### 1. Thay đổi cấu hình TinyMCE

```javascript
tinymce.init({
    selector: '.tinymce-editor',
    height: 300,
    plugins: ['advlist', 'autolink', 'lists', 'link', 'image'],
    toolbar: 'bold italic | bullist numlist | link image',
    // Thêm cấu hình khác...
});
```

### 2. Tùy chỉnh giao diện

Chỉnh sửa file `public/css/binh-luan-realtime.css` để thay đổi style.

### 3. Thêm tính năng mới

- Tạo migration mới cho tính năng
- Cập nhật Model
- Thêm method trong Controller
- Cập nhật Vue.js component

## Troubleshooting

### 1. TinyMCE không hoạt động
- Kiểm tra đường dẫn đến file tinymce.min.js
- Đảm bảo đã load script trước khi khởi tạo

### 2. Realtime không hoạt động
- Kiểm tra cấu hình Pusher
- Đảm bảo Laravel Echo đã được cài đặt
- Kiểm tra console để xem lỗi

### 3. Vote không cập nhật
- Kiểm tra CSRF token
- Đảm bảo user đã đăng nhập
- Kiểm tra quyền truy cập

## Performance

### 1. Index Database
- Đã thêm index cho `MaBai`, `MaBinhLuanCha`
- Index cho `SoUpvote`, `created_at`

### 2. Pagination
- Sử dụng Laravel pagination
- Load 10 bình luận mỗi trang

### 3. Caching
- Có thể thêm cache cho danh sách bình luận
- Cache vote counts

## Security

### 1. CSRF Protection
- Tất cả POST/PUT/DELETE requests đều có CSRF token

### 2. Authorization
- Chỉ người tạo bình luận mới có thể chỉnh sửa/xóa
- Kiểm tra quyền truy cập trước khi thực hiện action

### 3. Input Validation
- Validate tất cả input từ user
- Sanitize HTML content từ TinyMCE

## Migration từ hệ thống cũ

### 1. Backward Compatibility
- Component cũ vẫn hoạt động bình thường
- Sử dụng `useRealtime="false"` để dùng component cũ

### 2. Data Migration
- Dữ liệu cũ sẽ được giữ nguyên
- Các trường mới sẽ có giá trị mặc định

### 3. Gradual Rollout
- Có thể triển khai từng phần
- Test kỹ trước khi deploy production 