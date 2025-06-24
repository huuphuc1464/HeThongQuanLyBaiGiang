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

        <!-- Tiêu đề Bài Kiểm Tra -->
        <div class="duongKe my-1 section-title ps-3 nav-link active d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-list"></i>
                <span class="text-truncate d-inline-block"
                    style="max-width: 220px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                    Bài Kiểm Tra
                </span>
            </div>
        </div>

        <ul class="nav flex-column mt-1">
            <li class="nav-item gachDuoi">
                <a class="nav-link d-flex align-items-center gap-2 text-dark {{ request()->routeIs('danh-sach-bai-kiem-tra') ? 'active' : '' }}"
                    href="{{ route('danh-sach-bai-kiem-tra') }}">
                    <i class="fas fa-list"></i>
                    <span>Danh sách bài kiểm tra</span>
                </a>
            </li>
            <li class="nav-item gachDuoi">
                <a class="nav-link d-flex align-items-center gap-2 text-dark" href="/">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại trang chủ</span>
                </a>
            </li>
        </ul>
    </div>

    <div style="flex-grow: 1; overflow-y: auto;">
        <div class="p-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Hướng dẫn</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <ul class="list-unstyled mb-0">
                            <li>• Chọn bài kiểm tra đang diễn ra</li>
                            <li>• Làm bài trước khi hết thời gian</li>
                            <li>• Xem kết quả sau khi nộp bài</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
</nav>