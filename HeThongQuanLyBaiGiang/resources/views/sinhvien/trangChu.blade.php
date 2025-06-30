@extends('layouts.studentLayout')

@section('title', 'Trang chủ')

@section('sidebar')
@include('layouts.sidebarTrangChu', ['danhSachLopHocPhanSidebar' => $danhSachLopHocPhanSidebar])
@endsection

@section('content')
<div class="container-fluid px-3 pt-3">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        @foreach($danhSachBaiGiang as $baiGiang)
        <div class="col">
            <a href="{{ route('bai-giang.bai.tab', ['id' => $baiGiang->MaLopHocPhan]) }}"
                class="text-decoration-none text-dark">
                <div class="card card-class position-relative">
                    <img class="card-img-top"
                        src="{{ $baiGiang->AnhBaiGiang ? asset($baiGiang->AnhBaiGiang) : asset('img/hocphan/default.png') }}"
                        alt="{{ $baiGiang->TenLopHocPhan }}">
                    <div class="card-body pb-0">
                        <div class="fw-bold mb-1 fs-5">{{ $baiGiang->TenLopHocPhan }}</div>
                        <div class="mb-1 text-muted" style="font-size: 14px;">{{ $baiGiang->TenBaiGiang }}</div>
                        <p class="card-text"></p>{{ $baiGiang->MoTa }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="instructor"><img class="anh-giang-vien"
                                src="{{ asset($baiGiang->AnhGiangVien ?? '/AnhDaiDien/default-avatar.png') }}"
                                alt="{{ $baiGiang->TenGiangVien }}"><span class="ms-1">{{ $baiGiang->TenGiangVien
                                }}</span></div>
                        <div class="students"><i class="fas fa-users"></i><span>{{ $baiGiang->SoLuongSinhVien }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection