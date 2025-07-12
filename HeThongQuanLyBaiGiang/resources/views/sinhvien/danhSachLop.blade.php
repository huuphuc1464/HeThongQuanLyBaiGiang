@extends('layouts.lopHocPhanLayout')

@section('title','Danh sách lớp học phần')

@section('tab-content')
<div class="container-fluid my-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Giảng viên -->
            <h5 class="fw-bold fs-4">Giảng viên</h5>
            <hr class="my-2">
            <div class="d-flex align-items-center py-2">
                <img src="{{ asset($giangVien->AnhDaiDien ?? '/AnhDaiDien/default-avatar.png') }}" class="me-3 rounded-circle border" width="40" height="40" alt="{{ $giangVien->HoTen }}">
                <div class="fw-semibold highlight-target">{{ $giangVien->HoTen ?? 'Tên giảng viên' }}</div>
            </div>

            <!-- Sinh viên -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <h5 class="fw-bold fs-4 mb-0">Sinh viên</h5>
                <small class="fw-semibold text-muted">{{ count($sinhViens) }} sinh viên</small>
            </div>
            <hr class="my-2">
            @foreach ($sinhViens as $sv)
            <div class="d-flex align-items-center py-2 border-bottom">
                <img src="{{ asset($sv->AnhDaiDien ?? '/AnhDaiDien/default-avatar.png') }}" class="me-3 rounded-circle border" width="36" height="36" alt="{{ $sv->HoTen }}">
                <div class="fw-semibold highlight-target">{{ $sv->HoTen }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
