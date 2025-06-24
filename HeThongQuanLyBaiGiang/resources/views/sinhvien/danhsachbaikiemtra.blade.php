@extends('layouts.studentLayout')

@section('title', 'Danh Sách Bài Kiểm Tra')

@section('style')
<link rel="stylesheet" href="{{ asset('css/student/baikiemtra.css') }}">
@endsection

@section('sidebar')
@include('layouts.sidebarBaiKiemTra')
@endsection

@section('content')
<div class="container">
    <h2 class="text-center mb-4" style="color: #2c3e50;">Danh Sách Bài Kiểm Tra</h2>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($baiKiemTra->count() > 0)
    <div class="row">
        @foreach($baiKiemTra as $bai)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">{{ $bai->TenBaiKiemTra }}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><strong>Giảng viên:</strong> {{ $bai->giangVien->HoTen }}</p>
                    <p class="card-text"><strong>Lớp học phần:</strong> {{ $bai->lopHocPhan->TenLopHocPhan }}</p>
                    <p class="card-text"><strong>Thời gian bắt đầu:</strong> {{
                        \Carbon\Carbon::parse($bai->ThoiGianBatDau)->format('H:i d/m/Y') }}</p>
                    <p class="card-text"><strong>Thời gian kết thúc:</strong> {{
                        \Carbon\Carbon::parse($bai->ThoiGianKetThuc)->format('H:i d/m/Y') }}</p>

                    @if($bai->MoTa)
                    <p class="card-text"><strong>Mô tả:</strong> {{ Str::limit($bai->MoTa, 100) }}</p>
                    @endif

                    @php
                    $now = \Carbon\Carbon::now();
                    $thoiGianBatDau = \Carbon\Carbon::parse($bai->ThoiGianBatDau);
                    $thoiGianKetThuc = \Carbon\Carbon::parse($bai->ThoiGianKetThuc);
                    @endphp

                    <div class="mt-3">
                        @if($bai->daLam)
                        <span class="badge bg-success mb-2">Đã làm bài</span>
                        <br>
                        <a href="{{ route('ket-qua-bai-kiem-tra', $bai->MaBaiKiemTra) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Xem kết quả
                        </a>
                        @else
                        @if($now < $thoiGianBatDau) <span class="badge bg-warning mb-2">Chưa bắt đầu</span>
                            <br>
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="bi bi-clock"></i> Chưa đến giờ
                            </button>
                            @elseif($now > $thoiGianKetThuc)
                            <span class="badge bg-danger mb-2">Đã kết thúc</span>
                            <br>
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="bi bi-x-circle"></i> Hết thời gian
                            </button>
                            @else
                            <span class="badge bg-primary mb-2">Đang diễn ra</span>
                            <br>
                            <a href="{{ route('lam-bai-kiem-tra', $bai->MaBaiKiemTra) }}"
                                class="btn btn-success btn-sm">
                                <i class="bi bi-pencil"></i> Làm bài
                            </a>
                            @endif
                            @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Bạn chưa có bài kiểm tra nào trong các lớp học phần đã tham gia.
        </div>
    </div>
    @endif
</div>
@endsection