<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>
        @yield('title')
    </title>
    <link crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/student/main.css') }}">
    @yield('style')
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom px-3 fixed-top">
        <div class="container-fluid">
            {{-- Nút toggle sidebar --}}
            <button class="navbar-toggler me-2" type="button" id="anHienSidebarBtn"
                title="Thu gọn / Mở rộng thanh sidebar" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            {{-- Logo --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                <img class="logo-img" alt="Website logo placeholder image" height="24"
                    src="{{asset('/img/web/logo.jpg')}}" width="30" />
                <span>UniLecture</span>
            </a>
            {{-- Nút toggle menu navbar --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#anHienNavbar"
                aria-controls="anHienNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            {{-- Nội dung navbar --}}
            <div class="collapse navbar-collapse justify-content-between" id="anHienNavbar">
                {{-- Trái: Dropdown Khoa --}}
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle text-dark fw-bold text-nowrap"
                            style="border: none; box-shadow: none;" type="button" id="khoaDropdownBtn"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Khoa
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="khoaDropdownBtn">
                            @foreach ($danhSachKhoa as $khoa)
                            <li class="dropdown-submenu dropend">
                                <a class="dropdown-item dropdown-toggle text-truncate" href="#"
                                    data-bs-toggle="dropdown">{{ $khoa['TenKhoa'] }}</a>
                                <ul class="dropdown-menu">
                                    @foreach ($khoa['BaiGiang'] as $baiGiang)
                                    <li class="dropdown-submenu dropend">
                                        <a class="dropdown-item dropdown-toggle text-truncate" href="#"
                                            data-bs-toggle="dropdown">{{ $baiGiang['TenBaiGiang'] }}</a>
                                        <ul class="dropdown-menu">
                                            @foreach ($baiGiang['GiangVien'] as $gv)
                                            <li>
                                                <a class="dropdown-item text-truncate"
                                                    href="{{ route('trang-chu', ['giang_vien' => $gv['MaGiangVien'], 'bai_giang' => $baiGiang['MaBaiGiang']]) }}">{{
                                                    $gv['TenGiangVien']
                                                    }}
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Giữa: Search --}}
                <form action="{{ route('trang-chu') }}" method="GET"
                    class="d-flex me-auto mx-lg-auto my-2 my-lg-0 flex-grow-1 justify-content-end justify-content-lg-center px-3"
                    role="search" style="max-width: 600px;">
                    <div class="position-relative w-100">
                        <input class="form-control rounded-pill" type="search" name="search"
                            placeholder="Tìm kiếm theo lớp học phần, học phần, giảng viên, ..." aria-label="Search" />
                        <i class="fas fa-search search-icon-inside"></i>
                    </div>
                </form>

                {{-- Phải: Thông báo + Tên sinh viên --}}
                <div class="d-flex align-items-center">
                    {{-- Chuông thông báo --}}
                    <div class="dropdown me-3">
                        <button class="btn btn-link text-dark position-relative" type="button" id="notificationDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false" aria-label="Thông báo">
                            <i class="fas fa-bell fa-lg"></i>
                        </button>
                        <div class="dropdown-menu shadow border-0 mt-2 p-0" aria-labelledby="notificationDropdown"
                            style="width: 300px; border-radius: 12px; overflow: hidden;">
                            <div class="p-3 border-bottom">
                                <h6 class="fw-bold mb-0">Thông báo</h6>
                            </div>
                            <div style="max-height: 250px; overflow-y: auto;">
                                @if ($thongBao->isEmpty())
                                <div class="p-3 text-muted">Chưa có thông báo nào</div>
                                @else
                                @foreach ($thongBao as $tb)
                                <div class="dropdown-item text-truncate" title="{{ $tb->NoiDung }}">
                                    {{ $tb->NoiDung }}
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Tên người dùng --}}
                    <div class="dropdown d-flex align-items-center text-nowrap">
                        <button
                            class="btn btn-link dropdown-toggle d-flex align-items-center text-dark fw-bold text-decoration-none"
                            type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <input type="hidden" name="maNguoiDung" value="{{ $maNguoiDung }}">
                            <span class="me-2">Xin chào {{ $tenNguoiDung }}</span>
                            <i class="fas fa-user-circle fa-lg"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('sinhvien.doi-thong-tin') }}">Thay đổi thông tin
                                    cá nhân</a></li>
                            <li><a class="dropdown-item" href="{{ route('sinhvien.doi-mat-khau') }}">Đổi mật khẩu</a>
                            </li>

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
                </div>
            </div>
        </div>
    </nav>
    <nav class="sidebar" id="sidebar" aria-label="Sidebar navigation">
        @yield('sidebar')
    </nav>

    <main class="content-area" id="mainContent" tabindex="-1">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Render nội dung --}}
        @yield('content')
    </main>

    <script crossorigin="anonymous" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="{{ asset('/js/student/main.js') }}"></script>
    @yield('scripts')
</body>

</html>