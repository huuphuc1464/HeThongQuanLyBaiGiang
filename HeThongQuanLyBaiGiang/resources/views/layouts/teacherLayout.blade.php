<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="icon" type="shortcut icon" href=" {{ asset('img/web/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('/css/teacher/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/binh-luan-realtime.css') }}">
    @yield('styles')
</head>

<body onload="time()">

    <!-- Sidebar -->
    <div class="sidebar position-fixed">
        <div class="px-2 py-3 d-flex align-items-center border-bottom border-secondary">
            <img width="40" src="{{ asset('/img/web/logo.jpg') }}" alt="UniLecture Logo" class="me-2 rounded-circle">
            <h5 class="mb-0 fw-bold reload-text text-white" style="cursor: pointer;">UniLecture Teacher</h5>
        </div>
        <a href="{{ route('giangvien.dashboard') }}"
            class="sidebar-item {{ request()->is('giang-vien') ? 'active' : '' }}">
            <i class="fas fa-home me-2"></i> Dashboard</a>
        <a href="{{ route('giangvien.bai-giang.danh-sach') }}"
            class="sidebar-item {{ request()->is('giang-vien/bai-giang*') ? 'active' : '' }}">
            <i class="fas fa-book-open me-2"></i> Quản lý Bài Giảng</a>
        <a href="{{ route('giangvien.su-kien-zoom.danhsach') }}"
            class="sidebar-item {{ request()->is('giang-vien/su-kien-zoom*') ? 'active' : '' }}">
            <i class="fas fa-video me-2"></i> Quản lý Sự Kiện Zoom
        </a>
        <a href="{{ route('giangvien.bai-kiem-tra.danh-sach') }}"
            class="sidebar-item {{ request()->is('giang-vien/bai-kiem-tra*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list me-2"></i> Quản lý Bài Kiểm Tra
        </a>
        <a href="{{ route('giangvien.lophocphan.danhsach') }}"
            class="sidebar-item {{ request()->is('giang-vien/lop-hoc-phan*') ? 'active' : '' }}">
            <i class="fas fa-chalkboard-teacher me-2"></i> Quản lý Lớp Học Phần</a>
    </div>

    <!-- Content -->
    <div class="content">
        <nav class="navbar navbar-light bg-light mb-3">
            <div class="container-fluid justify-content-end">
                <div class="dropdown">
                    <input type="hidden" name="maNguoiDung" value="{{ $maNguoiDung }}">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="userDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> {{ $tenNguoiDung }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('giangvien.doi-thong-tin') }}">Thay đổi thông tin cá
                                nhân</a></li>
                        <li><a class="dropdown-item " href="{{ route('giangvien.doi-mat-khau') }}">Đổi mật khẩu</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <li>
                            <a class="dropdown-item text-danger" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Đăng xuất
                            </a>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>

        <main class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="tieuDe d-flex align-center justify-content-between bg-white">
                        <ul class="mb-0 breadcrumb">
                            <li class="breadcrumb-item">
                                <div class="text-dark"><b>@yield('tenTrang')</b></div>
                            </li>
                        </ul>
                        <div id="clock"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('errorSystem'))
                <div class="alert alert-danger">{{ session('errorSystem') }}</div>
                @endif

                @if(session('warning'))
                <div class="alert alert-warning">{!! session('warning') !!}</div>
                @endif

                <!-- Nội dung chính -->
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('/js/teacher/main.js') }}"></script>
    <script src="{{ asset('/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('/js/tinymce-init.js') }}"></script>
    <script src="https://unpkg.com/vue@3.5.17/dist/vue.global.js"></script>
    <script src="{{ asset('/js/components/BinhLuanRealtime.js') }}"></script>
    <!-- Laravel Echo và Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
    <script>
        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env("PUSHER_APP_KEY") }}',
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true
        });
    </script>

    @yield('scripts')
</body>

</html>