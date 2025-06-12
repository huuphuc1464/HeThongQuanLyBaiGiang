<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Đăng nhập hệ thống quản lý bài giảng</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/login/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/login/login.css') }}">
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script> --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
</head>

<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                @yield('content')
                @if(session('swal_success'))
                <script>
                    swal({
                        text: "{{ session('swal_success') }}"
                        , icon: "success"
                        , button: false
                        , timer: 1500
                    });

                </script>
                @endif

                @if(session('swal_error'))
                <script>
                    swal({
                        text: "{{ session('swal_error') }}"
                        , icon: "error"
                        , button: "Thử lại"
                    });

                </script>
                @endif

                @if(session('swal_warning'))
                <script>
                    swal({
                        text: "{{ session('swal_warning') }}"
                        , icon: "warning"
                        , button: "Thử lại"
                    });

                </script>
                @endif
                <!--=====FOOTER======-->
                <div class="text-center p-t-70 txt2">
                    Hệ thống quản lý bài giảng trực tuyến <i class="far fa-copyright" aria-hidden="true"></i>
                    <script type="text/javascript">
                        document.write(new Date().getFullYear());

                    </script>
                </div>
            </div>
        </div>
    </div>

    <!--Javascript-->
    <script src="https://code.jquery.com/jquery-4.0.0-beta.2.min.js"></script>

    @yield('scripts')
    <script type="text/javascript">
        //show - hide mật khẩu
        function myFunction() {
            var x = document.getElementById("myInput");
            if (x.type === "password") {
                x.type = "text"
            } else {
                x.type = "password";
            }
        }
        $(".click-eye").click(function() {
            $(this).toggleClass("bx-show bx-hide");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });

    </script>
</body>

</html>
