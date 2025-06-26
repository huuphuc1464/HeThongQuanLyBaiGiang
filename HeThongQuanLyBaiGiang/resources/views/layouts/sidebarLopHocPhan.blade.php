<nav class="sidebar d-flex flex-column" id="sidebar" style="height: 100vh;">
    <div class="flex-shrink-0">
        <!-- Trang chủ -->
        <ul class="nav flex-column duongKe">
            <li class="nav-item mt-2">
                <a class="nav-link d-flex align-items-center gap-2 text-dark" href="/">
                    <i class="fas fa-home"></i>
                    <span>Trang chủ</span>
                </a>
            </li>
        </ul>

        <!-- Tên lớp học phần -->
        <div class="duongKe my-1 section-title ps-3 nav-link active d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <img src=" {{ $lopHocPhan->AnhHocPhan ? asset('img/' . $lopHocPhan->AnhHocPhan) : asset('img/hocphan/default.png') }}"
                    width="30" height="32" alt="Class icon" style="border-radius: 50%; border: 1.5px solid #3a3a3a;" />
                <span class="text-truncate d-inline-block"
                    style="max-width: 220px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"
                    title="{{ $lopHocPhan->TenLopHocPhan }}">
                    {{ $lopHocPhan->TenLopHocPhan }}
                </span>
            </div>
        </div>

        <ul class="nav flex-column mt-1">
            <li class="nav-item gachDuoi">
                <a class="nav-link d-flex align-items-center gap-2 text-dark"
                    href="{{ route('danh-sach-bai-kiem-tra') }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Bài kiểm tra trắc nghiệm</span>
                </a>
            </li>
            <li class="nav-item gachDuoi">
                <a class="nav-link d-flex align-items-center gap-2 text-dark" href="{{ route('hoc-phan.bai-giang.tab', ['id' => $id, 'tab' => 'su-kien-zoom']) }}">
                    <i class="fas fa-video"></i>
                    <span>Sự kiện học trực tuyến</span>
                </a>
            </li>
        </ul>
    </div>

    <div style="flex-grow: 1; overflow-y: auto;">
        <div class="accordion" id="sidebarAccordion">
            @foreach ($danhSachBaiGiangSidebar as $tenChuong => $dsBai)
            @php $chuongId = Str::slug($tenChuong) . '-' . uniqid(); @endphp
            <div class="accordion-item border-0">
                <h2 class="accordion-header" id="heading-{{ $chuongId }}">
                    <button class="accordion-button collapsed bg-white text-dark py-2 px-3 fw-bold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $chuongId }}" aria-expanded="false"
                        aria-controls="collapse-{{ $chuongId }}">
                        <span class="w-100 d-inline-block text-truncate" style="font-size:14px;">
                            {{ $tenChuong }}
                        </span>
                    </button>
                </h2>
                <div id="collapse-{{ $chuongId }}" class="accordion-collapse collapse"
                    aria-labelledby="heading-{{ $chuongId }}" data-bs-parent="#sidebarAccordion">
                    <div class="accordion-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach ($dsBai as $tenBai => $dsBaiGiang)
                            {{-- Tên bài --}}
                            <li class="list-group-item bg-light text-dark fw-semibold px-3 py-2 ps-4">
                                <div class="d-inline-block text-truncate w-100" style="font-size:14px;"
                                    title="{{ $tenBai }}">
                                    {{ $tenBai }}
                                </div>
                            </li>
                            {{-- Bài giảng --}}
                            @foreach ($dsBaiGiang as $baiGiang)
                            <li class="list-group-item p-0 ps-3">
                                <a href="{{ route('bai-giang.chi-tiet', ['id' => $id, 'maBaiGiang' => $baiGiang->MaBaiGiang]) }}"
                                    class="d-flex justify-content-between align-items-center px-3 py-2 text-decoration-none text-dark w-100 h-100">
                                    <div class="text-truncate w-100 pe-2" style="font-size:14px;"
                                        title="{{ $baiGiang->TenBaiGiang }}">
                                        {{ $baiGiang->TenBaiGiang }}
                                    </div>
                                </a>
                            </li>
                            @endforeach
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</nav>