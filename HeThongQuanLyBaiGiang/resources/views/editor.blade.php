<!DOCTYPE html>
<html>
<head>
    <title>Laravel + TinyMCE + elFinder</title>

    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/elfinder.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/elfinder.min.js') }}"></script>
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <h2>Trình soạn thảo</h2>

    <form method="post">
        <textarea name="content" id="editor"></textarea>
    </form>

    <!-- Modal elFinder -->
    <div class="modal fade" id="elfinderModal">
        <div class="modal-dialog modal-lg" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div id="elfinder" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let elfinderInstance = null;

        tinymce.init({
            selector: '#editor'
            , license_key: 'gpl'
            , height: 500,

            // THÊM menu 'elfinder' vào menubar
            menubar: 'file edit view insert format tools table help elfinder',

            // Cấu hình plugin
            plugins: [
                'importcss', 'searchreplace', 'autolink'
                , 'autosave', 'save', 'directionality', 'code', 'visualblocks', 'visualchars'
                , 'fullscreen', 'image', 'link', 'media', 'codesample', 'table'
                , 'charmap', 'pagebreak', 'nonbreaking', 'anchor'
                , 'insertdatetime', 'advlist', 'lists', 'wordcount'
                , 'help', 'quickbars', 'emoticons'
            ],

            // Toolbar vẫn giữ nút elfinder
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | ' +
                'alignleft aligncenter alignright alignjustify | outdent indent | ' +
                'numlist bullist | forecolor backcolor removeformat | ' +
                'pagebreak | charmap emoticons | fullscreen preview save print | ' +
                'insertfile image media link codesample | elfinder',

            // THÊM cấu hình menu mới
            menu: {
                elfinder: {
                    title: 'Quản lý file'
                    , items: 'elfinder_menu'
                }
            },

            toolbar_sticky: true
            , relative_urls: false
            , remove_script_host: false
            , document_base_url: "{{ url('/') }}/",

            file_picker_callback: function(callback, value, meta) {
                let x = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                let y = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

                let cmsURL = '/elfinder/tinymce?type=' + meta.filetype;

                tinymce.activeEditor.windowManager.openUrl({
                    url: cmsURL
                    , title: 'elFinder File Manager'
                    , width: x * 0.8
                    , height: y * 0.8
                    , resizable: true
                    , onMessage: function(api, message) {
                        callback(message.content); // file URL
                    }
                });
            },

            setup: function(editor) {
                // NÚT toolbar
                editor.ui.registry.addButton('elfinder', {
                    text: 'elFinder'
                    , onAction: function() {
                        $('#elfinderModal').modal('show');
                        if (!window.elfinderInstance) {
                            window.elfinderInstance = $('#elfinder').elfinder({
                                customData: {
                                    _token: '{{ csrf_token() }}'
                                }
                                , lang: 'en'
                                , url: '{{ route("elfinder.connector") }}'
                                , soundPath: '{{ asset("assets/sounds") }}'
                                , getFileCallback: function(file) {
                                    editor.insertContent('<img src="' + file.url + '" />');
                                    $('#elfinderModal').modal('hide');
                                }
                                , resizable: false
                            }).elfinder('instance');
                        }
                    }
                });

                // MENU item cho menubar
                editor.ui.registry.addMenuItem('elfinder_menu', {
                    text: 'Mở elFinder'
                    , onAction: function() {
                        $('#elfinderModal').modal('show');
                        if (!window.elfinderInstance) {
                            window.elfinderInstance = $('#elfinder').elfinder({
                                customData: {
                                    _token: '{{ csrf_token() }}'
                                }
                                , lang: 'en'
                                , url: '{{ route("elfinder.connector") }}'
                                , soundPath: '{{ asset("assets/sounds") }}'
                                , getFileCallback: function(file) {
                                    editor.insertContent('<img src="' + file.url + '" />');
                                    $('#elfinderModal').modal('hide');
                                }
                                , resizable: false
                            }).elfinder('instance');
                        }
                    }
                });
            }
        });

    </script>

    <script>
        function getUrl(fileUrl) {
            window.parent.postMessage({
                mceAction: 'insert'
                , content: fileUrl
            }, '*');
        }

    </script>

</body>
</html>
