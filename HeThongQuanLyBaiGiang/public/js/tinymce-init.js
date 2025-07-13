/**
 * TinyMCE Initialization Helper
 * File này giúp khởi tạo TinyMCE với cấu hình chuẩn cho hệ thống bình luận
 */

// Cấu hình mặc định cho TinyMCE
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
    language_url: '/js/tinymce/langs/vi.js',
    // Cấu hình cho bảo mật
    extended_valid_elements: 'span[*],p[*],br,strong,em,u,strike,sub,sup,ol,ul,li,blockquote,pre,code,h1,h2,h3,h4,h5,h6',
    invalid_elements: 'script,iframe,object,embed,form,input,textarea,select,button',
    // Cấu hình cho responsive
    width: '100%',
    // Cấu hình cho paste
    paste_as_text: false,
    paste_enable_default_filters: true,
    paste_word_valid_elements: 'b,strong,i,em,h1,h2,h3,h4,h5,h6',
    paste_retain_style_properties: 'color font-size background-color',
    // Cấu hình cho upload ảnh
    images_upload_url: '/upload-image',
    images_upload_handler: function (blobInfo, success, failure) {
        // Xử lý upload ảnh nếu cần
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '/upload-image');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        xhr.onload = function () {
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
};

/**
 * Khởi tạo TinyMCE cho một selector cụ thể
 * @param {string} selector - CSS selector cho textarea
 * @param {object} customConfig - Cấu hình tùy chỉnh (optional)
 */
function initTinyMCE(selector, customConfig = {}) {
    if (typeof tinymce === 'undefined') {
        console.error('TinyMCE chưa được load. Vui lòng kiểm tra đường dẫn.');
        return;
    }

    const config = { ...TINYMCE_DEFAULT_CONFIG, ...customConfig };

    tinymce.init({
        selector: selector,
        ...config,
        setup: function (editor) {
            // Cập nhật v-model khi nội dung thay đổi
            editor.on('change', function () {
                const textarea = editor.getElement();
                if (textarea) {
                    const event = new Event('input', { bubbles: true });
                    textarea.dispatchEvent(event);
                }
            });

            // Cập nhật khi blur
            editor.on('blur', function () {
                const textarea = editor.getElement();
                if (textarea) {
                    const event = new Event('input', { bubbles: true });
                    textarea.dispatchEvent(event);
                }
            });

            // Xử lý paste
            editor.on('paste', function (e) {
                // Loại bỏ các thẻ HTML không mong muốn
                const content = e.content;
                const cleanContent = content.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                e.content = cleanContent;
            });
        }
    });
}

/**
 * Khởi tạo TinyMCE cho tất cả textarea có class tinymce-editor
 */
function initAllTinyMCE() {
    initTinyMCE('.tinymce-editor');
}

/**
 * Khởi tạo TinyMCE cho bình luận với cấu hình đặc biệt
 */
function initCommentTinyMCE() {
    const commentConfig = {
        ...TINYMCE_DEFAULT_CONFIG,
        height: 150,
        toolbar: 'bold italic | bullist numlist | link image | removeformat',
        plugins: ['lists', 'link', 'image', 'wordcount'],
        // Giới hạn ký tự
        max_chars: 1000,
        setup: function (editor) {
            editor.on('keydown', function (e) {
                const content = editor.getContent({ format: 'text' });
                if (content.length >= 1000 && e.keyCode !== 8 && e.keyCode !== 46) {
                    e.preventDefault();
                    return false;
                }
            });

            // Cập nhật v-model
            editor.on('change', function () {
                const textarea = editor.getElement();
                if (textarea) {
                    const event = new Event('input', { bubbles: true });
                    textarea.dispatchEvent(event);
                }
            });
        }
    };

    initTinyMCE('.tinymce-editor', commentConfig);
}

/**
 * Xóa nội dung của TinyMCE editor
 * @param {string} selector - CSS selector cho editor
 */
function clearTinyMCE(selector) {
    if (tinymce.get(selector)) {
        tinymce.get(selector).setContent('');
    }
}

/**
 * Lấy nội dung từ TinyMCE editor
 * @param {string} selector - CSS selector cho editor
 * @param {string} format - Format của nội dung ('text', 'html')
 * @returns {string} Nội dung của editor
 */
function getTinyMCEContent(selector, format = 'html') {
    if (tinymce.get(selector)) {
        return tinymce.get(selector).getContent({ format: format });
    }
    return '';
}

/**
 * Set nội dung cho TinyMCE editor
 * @param {string} selector - CSS selector cho editor
 * @param {string} content - Nội dung cần set
 */
function setTinyMCEContent(selector, content) {
    if (tinymce.get(selector)) {
        tinymce.get(selector).setContent(content);
    }
}

/**
 * Kiểm tra xem TinyMCE đã được load chưa
 * @returns {boolean}
 */
function isTinyMCELoaded() {
    return typeof tinymce !== 'undefined';
}

// Khởi tạo khi DOM ready
document.addEventListener('DOMContentLoaded', function () {
    // Đợi một chút để đảm bảo TinyMCE đã load
    setTimeout(function () {
        if (isTinyMCELoaded()) {
            initAllTinyMCE();
        } else {
            console.warn('TinyMCE chưa được load. Vui lòng kiểm tra đường dẫn.');
        }
    }, 100);
});

// Export functions để sử dụng trong Vue.js
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initTinyMCE,
        initAllTinyMCE,
        initCommentTinyMCE,
        clearTinyMCE,
        getTinyMCEContent,
        setTinyMCEContent,
        isTinyMCELoaded,
        TINYMCE_DEFAULT_CONFIG
    };
} else {
    window.TinyMCEHelper = {
        initTinyMCE,
        initAllTinyMCE,
        initCommentTinyMCE,
        clearTinyMCE,
        getTinyMCEContent,
        setTinyMCEContent,
        isTinyMCELoaded,
        TINYMCE_DEFAULT_CONFIG
    };
} 