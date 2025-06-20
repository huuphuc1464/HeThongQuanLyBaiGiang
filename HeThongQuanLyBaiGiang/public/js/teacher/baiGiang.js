const chuongBai = window.chuongBai || {};

function onChangeChuong() {
    const chuongSelect = document.getElementById('selectChuong');
    const baiSelect = document.getElementById('selectBai');
    const inputChuongMoi = document.getElementById('inputChuongMoi');
    const inputBaiMoi = document.getElementById('inputBaiMoi');
    const selectedChuong = chuongSelect.value;

    if (selectedChuong === 'other') {
        inputChuongMoi.style.display = 'block';
        inputChuongMoi.setAttribute('name', 'TenChuong');
        inputChuongMoi.setAttribute('required', true);

        chuongSelect.removeAttribute('name');
        chuongSelect.removeAttribute('required');

        inputBaiMoi.style.display = 'block';
        inputBaiMoi.setAttribute('name', 'TenBai');
        inputBaiMoi.setAttribute('required', true);

        baiSelect.innerHTML = '<option value="">-- Nhập bài mới --</option>';
        baiSelect.removeAttribute('name');
        baiSelect.removeAttribute('required');
    } else {
        inputChuongMoi.style.display = 'none';
        inputChuongMoi.removeAttribute('name');
        inputChuongMoi.removeAttribute('required');

        chuongSelect.setAttribute('name', 'TenChuong');
        chuongSelect.setAttribute('required', true);

        baiSelect.innerHTML = '<option value="">-- Chọn bài --</option>';
        if (chuongBai[selectedChuong]) {
            chuongBai[selectedChuong].forEach(function (bai) {
                const option = document.createElement('option');
                option.value = bai;
                option.text = bai;
                baiSelect.appendChild(option);
            });
            const other = document.createElement('option');
            other.value = 'other';
            other.text = 'Khác';
            baiSelect.appendChild(other);
        }

        inputBaiMoi.style.display = 'none';
        inputBaiMoi.removeAttribute('name');
        inputBaiMoi.removeAttribute('required');

        baiSelect.setAttribute('name', 'TenBai');
        baiSelect.setAttribute('required', true);
    }
}

function onChangeBai() {
    const baiSelect = document.getElementById('selectBai');
    const inputBaiMoi = document.getElementById('inputBaiMoi');

    if (baiSelect.value === 'other') {
        inputBaiMoi.style.display = 'block';
        inputBaiMoi.setAttribute('name', 'TenBai');
        inputBaiMoi.setAttribute('required', true);

        baiSelect.removeAttribute('name');
        baiSelect.removeAttribute('required');
    } else {
        inputBaiMoi.style.display = 'none';
        inputBaiMoi.removeAttribute('name');
        inputBaiMoi.removeAttribute('required');

        baiSelect.setAttribute('name', 'TenBai');
        baiSelect.setAttribute('required', true);
    }
}

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
function handleCancelBaiGiang({ routeUrl, maHocPhan, maBaiGiang = null }) {
    if (confirm('Bạn có chắc muốn hủy bỏ bài giảng không?')) {
        fetch(routeUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                MaHocPhan: maHocPhan,
                MaBaiGiang: maBaiGiang
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
