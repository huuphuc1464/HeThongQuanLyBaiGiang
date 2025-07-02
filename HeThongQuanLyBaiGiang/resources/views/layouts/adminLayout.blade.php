<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniLecture - Admin Dashboard</title>
    <link rel="icon" type="shortcut icon" href=" {{ asset('img/web/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/main.css') }}" />
    @yield('styles')
</head>

<body class="bg-light min-vh-100 d-flex flex-column">
    <div class="d-flex flex-grow-1">
        <!-- Sidebar -->
        <aside class="sidebar d-flex flex-column">
            <div class="p-3 d-flex align-items-center border-bottom border-secondary">
                <img src="{{ asset('/img/web/logo.jpg') }}" alt="UniLecture Logo" class="logo me-2">
                <h1 class="h5 mb-0 fw-bold reload-text" style="cursor: pointer;">UniLecture Admin</h1>
            </div>
            <nav class="nav flex-column mt-3">
                <a href="{{ url('/admin') }}" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ url('/admin/khoa') }}" class="nav-link {{ request()->is('admin/khoa*') ? 'active' : '' }}">
                    <i class="fas fa-building me-2"></i> Quản lý Khoa
                </a>
                <a href="{{ url('/admin/giang-vien') }}"
                    class="nav-link {{ request()->is('admin/giang-vien*') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher me-2"></i> Quản lý Giảng viên
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-grow-1 content">
            <!-- Header -->
            <header
                class="bg-white shadow-sm p-3 d-flex justify-content-end align-items-center border border-secondary rounded-3"
                style="border-width:1px !important;">
                <button id="menu-toggle" class="btn btn-outline-secondary d-md-none me-2 order-0">
                    <i class="fas fa-bars text-dark"></i>
                </button>
                <div class="dropdown ms-auto order-1">
                    <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2">{{ $tenNguoiDung }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.doi-thong-tin') }}">Thông tin tài khoản</a>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('admin.doi-mat-khau') }}">Đổi mật khẩu</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <li><a class="dropdown-item text-danger" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng
                                xuất</a></li>
                    </ul>
                </div>
            </header>

            <!-- Content -->
            <main class="p-4">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    {{ session('error') }}
                </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('menu-toggle').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('open');
        });
        document.querySelector('.reload-text').addEventListener('click', () => {
            location.reload();
        });
    </script>
    @yield('scripts')
</body>

</html>