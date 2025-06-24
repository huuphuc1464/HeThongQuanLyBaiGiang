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
    let time = parseInt('{{ $thoiGianConLai }}');
    const timerElement = document.getElementById('timer');
    const submitBtn = document.getElementById('submitBtn');
    const confirmSubmitBtn = document.getElementById('confirmSubmit');
    let isTimeUp = false;
    let isSubmitting = false;

    function disableAllInputs() {
        document.querySelectorAll('#examForm input, #examForm button').forEach(el => el.disabled = true);
    }
    function showLoading() {
        let loading = document.createElement('div');
        loading.id = 'loadingOverlay';
        loading.style.position = 'fixed';
        loading.style.top = 0;
        loading.style.left = 0;
        loading.style.width = '100vw';
        loading.style.height = '100vh';
        loading.style.background = 'rgba(255,255,255,0.7)';
        loading.style.display = 'flex';
        loading.style.alignItems = 'center';
        loading.style.justifyContent = 'center';
        loading.innerHTML = '<div class="spinner-border text-primary" style="width: 4rem; height: 4rem;"></div><span class="ms-3">Đang nộp bài...</span>';
        document.body.appendChild(loading);
    }

    const countdown = setInterval(() => {
        if (time <= 0 && !isSubmitting) {
            clearInterval(countdown);
            isTimeUp = true;
            timerElement.textContent = "00:00:00";
            timerElement.style.color = "#e74c3c";
            submitBtn.disabled = true;
            showTimeUpNotification();
            return;
        }
        if (time === 60) {
            alert('Chỉ còn 1 phút làm bài!');
        }
        let hours = Math.floor(time / 3600);
        let minutes = Math.floor((time % 3600) / 60);
        let seconds = time % 60;
        timerElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        if (time <= 300) {
            timerElement.style.color = "#e74c3c";
            if (time % 2 === 0) {
                timerElement.style.opacity = "0.5";
            } else {
                timerElement.style.opacity = "1";
            }
        }
        time--;
    }, 1000);

    function showTimeUpNotification() {
        if (isSubmitting) return;
        isSubmitting = true;
        disableAllInputs();
        showLoading();
        setTimeout(() => {
            submitExamAndRedirect();
        }, 1500);
    }
    function submitExamAndRedirect() {
        if (isSubmitting) {
            const form = document.getElementById('examForm');
            form.submit();
        }
    }
    confirmSubmitBtn.addEventListener('click', function () {
        const form = document.getElementById('examForm');
        if (!isTimeUp) {
            disableAllInputs();
            showLoading();
            form.submit();
        }
    });
    window.addEventListener('beforeunload', function (e) {
        if (!isTimeUp && !isSubmitting) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    document.addEventListener('visibilitychange', function () {
        if (document.hidden && !isTimeUp && !isSubmitting) {
            // Có thể thêm code xử lý khi sinh viên chuyển tab
            console.log('Sinh viên đã chuyển tab!');
        }
    });
</script>
@endsection