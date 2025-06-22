@extends('layouts.studentLayout')

@section('title', 'Trang chủ')

@section('sidebar')
@include('layouts.sidebarTrangChu', ['danhSachLopHocPhanSidebar' => $danhSachLopHocPhanSidebar])
@endsection

@section('content')
<div class="container py-5">
    <div class="card shadow border-0 mx-auto" style="max-width: 600px;">
        <div class="card-body text-center">
            <div class="mb-4">
                @if ($success)
                <i class="fas fa-check-circle text-success" style="font-size: 60px;"></i>
                <h2 class="text-success fw-bold">Xác nhận thành công</h2>
                @else
                <i class="fas fa-times-circle text-danger" style="font-size: 60px;"></i>
                <h2 class="text-danger fw-bold">Xác nhận thất bại</h2>
                @endif
            </div>
            <p class="fs-5">{{ $message }}</p>
            <a href="{{ route('trang-chu') }}" class="btn btn-primary mt-4">Về trang chủ</a>
        </div>
    </div>
</div>
@endsection
