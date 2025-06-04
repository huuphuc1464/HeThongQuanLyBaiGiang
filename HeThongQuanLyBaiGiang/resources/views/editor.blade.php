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
            , height: 500
            , plugins: 'image link media code'
            , toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | link image media | code | elfinder'
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
                editor.ui.registry.addButton('elfinder', {
                    text: 'elFinder'
                    , onAction: function() {
                        $('#elfinderModal').modal('show');

                        // Khởi tạo elFinder nếu chưa có
                        if (!elfinderInstance) {
                            elfinderInstance = $('#elfinder').elfinder({
                                url: '{{ route("elfinder.connector") }}'
                                , lang: 'en'
                                , customData: {
                                    _token: '{{ csrf_token() }}'
                                }
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
