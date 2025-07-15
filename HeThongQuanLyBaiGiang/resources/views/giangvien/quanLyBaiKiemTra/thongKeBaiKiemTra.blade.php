@extends('layouts.teacherLayout')

@section('title', 'Thống kê bài kiểm tra')
@section('tenTrang', 'Thống kê bài kiểm tra')
@section('styles')
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <h3 class="mb-0 fw-bold">Thống kê bài kiểm tra: <span class="text-primary">{{ $baiKiemTra->TenBaiKiemTra
                }}</span></h3>
        <span class="ms-3 text-success fw-semibold" style="font-size:1.1rem;">Lớp: {{
            $baiKiemTra->lopHocPhan->TenLopHocPhan }}</span>
    </div>

    <ul class="nav nav-tabs mb-3" id="thongKeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tong-quan-tab" data-bs-toggle="tab" data-bs-target="#tong-quan"
                type="button" role="tab" aria-controls="tong-quan" aria-selected="true">Tổng quan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sv-lam-tab" data-bs-toggle="tab" data-bs-target="#sv-lam" type="button"
                role="tab" aria-controls="sv-lam" aria-selected="false">SV đã làm</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sv-chua-lam-tab" data-bs-toggle="tab" data-bs-target="#sv-chua-lam"
                type="button" role="tab" aria-controls="sv-chua-lam" aria-selected="false">SV chưa làm</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cau-hoi-tab" data-bs-toggle="tab" data-bs-target="#cau-hoi" type="button"
                role="tab" aria-controls="cau-hoi" aria-selected="false">Tỷ lệ đúng/sai câu hỏi</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="phan-bo-diem-tab" data-bs-toggle="tab" data-bs-target="#phan-bo-diem"
                type="button" role="tab" aria-controls="phan-bo-diem" aria-selected="false">Phân bố điểm</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="thoi-gian-nop-tab" data-bs-toggle="tab" data-bs-target="#thoi-gian-nop"
                type="button" role="tab" aria-controls="thoi-gian-nop" aria-selected="false">Thời gian nộp</button>
        </li>
    </ul>
    <div class="tab-content" id="thongKeTabContent">
        <div class="tab-pane fade show active" id="tong-quan" role="tabpanel" aria-labelledby="tong-quan-tab">
            @include('giangvien.quanLyBaiKiemTra.thongKeTabs.tongQuan')
        </div>
        <div class="tab-pane fade" id="sv-lam" role="tabpanel" aria-labelledby="sv-lam-tab">
            @include('giangvien.quanLyBaiKiemTra.thongKeTabs.svLam')
        </div>
        <div class="tab-pane fade" id="sv-chua-lam" role="tabpanel" aria-labelledby="sv-chua-lam-tab">
            @include('giangvien.quanLyBaiKiemTra.thongKeTabs.svChuaLam')
        </div>
        <div class="tab-pane fade" id="cau-hoi" role="tabpanel" aria-labelledby="cau-hoi-tab">
            @include('giangvien.quanLyBaiKiemTra.thongKeTabs.cauHoi')
        </div>
        <div class="tab-pane fade" id="phan-bo-diem" role="tabpanel" aria-labelledby="phan-bo-diem-tab">
            @include('giangvien.quanLyBaiKiemTra.thongKeTabs.phanBoDiem')
        </div>
        <div class="tab-pane fade" id="thoi-gian-nop" role="tabpanel" aria-labelledby="thoi-gian-nop-tab">
            @include('giangvien.quanLyBaiKiemTra.thongKeTabs.thoiGianNop')
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection