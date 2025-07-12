// TinyMCE
function initTinyMCE(csrfToken, maBaiGiang = null, maBai = null) {
    tinymce.init({
        selector: '#editor',
        license_key: 'gpl',
        height: 500,
        menubar: 'file edit view insert format tools table help elfinder uploadword',
        paste_data_images: true,
        paste_as_text: false,
        paste_webkit_styles: 'all',
        content_css: false,
        plugins: [
            'importcss', 'searchreplace', 'autolink',
            'autosave', 'save', 'directionality', 'code', 'visualblocks', 'visualchars',
            'fullscreen', 'image', 'link', 'media', 'codesample', 'table',
            'charmap', 'pagebreak', 'nonbreaking', 'anchor',
            'insertdatetime', 'advlist', 'lists', 'wordcount',
            'help', 'quickbars', 'emoticons', 'powerpaste', 'advcode'
        ],
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | ' +
            'alignleft aligncenter alignright alignjustify | outdent indent | ' +
            'numlist bullist | forecolor backcolor removeformat | ' +
            'pagebreak | charmap emoticons | fullscreen preview save print | ' +
            'insertfile image media codesample | elfinder',
        menu: {
            uploadword: {
                title: 'Upload Word',
                items: 'uploadword_menu'
            },
            elfinder: {
                title: 'Quản lý file',
                items: 'elfinder_menu'
            }
        },
        powerpaste_allow_local_images: true,
        powerpaste_word_import: 'prompt',
        powerpaste_html_import: 'prompt',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        paste_block_drop: true,
        toolbar_sticky: true,
        automatic_uploads: false,
        relative_urls: false,
        remove_script_host: false,
        document_base_url: window.APP_URL + '/',

        file_picker_callback: function (callback, value, meta) {
            const elfinderContainer = $('#elfinder');

            // Dọn dẹp instance cũ
            if (elfinderContainer.hasClass('elfinder')) {
                try {
                    elfinderContainer.elfinder('destroy');
                } catch (e) {
                    console.warn('Không thể destroy elFinder:', e);
                }
                elfinderContainer.empty(); // Xoá DOM con cũ
            }

            const elfinderUrl = window.ELFINDER_URL +
                '?type=' + encodeURIComponent(meta.filetype) +
                '&maBaiGiang=' + encodeURIComponent(window.maBaiGiang || '') +
                '&maBai=' + encodeURIComponent(window.maBai || '');

            // Hiện modal
            $('#elfinderModal').modal('show');

            // Khởi tạo lại elFinder mới
            elfinderContainer.elfinder({
                lang: 'en',
                url: elfinderUrl,
                soundPath: window.ELFINDER_SOUND,
                customHeaders: {
                    'X-CSRF-TOKEN': csrfToken
                },
                getFileCallback: function (file) {
                    if (meta.filetype === 'image') {
                        callback(file.url, { alt: file.name });
                    } else if (meta.filetype === 'media') {
                        callback(file.url);
                    } else {
                        callback(file.url, { text: file.name });
                    }

                    $('#elfinderModal').modal('hide');
                },
                resizable: false
            });
        }
        ,


        setup: function (editor) {
            editor.ui.registry.addMenuItem('uploadword_menu', {
                text: 'Tải lên file word (.docx)',
                icon: 'upload',
                onAction: function () {
                    document.getElementById('upload-docx').click();
                }
            });

            editor.ui.registry.addMenuItem('elfinder_menu', {
                text: 'Mở danh sách file',
                onAction: function () {
                    $('#elfinderModal').modal('show');
                    if (!window.elfinderInstance) {
                        window.elfinderInstance = $('#elfinder').elfinder({
                            lang: 'en',
                            url: window.ELFINDER_URL,
                            soundPath: window.ELFINDER_SOUND,
                            getFileCallback: function (file) {
                                editor.insertContent('<img src="' + file.url + '" />');
                                $('#elfinderModal').modal('hide');
                            },
                            resizable: false,
                            customHeaders: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            transport: {
                                withCredentials: true
                            }
                        }).elfinder('instance');
                    }
                }
            });

            editor.on('PastePostProcess', function () {
                setTimeout(() => {
                    const doc = editor.getDoc();

                    // 1. Xử lý ảnh lỗi hoặc rỗng
                    const allImages = doc.querySelectorAll('img');
                    allImages.forEach(img => {
                        const src = img.getAttribute('src');
                        if (!src || src.trim() === '' || (!src.startsWith('blob:') && !src.startsWith('data:image') && !src.startsWith('http'))) {
                            const span = document.createElement('span');
                            span.style.color = 'red';
                            span.textContent = '[Hình ảnh không hợp lệ đã bị xóa]';
                            img.replaceWith(span);
                        }
                    });

                    // 2. Xử lý ảnh dán dạng blob hoặc base64
                    const images = [...doc.querySelectorAll('img')].filter(img => {
                        const src = img.getAttribute('src');
                        return src && (src.startsWith('blob:') || src.startsWith('data:image'));
                    });

                    let totalImages = images.length;
                    let processed = 0;
                    let unsupportedCount = 0;
                    if (totalImages === 0) return;

                    const checkDone = () => {
                        processed++;
                        if (processed === totalImages && unsupportedCount > 0) {
                            alert(`Có ${unsupportedCount} ảnh không hỗ trợ đã được thay thế bằng "[Định dạng không hỗ trợ]".`);
                        }
                    };

                    images.forEach(img => {
                        const src = img.getAttribute('src');

                        fetch(src)
                            .then(res => res.blob())
                            .then(blob => {
                                if (!blob.type.startsWith('image/')) {
                                    const span = document.createElement('span');
                                    span.style.color = 'red';
                                    span.textContent = '[Định dạng không hỗ trợ]';
                                    img.replaceWith(span);
                                    unsupportedCount++;
                                    checkDone();
                                    return;
                                }

                                const formData = new FormData();
                                formData.append('image', blob, 'pasted_image.png');
                                formData.append('maBaiGiang', maBaiGiang || '');
                                formData.append('maBai', maBai || '');

                                return fetch('/upload-docx-image', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: formData
                                })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.url) {
                                            img.setAttribute('src', data.url);
                                            console.log(data.url);
                                            
                                            // Đánh dấu nếu có thể là equation
                                            const alt = img.getAttribute('alt')?.toLowerCase() || '';
                                            if (alt.includes('equation') || data.url.includes('equation')) {
                                                img.style.border = '2px dashed orange';
                                                img.setAttribute('title', 'Ảnh có thể là công thức từ Word');
                                            }
                                        } else {
                                            const span = document.createElement('span');
                                            span.style.color = 'red';
                                            span.textContent = '[Định dạng không hỗ trợ]';
                                            img.replaceWith(span);
                                            unsupportedCount++;
                                        }
                                        checkDone();
                                    })
                                    .catch(() => {
                                        const span = document.createElement('span');
                                        span.style.color = 'red';
                                        span.textContent = '[Định dạng không hỗ trợ]';
                                        img.replaceWith(span);
                                        unsupportedCount++;
                                        checkDone();
                                    });
                            })
                            .catch(() => {
                                const span = document.createElement('span');
                                span.style.color = 'red';
                                span.textContent = '[Định dạng không hỗ trợ]';
                                img.replaceWith(span);
                                unsupportedCount++;
                                checkDone();
                            });
                    });
                }, 200);
            });


        }
    });
}

// Nút hủy
function handleCancelBaiGiang({ routeUrl, maBaiGiang, maBai = null }) {
    if (confirm('Bạn có chắc muốn hủy bỏ bài học không?')) {
        fetch(routeUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                MaBaiGiang: maBaiGiang,
                MaBai: maBai
            })
        })
            .then(res => {
                if (!res.ok) throw new Error('Lỗi mạng');
                return res.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = document.referrer;
                } else {
                    alert('Hủy bỏ thất bại.');
                }
            })
            .catch(err => {
                console.error('Lỗi khi hủy bỏ:', err);
                alert('Đã xảy ra lỗi khi hủy bỏ.');
            });
    }
}

window.addEventListener('DOMContentLoaded', () => {
    initTinyMCE(window.csrfToken, window.maBaiGiang, window.maBai || null);

    document.getElementById('btn-cancel')?.addEventListener('click', () => {
        handleCancelBaiGiang({
            routeUrl: window.cancelRoute,
            maBaiGiang: window.maBaiGiang
        });
    });

    const uploadDocxInput = document.getElementById('upload-docx');

    if (uploadDocxInput) {
        uploadDocxInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB

            const allowedExtension = /\.docx$/i;
            if (!allowedExtension.test(file.name)) {
                alert('Chỉ hỗ trợ định dạng .docx!');
                this.value = '';
                return;
            }

            if (file && file.size > maxSize) {
                alert('File quá lớn! Dung lượng tối đa là 5MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const arrayBuffer = e.target.result;

                mammoth.convertToHtml({ arrayBuffer }, {
                    convertImage: mammoth.images.inline(function (element) {
                        return element.read("base64").then(imageBuffer => ({
                            src: "data:" + element.contentType + ";base64," + imageBuffer
                        }));
                    })
                }).then(result => {
                    const editor = tinymce.get('editor');
                    editor.setContent(result.value);

                    setTimeout(() => uploadBase64ImagesFromEditor(editor), 500);
                });
            };

            reader.readAsArrayBuffer(file);
        });
    }

    document.querySelector('form')?.addEventListener('submit', function (e) {
        e.preventDefault();

        const editor = tinymce.get('editor');
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerText = 'Đang xử lý ảnh...';

        uploadBase64ImagesFromEditor(editor, () => {
            const newContent = editor.getContent();

            // Kiểm tra độ dài nội dung (tính theo byte)
            const contentByteLength = new TextEncoder().encode(newContent).length;
            const maxLength = 65000; // tương ứng kiểu TEXT trong MySQL

            if (contentByteLength > maxLength) {
                alert('Nội dung quá dài (' + contentByteLength + ' byte). Vui lòng chia nhỏ hoặc rút gọn nội dung.');
                btn.disabled = false;
                btn.innerText = 'Lưu lại';
                return;
            }

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'NoiDung';
            hiddenInput.value = newContent;
            this.appendChild(hiddenInput);

            this.submit();
        });
    });



});

function uploadBase64ImagesFromEditor(editor, onDone) {
    const content = editor.getContent();
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = content;

    const images = tempDiv.querySelectorAll('img');
    const uploadPromises = [];

    images.forEach(img => {
        const src = img.src;
        if (src.startsWith('data:image')) {
            const promise = fetch(src)
                .then(res => res.blob())
                .then(blob => {
                    const formData = new FormData();
                    formData.append('image', blob, 'image.png');
                    formData.append('maBaiGiang', window.maBaiGiang);
                    formData.append('maBai', window.maBai || '');
                    formData.append('maNguoiDung', window.maNguoiDung);

                    return fetch('/upload-docx-image', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': window.csrfToken
                        },
                        body: formData
                    });
                })
                .then(res => res.json())
                .then(data => {
                    if (data.url) {
                        img.src = data.url;
                    }
                });

            uploadPromises.push(promise);
        }
    });

    Promise.all(uploadPromises).then(() => {
        editor.setContent(tempDiv.innerHTML);
        if (typeof onDone === 'function') onDone();
    });
}
