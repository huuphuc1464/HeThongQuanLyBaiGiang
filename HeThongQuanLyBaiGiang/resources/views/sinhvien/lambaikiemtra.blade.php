@extends('layouts.studentLayout')

@section('title', 'Làm Bài Kiểm Tra')

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/student/baikiemtra.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('sidebar')
@include('layouts.sidebarBaiKiemTra')
@endsection

@section('content')
<div class="container">
    <h2 class="text-center mb-5" style="color: #1f2937; font-weight: 700; font-size: 2em;">Làm Bài Kiểm Tra</h2>

    <!-- Exam Information -->
    <div class="card mb-5">
        <div class="card-body p-4">
            <h4 class="card-title mb-4" style="color: #1e40af; font-weight: 600;">{{ $baiKiemTra->TenBaiKiemTra }}</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <p class="card-text"><i class="bi bi-person-circle me-2"></i><strong>Giảng viên:</strong> {{
                        $baiKiemTra->giangVien->HoTen }}</p>
                    <p class="card-text"><i class="bi bi-book me-2"></i><strong>Lớp học phần:</strong> {{
                        $baiKiemTra->lopHocPhan->TenLopHocPhan }}</p>
                    <p class="card-text"><i class="bi bi-clock me-2"></i><strong>Thời gian bắt đầu:</strong> {{
                        \Carbon\Carbon::parse($baiKiemTra->ThoiGianBatDau)->format('H:i:s d/m/Y') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="card-text"><i class="bi bi-clock-history me-2"></i><strong>Thời gian kết thúc:</strong> {{
                        \Carbon\Carbon::parse($baiKiemTra->ThoiGianKetThuc)->format('H:i:s d/m/Y') }}</p>
                    <p class="card-text"><i class="bi bi-info-circle me-2"></i><strong>Mô tả:</strong> {{
                        $baiKiemTra->MoTa ?? 'Không có mô tả' }}</p>
                    <p class="card-text"><i class="bi bi-hourglass-split me-2"></i><strong>Thời gian còn lại:</strong>
                        <span id="timer">{{ gmdate('H:i:s', $thoiGianConLai) }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Form -->
    <form action="{{ route('nop-bai-kiem-tra', $baiKiemTra->MaBaiKiemTra) }}" method="POST" id="examForm"
        class="needs-validation" novalidate onsubmit="return false;">
        @csrf
        <input type="hidden" name="redirect_url"
            value="{{ route('danh-sach-bai-kiem-tra', $baiKiemTra->MaLopHocPhan) }}">
        <div class="card mb-5">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Danh sách câu hỏi ({{
                    $baiKiemTra->cauHoiBaiKiemTra->count() }} câu)</h5>
            </div>
            <div class="card-body p-4">
                @foreach($baiKiemTra->cauHoiBaiKiemTra as $index => $cauHoi)
                <div class="question-container" id="question_{{ $cauHoi->MaCauHoi }}">
                    <textarea class="mb-4 p-2 w-100 auto-resize" disabled readonly
                        style="font-weight: 600; color: #1f2937;">Câu {{ $index + 1 }}: {{ $cauHoi->CauHoi
                        }}</textarea>
                    <div class="answer-options">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="cauhoi_{{ $cauHoi->MaCauHoi }}"
                                id="cauhoi_{{ $cauHoi->MaCauHoi }}_A" value="A" required>
                            <label class="form-check-label" for="cauhoi_{{ $cauHoi->MaCauHoi }}_A">A) {{ $cauHoi->DapAnA
                                }}</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="cauhoi_{{ $cauHoi->MaCauHoi }}"
                                id="cauhoi_{{ $cauHoi->MaCauHoi }}_B" value="B">
                            <label class="form-check-label" for="cauhoi_{{ $cauHoi->MaCauHoi }}_B">B) {{ $cauHoi->DapAnB
                                }}</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="cauhoi_{{ $cauHoi->MaCauHoi }}"
                                id="cauhoi_{{ $cauHoi->MaCauHoi }}_C" value="C">
                            <label class="form-check-label" for="cauhoi_{{ $cauHoi->MaCauHoi }}_C">C) {{ $cauHoi->DapAnC
                                }}</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="cauhoi_{{ $cauHoi->MaCauHoi }}"
                                id="cauhoi_{{ $cauHoi->MaCauHoi }}_D" value="D">
                            <label class="form-check-label" for="cauhoi_{{ $cauHoi->MaCauHoi }}_D">D) {{ $cauHoi->DapAnD
                                }}</label>
                        </div>
                        <div class="invalid-feedback">
                            Vui lòng chọn một đáp án!
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mb-4">
            <button type="button" class="btn btn-success btn-lg" id="submitBtn">
                <i class="bi bi-check-circle me-2"></i>Nộp bài
            </button>
        </div>
    </form>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="confirmModalLabel"><i class="bi bi-exclamation-triangle me-2"></i>Xác
                        nhận nộp bài</h5>
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

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel"><i class="bi bi-check-circle-fill me-2"></i>Nộp bài
                        thành công</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Nộp bài kiểm tra thành công! Bạn sẽ được chuyển hướng về danh sách bài kiểm tra.
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận rời trang -->
    <div class="modal fade" id="leaveConfirmModal" tabindex="-1" aria-labelledby="leaveConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="leaveConfirmModalLabel"><i
                            class="bi bi-exclamation-triangle me-2"></i>Xác nhận rời trang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn đang làm bài kiểm tra. Nếu rời trang, bài kiểm tra sẽ được nộp ngay lập tức. Bạn có chắc chắn
                    muốn tiếp tục?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelLeaveBtn">Hủy</button>
                    <button type="button" class="btn btn-primary" id="confirmLeaveBtn">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    window.csrfToken = @json(csrf_token());
    window.thoiGianConLai = {{ $thoiGianConLai }};

</script>
<script>
    document.querySelectorAll('.form-check').forEach(option => {
        option.addEventListener('click', () => {
            const radio = option.querySelector('.form-check-input');
            radio.checked = true;
            option.parentElement.querySelectorAll('.form-check').forEach(sibling => {
                sibling.classList.remove('selected');
            });
            option.classList.add('selected');
        });
    });

</script>
<script src="{{ asset('js/student/lamBaiKiemTra.js') }}"></script>
@endsection