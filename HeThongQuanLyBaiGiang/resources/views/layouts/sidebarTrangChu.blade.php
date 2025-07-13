<style>
    .sidebar-wrapper {
        height: 90vh;
        display: flex;
        flex-direction: column;
        background: #fff;
        justify-content: space-between;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        padding: 8px 0 0 0;
        min-width: 220px;
    }

    .nav-link,
    .class-course-item {
        border-radius: 6px;
        transition: background 0.18s, color 0.18s;
        padding: 6px 10px;
        font-size: 15px;
    }

    .nav-link.active,
    .nav-link:hover,
    .class-course-item:hover {
        background: #e3f0ff;
        color: #1976d2 !important;
    }

    .icon-circle {
        width: 28px;
        height: 28px;
        background: #f5f6fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 6px;
    }

    .section-title {
        font-weight: 600;
        font-size: 14px;
        color: #1976d2;
        margin: 10px 0 0 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #toggleClassListBtn {
        background: none;
        border: none;
        outline: none;
        padding: 0 2px 0 0;
        color: #1976d2;
        font-size: 16px;
        cursor: pointer;
        transition: color 0.18s;
    }

    #toggleClassListBtn:hover {
        color: #0d47a1;
    }

    .sidebar-archive {
        background: #f8f9fa;
        border-radius: 6px;
        margin: 10px 8px 8px 8px;
        padding: 7px 10px;
        font-weight: 600;
        color: #6d4c41;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        transition: background 0.18s, color 0.18s;
        font-size: 15px;
    }

    .sidebar-archive.active,
    .sidebar-archive:hover {
        background: #ffe0b2;
        color: #d84315 !important;
    }

    .title {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 0;
    }

    .desc {
        font-size: 12px;
        color: #888;
        margin-bottom: 0;
    }

    .nav.flex-column {
        gap: 2px;
    }
</style>
<div class="sidebar-wrapper">
    <div>
        <div>
            <ul class="nav flex-column duongke" style="padding: 0 8px;">
                <li class="nav-item mt-1">
                    <a aria-current="page"
                        class="nav-link d-flex align-items-center gap-2 text-dark {{ request()->is('/') ? 'active' : '' }}"
                        href="/">
                        <i class="fas fa-home"></i>
                        <span>Trang chủ</span>
                    </a>
                </li>
                <li>
                    <div class="section-title">
                        <button aria-expanded="true" aria-controls="classList" id="toggleClassListBtn"
                            title="Thu gọn / Mở rộng danh sách lớp học phần" type="button">
                            <i class="fas fa-caret-down" id="toggleIcon"></i>
                        </button>
                        <i class="fas fa-graduation-cap"></i>
                        <span>Đã tham gia</span>
                    </div>
                </li>
            </ul>
        </div>
        <div id="classListWrapper" style="max-height: 60vh; overflow-y: auto; padding: 0 4px 0 4px;">
            <ul class="nav flex-column mt-2">
                @foreach ($danhSachLopHocPhanSidebar as $lop)
                <li>
                    <a href="{{ route('bai-giang.bai.tab', ['id' => $lop->MaLopHocPhan]) }}"
                        class="nav-item class-course-item d-flex align-items-center text-decoration-none text-dark">
                        <div class="icon-circle">
                            <img src="{{ $lop->AnhBaiGiang ? asset($lop->AnhBaiGiang) : asset('img/hocphan/default.png') }}"
                                alt="ảnh bài giảng" width="22" height="22" class="rounded-circle" />
                        </div>
                        <div class="text-group">
                            <p class="title">{{$lop->TenLopHocPhan}}</p>
                            <p class="desc">{{Str::limit($lop->MoTa, 32)}}</p>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div style="">
        <a class=" sidebar-archive {{ request()->routeIs('lop-hoc-phan.luu-tru') ? 'active' : '' }}"
            href="{{ route('lop-hoc-phan.luu-tru') }}">
            <i class="fas fa-archive"></i>
            <span>Lớp học phần lưu trữ</span>
        </a>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggleClassListBtn');
            const classListWrapper = document.getElementById('classListWrapper');
            const icon = document.getElementById('toggleIcon');
            let isOpen = true;

            toggleBtn.addEventListener('click', function () {
                isOpen = !isOpen;
                if (isOpen) {
                    classListWrapper.style.display = '';
                    icon.classList.remove('fa-caret-right');
                    icon.classList.add('fa-caret-down');
                } else {
                    classListWrapper.style.display = 'none';
                    icon.classList.remove('fa-caret-down');
                    icon.classList.add('fa-caret-right');
                }
            });
        });
    </script>
</div>