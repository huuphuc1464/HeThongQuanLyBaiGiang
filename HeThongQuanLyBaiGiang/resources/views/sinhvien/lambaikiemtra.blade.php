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
        class="needs-validation" novalidate onsubmit="return false;">
        @csrf
        <input type="hidden" name="redirect_url" value="{{ route('danh-sach-bai-kiem-tra') }}">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Danh sách câu hỏi ({{ $baiKiemTra->cauHoiBaiKiemTra->count() }} câu)</h5>
            </div>
            <div class="card-body">
                @foreach($baiKiemTra->cauHoiBaiKiemTra as $index => $cauHoi)
                <div class="mb-4 p-3 border rounded" style="background-color: #f8f9fa;"
                    id="question_{{ $cauHoi->MaCauHoi }}">
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
            <button type="button" class="btn btn-success btn-lg" id="submitBtn">
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

    <!-- Modal thành công -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Nộp bài thành công</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Nộp bài kiểm tra thành công!
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    console.log(@json(csrf_token()));
</script>
<script>
    window.csrfToken = @json(csrf_token())
</script>


<script src="{{asset('js/student/lamBaiKiemTra.js')}}"></script>
@endsection