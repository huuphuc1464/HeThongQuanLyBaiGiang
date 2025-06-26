<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Admin - Hệ thống quản lý bài giảng')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #343a40;
        }

        .sidebar a {
            color: #fff;
            padding: 15px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .content {
            flex: 1;
            padding: 20px;
            margin-left: 250px;
        }

        .sidebar-item.active {
            background-color: #495057 !important;
            font-weight: 600;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar position-fixed">
        <div class="p-3 text-center border-bottom border-secondary">
            <h5 class="text-white">Quản trị hệ thống</h5>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->is('admin') ? 'active' : '' }}">
            <i class="fas fa-home me-2"></i> Dashboard</a>
        <a href="{{ route('admin.khoa.danh-sach') }}" class="sidebar-item {{ request()->is('admin/khoa*') ? 'active' : '' }}">
            <i class="fas fa-building me-2"></i> Quản lý Khoa</a>
        <a href="{{ route('admin.mon-hoc.danh-sach') }}" class="sidebar-item {{ request()->is('admin/mon-hoc*') ? 'active' : '' }}">
            <i class="fas fa-book-open me-2"></i> Quản lý Môn học</a>
        <a href="{{route('admin.giang-vien.danh-sach') }}" class="sidebar-item {{ request()->is('admin/giang-vien*') ? 'active' : '' }}">
            <i class="fas fa-chalkboard-teacher me-2"></i> Quản lý Giảng viên</a>
    </div>

    <!-- Content -->
    <div class="content">
        <nav class="navbar navbar-light bg-light mb-3">
            <div class="container-fluid justify-content-end">
                <div class="dropdown">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> {{ $tenNguoiDung }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.doi-thong-tin') }}">Thông tin tài khoản</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.doi-mat-khau') }}">Đổi mật khẩu</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a></li>

                    </ul>
                </div>
            </div>
        </nav>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Nội dung chính -->
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
