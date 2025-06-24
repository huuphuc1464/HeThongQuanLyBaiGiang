@extends('layouts.studentLayout')

@section('title', 'Kết Quả Bài Kiểm Tra')

@section('style')
<link rel="stylesheet" href="{{ asset('css/student/baikiemtra.css') }}">
@endsection

@section('sidebar')
@include('layouts.sidebarBaiKiemTra')
@endsection

@section('content')
<div class="container">
    <h2 class="text-center mb-4" style="color: #2c3e50;">Kết Quả Bài Kiểm Tra</h2>

    <!-- Thông tin bài kiểm tra -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title">{{ $baiKiemTra->TenBaiKiemTra }}</h4>
            <p class="card-text"><strong>Giảng viên:</strong> {{ $baiKiemTra->giangVien->HoTen }}</p>
            <p class="card-text"><strong>Lớp học phần:</strong> {{ $baiKiemTra->lopHocPhan->TenLopHocPhan }}</p>
            <p class="card-text"><strong>Thời gian nộp:</strong> {{
                \Carbon\Carbon::parse($ketQua->NgayNop)->format('H:i:s d/m/Y') }}</p>
        </div>
    </div>

    <!-- Kết quả tổng quan -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Kết Quả Tổng Quan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tổng số câu hỏi:</strong> {{ $ketQua->TongSoCauHoi }}</p>
                    <p><strong>Số câu đúng:</strong> {{ $ketQua->TongCauDung }}</p>
                    <p><strong>Số câu sai:</strong> {{ $ketQua->TongSoCauHoi - $ketQua->TongCauDung }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Điểm số:</strong>
                        <span
                            class="badge bg-{{ $ketQua->TongCauDung >= $ketQua->TongSoCauHoi * 0.5 ? 'success' : 'danger' }}">
                            {{ number_format(($ketQua->TongCauDung / $ketQua->TongSoCauHoi) * 10, 1) }}/10
                        </span>
                    </p>
                    <p><strong>Tỷ lệ đúng:</strong>
                        <span
                            class="badge bg-{{ $ketQua->TongCauDung >= $ketQua->TongSoCauHoi * 0.5 ? 'success' : 'danger' }}">
                            {{ number_format(($ketQua->TongCauDung / $ketQua->TongSoCauHoi) * 100, 1) }}%
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết từng câu hỏi -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Chi Tiết Từng Câu Hỏi</h5>
        </div>
        <div class="card-body">
            @foreach($ketQua->chiTietKetQua as $index => $chiTiet)
            <div class="mb-4 p-3 border rounded {{ $chiTiet->KetQua ? 'border-success' : 'border-danger' }}"
                style="background-color: {{ $chiTiet->KetQua ? '#d4edda' : '#f8d7da' }};">
                <h6>Câu {{ $index + 1 }}: {{ $chiTiet->cauHoi->CauHoi }}</h6>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Đáp án của bạn:</strong>
                            <span class="badge bg-{{ $chiTiet->KetQua ? 'success' : 'danger' }}">
                                {{ $chiTiet->DapAnSinhVien ?? 'Không chọn' }}
                            </span>
                        </p>
                        <p><strong>Đáp án đúng:</strong>
                            <span class="badge bg-success">{{ $chiTiet->cauHoi->DapAnDung }}</span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Kết quả:</strong>
                            @if($chiTiet->KetQua)
                            <span class="badge bg-success">Đúng</span>
                            @else
                            <span class="badge bg-danger">Sai</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mt-3">
                    <p><strong>Các đáp án:</strong></p>
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">A) {{ $chiTiet->cauHoi->DapAnA }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">B) {{ $chiTiet->cauHoi->DapAnB }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">C) {{ $chiTiet->cauHoi->DapAnC }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">D) {{ $chiTiet->cauHoi->DapAnD }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Nút quay lại -->
    <div class="text-center mt-4">
        <a href="{{ route('danh-sach-bai-kiem-tra') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách bài kiểm tra
        </a>
    </div>
</div>
@endsection