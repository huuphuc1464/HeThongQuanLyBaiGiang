@extends('layouts.studentLayout')

@section('title', 'Làm Bài Kiểm Tra')

@section('style')
<link rel="stylesheet" href="{{ asset('css/student/baikiemtra.css') }}">
@endsection

@section('sidebar')
@include('layouts.sidebarBaiKiemTra')
@endsection

@section('content')
<div class="container">
    <h2 class="text-center mb-4" style="color: #2c3e50;">Làm Bài Kiểm Tra</h2>

    <!-- Thông tin bài kiểm tra -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title">{{ $baiKiemTra->TenBaiKiemTra }}</h4>
            <p class="card-text"><strong>Giảng viên:</strong> {{ $baiKiemTra->giangVien->HoTen }}</p>
            <p class="card-text"><strong>Lớp học phần:</strong> {{ $baiKiemTra->lopHocPhan->TenLopHocPhan }}</p>
            <p class="card-text"><strong>Thời gian bắt đầu:</strong> {{
                \Carbon\Carbon::parse($baiKiemTra->ThoiGianBatDau)->format('H:i:s d/m/Y') }}</p>
            <p class="card-text"><strong>Thời gian kết thúc:</strong> {{
                \Carbon\Carbon::parse($baiKiemTra->ThoiGianKetThuc)->format('H:i:s d/m/Y') }}</p>
            <p class="card-text"><strong>Mô tả:</strong> {{ $baiKiemTra->MoTa ?? 'Không có mô tả' }}</p>
            <p class="card-text"><strong>Thời gian còn lại:</strong> <span id="timer" style="color: #e74c3c;">{{
                    gmdate('H:i:s', $thoiGianConLai) }}</span></p>
        </div>
    </div>

    <!-- Form làm bài -->
    <form action="{{ route('nop-bai-kiem-tra', $baiKiemTra->MaBaiKiemTra) }}" method="POST" id="examForm"
        class="needs-validation" novalidate>
        @csrf
        <input type="hidden" name="redirect_url" value="{{ route('danh-sach-bai-kiem-tra') }}">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Danh sách câu hỏi ({{ $baiKiemTra->cauHoiBaiKiemTra->count() }} câu)</h5>
            </div>
            <div class="card-body">
                @foreach($baiKiemTra->cauHoiBaiKiemTra as $index => $cauHoi)
                <div class="mb-4 p-3 border rounded" style="background-color: #f8f9fa;">
                    <h6>Câu {{ $index + 1 }}: {{ $cauHoi->CauHoi }}</h6>
                    <div class="form-check mb-2">
                        <input type="radio" class="form-check-input" name="cauhoi_{{ $cauHoi->MaCauHoi }}" value="A"
                            required>
                        <label class="form-check-label">A) {{ $cauHoi->DapAnA }}</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="radio" class="form-check-input" name="cauhoi_{{ $cauHoi->MaCauHoi }}" value="B">
                        <label class="form-check-label">B) {{ $cauHoi->DapAnB }}</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="radio" class="form-check-input" name="cauhoi_{{ $cauHoi->MaCauHoi }}" value="C">
                        <label class="form-check-label">C) {{ $cauHoi->DapAnC }}</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="cauhoi_{{ $cauHoi->MaCauHoi }}" value="D">
                        <label class="form-check-label">D) {{ $cauHoi->DapAnD }}</label>
                    </div>
                    <div class="invalid-feedback">
                        Vui lòng chọn một đáp án!
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Nút nộp bài -->
        <div class="text-center">
            <button type="button" class="btn btn-success btn-lg" id="submitBtn" data-bs-toggle="modal"
                data-bs-target="#confirmModal">
                <i class="bi bi-check-circle"></i> Nộp bài
            </button>
        </div>
    </form>

    <!-- Modal xác nhận nộp bài -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="confirmModalLabel">Xác nhận nộp bài</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn nộp bài kiểm tra? Sau khi nộp, bạn sẽ không thể chỉnh sửa nữa.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="confirmSubmit">Xác nhận nộp</button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Validation form
    (function () {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Timer
    let time = parseInt('{{ $thoiGianConLai }}'); // Thời gian còn lại từ server (tính bằng giây)
    const timerElement = document.getElementById('timer');
    const submitBtn = document.getElementById('submitBtn');
    const confirmSubmitBtn = document.getElementById('confirmSubmit');
    let isTimeUp = false;

    const countdown = setInterval(() => {
        if (time <= 0) {
            clearInterval(countdown);
            isTimeUp = true;
            timerElement.textContent = "00:00:00";
            timerElement.style.color = "#e74c3c";
            submitBtn.disabled = true;

            // Hiển thị thông báo thời gian hết
            showTimeUpNotification();
            return;
        }

        let hours = Math.floor(time / 3600);
        let minutes = Math.floor((time % 3600) / 60);
        let seconds = time % 60;

        timerElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        // Đổi màu khi còn ít thời gian (dưới 5 phút)
        if (time <= 300) {
            timerElement.style.color = "#e74c3c";
        }

        time--;
    }, 1000);

    // Hàm hiển thị thông báo thời gian hết
    function showTimeUpNotification() {
        // Tạo modal thông báo
        const modalHtml = `
            <div class="modal fade" id="timeUpModal" tabindex="-1" aria-labelledby="timeUpModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="timeUpModalLabel">
                                <i class="fas fa-clock"></i> Hết thời gian làm bài
                            </h5>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h6>Thời gian làm bài kiểm tra đã hết!</h6>
                            <p class="text-muted">Bài kiểm tra sẽ được nộp tự động. Bạn sẽ được chuyển về trang danh sách bài kiểm tra.</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" id="confirmTimeUp">
                                <i class="fas fa-check"></i> Xác nhận
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Thêm modal vào body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Hiển thị modal
        const timeUpModal = new bootstrap.Modal(document.getElementById('timeUpModal'));
        timeUpModal.show();

        // Xử lý khi người dùng xác nhận
        document.getElementById('confirmTimeUp').addEventListener('click', function () {
            timeUpModal.hide();
            // Nộp bài và chuyển hướng
            submitExamAndRedirect();
        });
    }

    // Hàm nộp bài và chuyển hướng
    function submitExamAndRedirect() {
        const form = document.getElementById('examForm');

        // Thêm input ẩn để đánh dấu là nộp tự động
        const autoSubmitInput = document.createElement('input');
        autoSubmitInput.type = 'hidden';
        autoSubmitInput.name = 'auto_submit';
        autoSubmitInput.value = '1';
        form.appendChild(autoSubmitInput);

        // Submit form và chuyển hướng
        form.submit();
    }

    // Xác nhận nộp bài
    confirmSubmitBtn.addEventListener('click', function () {
        const form = document.getElementById('examForm');
        if (form.checkValidity()) {
            // Đóng modal xác nhận
            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            confirmModal.hide();

            // Submit form
            form.submit();
        } else {
            showValidationError();
        }
    });

    // Hàm hiển thị lỗi validation
    function showValidationError() {
        // Tạo modal thông báo lỗi
        const modalHtml = `
            <div class="modal fade" id="validationErrorModal" tabindex="-1" aria-labelledby="validationErrorModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="validationErrorModalLabel">
                                <i class="fas fa-exclamation-triangle"></i> Lỗi
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                            <h6>Vui lòng hoàn thành bài kiểm tra!</h6>
                            <p class="text-muted">Bạn cần chọn đáp án cho tất cả các câu hỏi trước khi nộp bài.</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Đóng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Xóa modal cũ nếu có
        const oldModal = document.getElementById('validationErrorModal');
        if (oldModal) {
            oldModal.remove();
        }

        // Thêm modal mới vào body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Hiển thị modal
        const validationModal = new bootstrap.Modal(document.getElementById('validationErrorModal'));
        validationModal.show();
    }

    // Ngăn chặn submit khi chưa xác nhận
    document.getElementById('examForm').addEventListener('submit', function (event) {
        if (!isTimeUp && !document.getElementById('examForm').checkValidity()) {
            event.preventDefault();
            showValidationError();
        }
    });

    // Ngăn chặn người dùng rời khỏi trang khi đang làm bài
    window.addEventListener('beforeunload', function (e) {
        if (!isTimeUp) {
            e.preventDefault();
            e.returnValue = 'Bạn có chắc chắn muốn rời khỏi trang? Bài kiểm tra sẽ bị mất.';
        }
    });
</script>
@endsection