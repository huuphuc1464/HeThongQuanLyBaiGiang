@extends('layouts.lopHocPhanLayout')

@section('title','Danh sách sự kiện zoom')

@section('tab-content')
@if ($suKiens->isEmpty())
<div class="text-center text-muted py-4">
    Hiện tại không có sự kiện học trực tuyến qua Zoom nào.
</div>
@else
<div class="container-fluid my-4">
    <h4 class="fw-semibold mb-3">Danh sách sự kiện Zoom</h4>

    @foreach ($suKiens as $suKien)
    @php
    $now = now('Asia/Ho_Chi_Minh');
    $batDau = \Carbon\Carbon::parse($suKien->ThoiGianBatDau, 'Asia/Ho_Chi_Minh');
    $ketThuc = \Carbon\Carbon::parse($suKien->ThoiGianKetThuc, 'Asia/Ho_Chi_Minh');
    $dangDienRa = $now->between($batDau, $ketThuc);
    $daKetThuc = $now->greaterThan($ketThuc);
    @endphp
    <div class="d-flex align-items-center border-top py-2 position-relative {{ $daKetThuc ? 'text-muted' : '' }}">
        <a href="{{ route('su-kien-zoom.chi-tiet', ['id' => $id, 'maSuKien' => $suKien->MaSuKienZoom]) }}" class="stretched-link"></a>
        <div class="me-3">
            <div class="d-flex justify-content-center align-items-center bg-opacity-25 rounded-circle {{ $daKetThuc ? 'bg-secondary' : 'bg-info' }}" style="width: 40px; height: 40px;">
                <i class="fas fa-video fs-5 {{ $daKetThuc ? 'text-secondary' : 'text-info' }}"></i>
            </div>
        </div>
        <div class="flex-grow-1 fw-semibold fs-6">
            {{ $suKien->TenSuKien }}
        </div>
        <div class="d-flex align-items-center text-end" style="z-index: 1;">
            <div class="text-muted small me-3">
                Thời gian bắt đầu: {{ \Carbon\Carbon::parse($suKien->ThoiGianBatDau)->format('H:i:s d/m/Y') }}
            </div>
            @if ($dangDienRa)
            <a href="{{ $suKien->LinkSuKien }}" target="_blank" class="btn btn-primary btn-sm rounded-pill text-center d-inline-block" style="width: 150px;">
                Tham gia
            </a>
            @elseif ($daKetThuc)
            <button class="btn btn-secondary btn-sm rounded-pill text-center d-inline-block" style="width: 150px;" disabled>
                Đã kết thúc
            </button>
            @else
            <button class="btn btn-secondary btn-sm rounded-pill text-center d-inline-block" style="width: 150px;" disabled>
                Chưa bắt đầu
            </button>
            @endif
        </div>
    </div>


    @endforeach
</div>
@endif
@endsection
