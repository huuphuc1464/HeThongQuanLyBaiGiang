@extends('layouts.lopHocPhanLayout')

@section('title','Danh sách sự kiện zoom')

@section('tab-content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-3">
        <div class="me-3">
            <div class="d-flex justify-content-center align-items-center bg-info bg-opacity-25 rounded-circle" style="width: 50px; height: 50px;">
                <i class="fas fa-video text-info fs-4"></i>
            </div>
        </div>
        <div>
            <h4 class="mb-1">{{ $suKien->TenSuKien }}</h4>
            @php
            $created = \Carbon\Carbon::parse($suKien->created_at);
            $updated = \Carbon\Carbon::parse($suKien->updated_at);
            @endphp
            <small class="text-muted">
                {{ $tenGiangVien ?? 'Tên giảng viên' }}
                <span class="mx-1">&bull;</span>
                {{ $created->format('H:i:s d/m/Y') }}
                @if (!$created->equalTo($updated))
                <span class="text-secondary"> (Cập nhật: {{ $updated->format('H:i:s d/m/Y') }})</span>
                @endif
            </small>
        </div>
    </div>

    <hr>

    <ul class="list-unstyled fs-6">
        <li class="mb-2">
            <span class="me-2 text-primary"><i class="fas fa-clock"></i></span>
            <strong>Thời gian bắt đầu:</strong> {{ \Carbon\Carbon::parse($suKien->ThoiGianBatDau)->format('H:i:s d/m/Y') }}
        </li>
        <li class="mb-2">
            <span class="me-2 text-danger"><i class="fas fa-clock"></i></span>
            <strong>Thời gian kết thúc:</strong> {{ \Carbon\Carbon::parse($suKien->ThoiGianKetThuc)->format('H:i:s d/m/Y') }}
        </li>
        <li class="mb-2">
            <span class="me-2 text-success"><i class="fas fa-file-alt"></i></span>
            <strong>Mô tả sự kiện:</strong> {{ $suKien->MoTa }}
        </li>
        <li class="mb-2">
            <span class="me-2 text-warning"><i class="fas fa-link"></i></span>
            <strong>Link sự kiện:</strong> <a href="{{ $suKien->LinkSuKien }}" target="_blank">{{ $suKien->LinkSuKien }}</a>
        </li>
        <li class="mb-4">
            <span class="me-2 text-dark"><i class="fas fa-lock"></i></span>
            <strong>Mật khẩu sự kiện:</strong> {{ $suKien->MatKhauSuKien }}
        </li>
    </ul>

    @php
    $now = now('Asia/Ho_Chi_Minh');
    $batDau = \Carbon\Carbon::parse($suKien->ThoiGianBatDau)->timezone('Asia/Ho_Chi_Minh');
    $ketThuc = \Carbon\Carbon::parse($suKien->ThoiGianKetThuc)->timezone('Asia/Ho_Chi_Minh');
    $dangDienRa = $now->between($batDau, $ketThuc);
    $daKetThuc = $now->gt($ketThuc);
    @endphp

    @if ($dangDienRa)
    <a href="{{ $suKien->LinkSuKien }}" target="_blank" class="btn btn-primary rounded-pill px-4">Tham gia</a>
    @elseif ($daKetThuc)
    <button class="btn btn-secondary rounded-pill px-4" disabled>Đã kết thúc</button>
    @else
    <button class="btn btn-secondary rounded-pill px-4" disabled>Chưa bắt đầu</button>
    @endif
</div>


@endsection
