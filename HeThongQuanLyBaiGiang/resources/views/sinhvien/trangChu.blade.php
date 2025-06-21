@extends('layouts.studentLayout')

@section('title', 'Trang chủ')

@section('sidebar')
@include('layouts.sidebarTrangChu', ['danhSachLopHocPhanSidebar' => $danhSachLopHocPhanSidebar])
@endsection

@section('content')
<div class="container-fluid px-3 pt-3">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="classCardContainer">
        @foreach($danhSachBaiGiang as $baiGiang)
        <div class="col">
            <a href="{{ route('hoc-phan.bai-giang.tab', ['id' => $baiGiang->MaLopHocPhan]) }}" class="text-decoration-none text-dark">
                <div class="card card-class position-relative">
                    <img class="card-img-top" src="{{ $baiGiang->AnhHocPhan }}" alt="Class image">
                    <div class="card-body">
                        <h5 class="card-title mb-1">{{ $baiGiang->TenLopHocPhan }}</h5>
                        <h6 class="card-subtitle mb-1 text-muted">{{ $baiGiang->TenHocPhan }}</h6>
                        <p class="card-text mb-2"></p>{{ $baiGiang->MoTa }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="instructor"><img class="anh-giang-vien" src="{{ $baiGiang->AnhGiangVien }}"><span class="ms-1">{{ $baiGiang->TenGiangVien }}</span></div>
                        <div class="students"><i class="fas fa-users"></i><span>{{ $baiGiang->SoLuongSinhVien }}</span></div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
