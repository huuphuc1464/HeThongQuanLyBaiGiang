<ul class="nav flex-column duongke">
    <li class="nav-item mt-2">
        <a aria-current="page" class="nav-link active d-flex align-items-center gap-2 text-dark" href="/">
            <i class="fas fa-home"></i>
            <span>
                Trang chủ
            </span>
        </a>
    </li>
</ul>
<div class="section-title">
    <button aria-expanded="true" aria-controls="classList" id="toggleClassListBtn"
        title="Thu gọn / Mở rộng danh sách lớp học phần">
        <i class="fas fa-caret-down" id="toggleIcon"></i>
    </button>
    <i class="fas fa-graduation-cap"></i>
    <span>
        Đã tham gia
    </span>
</div>
<ul class="nav flex-column mt-3" id="classList" style="max-height: calc(100vh - 150px); overflow-y: auto;">
    @foreach ($danhSachLopHocPhanSidebar as $lop)
    <a href="{{ route('hoc-phan.bai-giang.tab', ['id' => $lop->MaLopHocPhan]) }}"
        class="nav-item class-course-item d-flex text-decoration-none text-dark">
        <div class="icon-circle">
            <img src="{{ $lop->AnhHocPhan ? asset('img/' . $lop->AnhHocPhan) : asset('img/hocphan/default.png') }}"
                alt="icon" width="25" height="25" class="rounded-circle" />
        </div>
        <div class="text-group">
            <p class="title mb-2">{{Str::limit($lop->TenLopHocPhan, 25)}}</p>
            <p class="desc mb-2">{{Str::limit($lop->MoTa, 35)}}</p>
        </div>
    </a>
    @endforeach
</ul>