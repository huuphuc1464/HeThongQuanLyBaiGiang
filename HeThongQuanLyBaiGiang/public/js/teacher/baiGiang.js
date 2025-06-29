// TinyMCE
function initTinyMCE(maHocPhan, csrfToken) {
    tinymce.init({
        selector: '#editor',
        license_key: 'gpl',
        height: 500,
        menubar: 'file edit view insert format tools table help elfinder',
        plugins: [
            'importcss', 'searchreplace', 'autolink',
            'autosave', 'save', 'directionality', 'code', 'visualblocks', 'visualchars',
            'fullscreen', 'image', 'link', 'media', 'codesample', 'table',
            'charmap', 'pagebreak', 'nonbreaking', 'anchor',
            'insertdatetime', 'advlist', 'lists', 'wordcount',
            'help', 'quickbars', 'emoticons'
        ],
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | ' +
            'alignleft aligncenter alignright alignjustify | outdent indent | ' +
            'numlist bullist | forecolor backcolor removeformat | ' +
            'pagebreak | charmap emoticons | fullscreen preview save print | ' +
            'insertfile image media link codesample | elfinder',
        menu: {
            elfinder: {
                title: 'Quản lý file',
                items: 'elfinder_menu'
            }
        },
        toolbar_sticky: true,
        relative_urls: false,
        remove_script_host: false,
        document_base_url: window.APP_URL + '/',
        setup: function (editor) {
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
