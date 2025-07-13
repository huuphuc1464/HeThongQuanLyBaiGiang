# Hướng dẫn sử dụng TinyMCE trong hệ thống bình luận

## Tổng quan

TinyMCE đã được tích hợp vào hệ thống để cung cấp editor rich text cho bình luận. Điều này cho phép người dùng:
- Định dạng text (bold, italic, lists, etc.)
- Chèn hình ảnh
- Tạo danh sách có dấu chấm
- Sử dụng các công cụ định dạng khác

## Cài đặt

### 1. Files đã được thêm vào layout

Các file sau đã được thêm vào layout `studentLayout.blade.php` và `teacherLayout.blade.php`:

```html
<!-- CSS -->
<link rel="stylesheet" href="{{ asset('css/binh-luan-realtime.css') }}">

<!-- JavaScript -->
<script src="{{ asset('/js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('/js/tinymce-init.js') }}"></script>
<script src="{{ asset('/js/components/BinhLuanRealtime.js') }}"></script>
```

### 2. Cấu trúc thư mục

```
public/
├── js/
│   ├── tinymce/
│   │   ├── tinymce.min.js          # TinyMCE core
│   │   ├── langs/
│   │   │   └── vi.js               # Ngôn ngữ tiếng Việt
│   │   └── ...
│   ├── tinymce-init.js             # Helper functions
│   └── components/
│       └── BinhLuanRealtime.js     # Vue.js component
└── css/
    └── binh-luan-realtime.css      # Styles cho bình luận
```

## Sử dụng cơ bản

### 1. Tạo textarea với TinyMCE

```html
<textarea 
    id="myEditor" 
    class="form-control tinymce-editor"
    placeholder="Viết nội dung..."
    rows="4"
></textarea>
```

### 2. Khởi tạo TinyMCE

```javascript
// Tự động khởi tạo cho tất cả textarea có class tinymce-editor
document.addEventListener('DOMContentLoaded', function() {
    if (typeof TinyMCEHelper !== 'undefined') {
        TinyMCEHelper.initAllTinyMCE();
    }
});

// Hoặc khởi tạo cho một selector cụ thể
TinyMCEHelper.initTinyMCE('#myEditor');

// Hoặc khởi tạo với cấu hình đặc biệt cho bình luận
TinyMCEHelper.initCommentTinyMCE();
```

### 3. Lấy và set nội dung

```javascript
// Lấy nội dung HTML
const htmlContent = TinyMCEHelper.getTinyMCEContent('#myEditor');

// Lấy nội dung text thuần
const textContent = TinyMCEHelper.getTinyMCEContent('#myEditor', 'text');

// Set nội dung
TinyMCEHelper.setTinyMCEContent('#myEditor', '<p>Nội dung mới</p>');

// Xóa nội dung
TinyMCEHelper.clearTinyMCE('#myEditor');
```

## Cấu hình

### 1. Cấu hình mặc định

```javascript
const TINYMCE_DEFAULT_CONFIG = {
    height: 200,
    menubar: false,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic forecolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
    language: 'vi',
    language_url: '/js/tinymce/langs/vi.js'
};
```

### 2. Cấu hình cho bình luận

```javascript
const commentConfig = {
    height: 150,
    toolbar: 'bold italic | bullist numlist | link image | removeformat',
    plugins: ['lists', 'link', 'image', 'wordcount'],
    max_chars: 1000,
    // Giới hạn ký tự
    setup: function(editor) {
        editor.on('keydown', function(e) {
            const content = editor.getContent({format: 'text'});
            if (content.length >= 1000 && e.keyCode !== 8 && e.keyCode !== 46) {
                e.preventDefault();
                return false;
            }
        });
    }
};
```

## Sử dụng trong Vue.js

### 1. Trong Vue component

```javascript
const BinhLuanComponent = {
    data() {
        return {
            noiDung: ''
        }
    },
    
    mounted() {
        // Khởi tạo TinyMCE sau khi component được mount
        this.$nextTick(() => {
            TinyMCEHelper.initTinyMCE('#noiDung', {
                height: 150,
                toolbar: 'bold italic | bullist numlist'
            });
        });
    },
    
    methods: {
        guiBinhLuan() {
            const content = TinyMCEHelper.getTinyMCEContent('#noiDung');
            if (content.trim()) {
                // Gửi bình luận
                console.log(content);
                // Xóa nội dung
                TinyMCEHelper.clearTinyMCE('#noiDung');
            }
        }
    }
};
```

### 2. Với v-model

```html
<textarea 
    id="noiDung" 
    v-model="noiDung"
    class="form-control tinymce-editor"
></textarea>
```

```javascript
// TinyMCE sẽ tự động cập nhật v-model khi nội dung thay đổi
```

## Bảo mật

### 1. Sanitize HTML

TinyMCE đã được cấu hình để loại bỏ các thẻ HTML nguy hiểm:

```javascript
extended_valid_elements: 'span[*],p[*],br,strong,em,u,strike,sub,sup,ol,ul,li,blockquote,pre,code,h1,h2,h3,h4,h5,h6',
invalid_elements: 'script,iframe,object,embed,form,input,textarea,select,button'
```

### 2. Xử lý paste

```javascript
editor.on('paste', function(e) {
    // Loại bỏ các thẻ HTML không mong muốn
    const content = e.content;
    const cleanContent = content.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
    e.content = cleanContent;
});
```

## Upload hình ảnh

### 1. Cấu hình upload

```javascript
images_upload_url: '/upload-image',
images_upload_handler: function (blobInfo, success, failure) {
    var xhr, formData;
    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', '/upload-image');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    xhr.onload = function() {
        var json;
        if (xhr.status != 200) {
            failure('HTTP Error: ' + xhr.status);
            return;
        }
        json = JSON.parse(xhr.responseText);
        if (!json || typeof json.location != 'string') {
            failure('Invalid JSON: ' + xhr.responseText);
            return;
        }
        success(json.location);
    };
    
    formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    xhr.send(formData);
}
```

### 2. Route cho upload

```php
Route::post('/upload-image', function(Request $request) {
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $path = $file->store('public/images');
        return response()->json(['location' => Storage::url($path)]);
    }
    return response()->json(['error' => 'No file uploaded'], 400);
});
```

## Troubleshooting

### 1. TinyMCE không load

```javascript
// Kiểm tra xem TinyMCE đã được load chưa
if (typeof tinymce === 'undefined') {
    console.error('TinyMCE chưa được load');
}
```

### 2. Không tìm thấy file ngôn ngữ

```javascript
// Kiểm tra đường dẫn file ngôn ngữ
language_url: '/js/tinymce/langs/vi.js'
```

### 3. V-model không hoạt động

```javascript
// Đảm bảo TinyMCE cập nhật v-model
editor.on('change', function() {
    const textarea = editor.getElement();
    if (textarea) {
        const event = new Event('input', { bubbles: true });
        textarea.dispatchEvent(event);
    }
});
```

## Tùy chỉnh giao diện

### 1. Thay đổi toolbar

```javascript
toolbar: 'bold italic | bullist numlist | link image | removeformat'
```

### 2. Thay đổi plugins

```javascript
plugins: ['lists', 'link', 'image', 'wordcount']
```

### 3. Thay đổi style

```javascript
content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }'
```

## Ví dụ hoàn chỉnh

Xem file `resources/views/examples/tinymce-example.blade.php` để có ví dụ hoàn chỉnh về cách sử dụng TinyMCE trong hệ thống bình luận.

## Lưu ý quan trọng

1. **Performance**: TinyMCE chỉ nên được khởi tạo khi cần thiết
2. **Memory**: Đảm bảo destroy editor khi không cần thiết
3. **Security**: Luôn sanitize HTML trước khi lưu vào database
4. **Accessibility**: Đảm bảo editor có thể sử dụng bằng keyboard
5. **Mobile**: Test trên thiết bị di động để đảm bảo UX tốt 