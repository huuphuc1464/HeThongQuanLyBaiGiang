<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('/css/teacher/main.css') }}">
    @yield('style')
</head>

<body onload="time()">

    <!-- Sidebar -->
    <div class="sidebar position-fixed">
        <div class="p-3 text-center border-bottom border-secondary">
            <h5 class="text-white">Quản trị hệ thống</h5>
        </div>
        <a href="/teacher/dashboard"><i class="fas fa-home me-2"></i> Dashboard</a>
        <a href="/teacher/quanLyHocPhan"><i class="fas fa-book-open me-2"></i> Quản lý Học Phần</a>
        <a href="/teacher/quanLySuKienZoom"><i class="fas fa-video me-2"></i> Quản lý Sự Kiện Zoom</a>
        <a href="/teacher/baiKiemTra"><i class="fas fa-clipboard-list me-2"></i> Quản lý Bài Kiểm Tra</a>
        <a href="/teacher/lopHocPhan"><i class="fas fa-chalkboard-teacher me-2"></i> Quản lý Lớp Học Phần</a>
    </div>

    <!-- Content -->
    <div class="content">
        <nav class="navbar navbar-light bg-light mb-3">
            <div class="container-fluid justify-content-end">
                <div class="dropdown">
                    <input type="hidden" name="maNguoiDung" value="{{ $maNguoiDung }}">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> {{ $tenNguoiDung }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('giangvien.doi-thong-tin') }}">Thay đổi thông tin cá nhân</a></li>
                        <li><a class="dropdown-item " href="{{ route('giangvien.doi-mat-khau') }}">Đổi mật khẩu</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
                            <li class="breadcrumb-item"><a href="#" class="text-dark"><b>@yield('tenTrang')</b></a></li>
                        </ul>
                        <div id="clock"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
            @endif --}}

            <!-- Nội dung chính -->
            @yield('content')
    </div>
    </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('/js/teacher/main.js') }}"></script>
    @yield('scripts')
</body>

</html>
