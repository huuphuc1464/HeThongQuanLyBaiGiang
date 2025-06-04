<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>elFinder 2.0</title>

    <!-- jQuery + jQuery UI -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

    <!-- elFinder CSS + JS -->
    <link rel="stylesheet" href="{{ asset('assets/css/elfinder.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <script src="{{ asset('assets/js/elfinder.min.js') }}"></script>
</head>

<body style="margin:0;padding:0;">
    <div id="elfinder"></div>

    <script type="text/javascript">
        $().ready(function() {
            $('#elfinder').elfinder({
                customData: {
                    _token: '{{ csrf_token() }}'
                }
                , url: '{{ route("elfinder.connector") }}'
                , getFileCallback: function(file) {
                    // Gửi URL file về trình soạn thảo TinyMCE
                    window.parent.postMessage({
                        content: file.url
                    }, '*');
                }
                , resizable: false
            });
        });

    </script>
</body>
</html>

