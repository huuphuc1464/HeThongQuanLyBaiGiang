@extends('layouts.studentLayout')

@section('title', 'Lớp học phần lưu trữ')

@section('sidebar')
@include('layouts.sidebarTrangChu', ['danhSachLopHocPhanSidebar' => $danhSachLopHocPhanSidebar ?? null])
@endsection

@section('content')
<div class="container-fluid px-3 pt-3">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
        @if($danhSachLopLuuTru->isEmpty())
        <div class="col-12 text-center text-muted py-5">
            <h5>Chưa có lớp học phần nào được lưu trữ.</h5>
        </div>
        @else
        @foreach($danhSachLopLuuTru as $baiGiang)
        <div class="col">
            <a href="{{ route('bai-giang.bai.tab', ['id' => $baiGiang->MaLopHocPhan]) }}"
                class="text-decoration-none text-dark">
                <div class="card card-class position-relative">
                    <img class="card-img-top"
                        src="{{ $baiGiang->AnhBaiGiang ? asset($baiGiang->AnhBaiGiang) : asset('img/hocphan/default.png') }}"
                        alt="{{ $baiGiang->TenLopHocPhan }}">
                    <div class="card-body pb-0">
                        <div class="mb-2" style="min-height: 150px;">
                            <div class="fw-bold fs-5" style="white-space: normal; word-wrap: break-word;">
                                {{ $baiGiang->TenLopHocPhan }}
                            </div>
                            <div class="text-muted"
                                style="font-size: 14px; white-space: normal; word-wrap: break-word;">
                                {{ $baiGiang->TenBaiGiang }}
                            </div>
                        </div>
                        <p class="card-text">{{ $baiGiang->MoTa }}</p>
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
        @endif
    </div>
</div>
@endsection