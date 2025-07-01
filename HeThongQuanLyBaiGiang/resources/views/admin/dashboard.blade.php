@extends('layouts.adminLayout')

@section('styles')
<link rel="stylesheet" href="{{asset('css/admin/dashbroad.css')}}" />
@endsection

@section('content')
<div class="container">
    <h2 class="mb-4">Thống kê hệ thống</h2>

    <!-- Kiểm tra dữ liệu -->
    @if($thongKeTongQuan['tongKhoa'] == 0 && $thongKeTongQuan['tongBaiGiang'] == 0 && $thongKeTongQuan['tongChuong'] ==
    0 && $thongKeTongQuan['tongBai'] == 0 && $thongKeTongQuan['tongGiangVien'] == 0 && $thongKeTongQuan['tongSinhVien']
    == 0)
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Chưa có dữ liệu thống kê. Vui lòng thêm dữ liệu vào hệ thống để xem thống kê.
    </div>
    @endif

    <!-- Tabs điều hướng -->
    <ul class="nav nav-tabs mb-4" id="thongKeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tongquan-tab" data-bs-toggle="tab" data-bs-target="#tongquan"
                type="button" role="tab">Tổng quan hệ thống</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="daotao-tab" data-bs-toggle="tab" data-bs-target="#daotao" type="button"
                role="tab">Hoạt động đào tạo</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sinhvien-tab" data-bs-toggle="tab" data-bs-target="#sinhvien" type="button"
                role="tab">Hoạt động sinh viên</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="hethong-tab" data-bs-toggle="tab" data-bs-target="#hethong" type="button"
                role="tab">Hoạt động hệ thống</button>
        </li>
    </ul>

    <div class="tab-content" id="thongKeTabContent">
        <!-- Tổng quan hệ thống -->
        <div class="tab-pane fade show active" id="tongquan" role="tabpanel">
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-bg-primary mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Khoa</h5>
                            <p class="card-text fs-3">{{ $thongKeTongQuan['tongKhoa'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-success mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Bài giảng</h5>
                            <p class="card-text fs-3">{{ $thongKeTongQuan['tongBaiGiang'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-info mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Chương</h5>
                            <p class="card-text fs-3">{{ $thongKeTongQuan['tongChuong'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-secondary mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Bài</h5>
                            <p class="card-text fs-3">{{ $thongKeTongQuan['tongBai'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hoạt động đào tạo -->
        <div class="tab-pane fade" id="daotao" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card border-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số bài giảng</h5>
                            <p class="card-text fs-3">{{ $thongKeDaoTao['tongBaiGiang'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số chương</h5>
                            <p class="card-text fs-3">{{ $thongKeDaoTao['tongChuong'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-secondary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số bài</h5>
                            <p class="card-text fs-3">{{ $thongKeDaoTao['tongBai'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Top Khoa có nhiều bài giảng nhất</h5>
                    <ul class="list-group mb-3">
                        @forelse($thongKeDaoTao['topKhoaNhieuBaiGiang'] as $khoa)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $khoa->TenKhoa }}
                            <span class="badge bg-primary rounded-pill">{{ $khoa->soBaiGiang }} bài giảng</span>
                        </li>
                        @empty
                        <li class="list-group-item">Chưa có dữ liệu</li>
                        @endforelse
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Top Giảng viên có nhiều bài giảng nhất</h5>
                    <ul class="list-group">
                        @forelse($thongKeDaoTao['topGiangVienNhieuBaiGiang'] as $giangVien)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $giangVien->HoTen }}
                            <span class="badge bg-success rounded-pill">{{ $giangVien->soBaiGiang }} bài giảng</span>
                        </li>
                        @empty
                        <li class="list-group-item">Chưa có dữ liệu</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Hoạt động sinh viên -->
        <div class="tab-pane fade" id="sinhvien" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card border-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số sinh viên đã tham gia lớp học phần</h5>
                            <p class="card-text fs-3">{{ $thongKeSinhVien['tongSinhVienThamGia'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Trung bình sinh viên/lớp học phần</h5>
                            <p class="card-text fs-3">{{ $thongKeSinhVien['trungBinhSinhVienLop'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <h5>Lớp học phần có nhiều sinh viên nhất</h5>
            <ul class="list-group">
                @forelse($thongKeSinhVien['lopNhieuSinhVienNhat'] as $lop)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $lop->TenLopHocPhan }}
                    <span class="badge bg-info rounded-pill">{{ $lop->soSinhVien }} sinh viên</span>
                </li>
                @empty
                <li class="list-group-item">Chưa có dữ liệu</li>
                @endforelse
            </ul>
        </div>

        <!-- Hoạt động hệ thống -->
        <div class="tab-pane fade" id="hethong" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header text-center">
                            <h5 class="card-title mb-0">Thống kê hoạt động hệ thống</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 text-end">
                                <label for="selectYear" class="form-label me-2">Chọn năm:</label>
                                <select id="selectYear" class="form-select d-inline-block w-auto"></select>
                            </div>
                            <div class="chart-container">
                                <canvas id="heThongBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/admin/dashbroad.js') }}"></script>
@endsection