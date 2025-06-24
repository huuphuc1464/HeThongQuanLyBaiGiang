@extends('layouts.teacherLayout')

@section('title', 'Giảng viên - Chi tiết bài kiểm tra')
@section('tenTrang', 'Chi tiết bài kiểm tra')

@section('styles')
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .question-group {
        border: 1px solid #dee2e6;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Chi tiết bài kiểm tra: {{ $baiKiemTra->TenBaiKiemTra }}</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('errorSystem'))
            <div class="alert alert-danger">{{ session('errorSystem') }}</div>
            @endif
            <div class="mb-3">
                <strong>Tên bài kiểm tra:</strong> {{ $baiKiemTra->TenBaiKiemTra }}
            </div>
            <div class="mb-3">
                <strong>Lớp học phần:</strong> {{ $baiKiemTra->MaLopHocPhan }}
            </div>
            <div class="mb-3">
                <strong>Thời gian bắt đầu:</strong> {{ \Carbon\Carbon::parse($baiKiemTra->ThoiGianBatDau)->format('H:i:s
                d/m/Y') }}
            </div>
            <div class="mb-3">
                <strong>Thời gian kết thúc:</strong> {{
                \Carbon\Carbon::parse($baiKiemTra->ThoiGianKetThuc)->format('H:i:s d/m/Y') }}
            </div>
            <div class="mb-3">
                <strong>Thời gian làm bài:</strong> {{ $baiKiemTra->ThoiGianLamBai }} phút
            </div>
            <div class="mb-3">
                <strong>Cho phép xem kết quả:</strong>
                <span class="badge {{ $baiKiemTra->ChoPhepXemKetQua ? 'bg-success' : 'bg-danger' }}">
                    {{ $baiKiemTra->ChoPhepXemKetQua ? 'Có' : 'Không' }}
                </span>
            </div>
            <div class="mb-3">
                <strong>Mô tả:</strong> {{ $baiKiemTra->MoTa ?? 'Không có mô tả' }}
            </div>
            <div class="mb-3">
                <strong>Trạng thái:</strong>
                <span class="badge {{ $baiKiemTra->TrangThai ? 'bg-success' : 'bg-danger' }}">
                    {{ $baiKiemTra->TrangThai ? 'Hiện' : 'Ẩn' }}
                </span>
            </div>
            <div class="mb-3">
                <strong>Số lượng sinh viên đã làm:</strong> {{ $soLuongSinhVien }}
            </div>
            <h5>Danh sách câu hỏi</h5>
            @if ($cauHois->isEmpty())
            <p>Không có câu hỏi nào.</p>
            @else
            @foreach ($cauHois as $index => $cauHoi)
            <div class="question-group">
                <h6>Câu {{ $index + 1 }}: {{ $cauHoi->CauHoi }}</h6>
                <p><strong>Đáp án A:</strong> {{ $cauHoi->DapAnA }}</p>
                <p><strong>Đáp án B:</strong> {{ $cauHoi->DapAnB }}</p>
                <p><strong>Đáp án C:</strong> {{ $cauHoi->DapAnC }}</p>
                <p><strong>Đáp án D:</strong> {{ $cauHoi->DapAnD }}</p>
                <p><strong>Đáp án đúng:</strong> {{ $cauHoi->DapAnDung }}</p>
            </div>
            @endforeach
            @endif
            <a href="{{ route('giangvien.bai-kiem-tra.danh-sach') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</div>
@endsection