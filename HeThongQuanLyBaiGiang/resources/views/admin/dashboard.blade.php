@extends('layouts.adminLayout')

@section('styles')
<style>
.card {
    transition: transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.badge {
    font-size: 0.8em;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
}

@media (max-width: 768px) {
    .fs-3 {
        font-size: 1.5rem !important;
    }
    
    .card-title {
        font-size: 0.9rem;
    }
}

.no-data-message {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
    font-style: italic;
}
</style>
@endsection

@section('content')
<div class="container">
    <h2 class="mb-4">Thống kê hệ thống</h2>

    <!-- Kiểm tra dữ liệu -->
    @if($thongKeTongQuan['tongKhoa'] == 0 && $thongKeTongQuan['tongMonHoc'] == 0 && $thongKeTongQuan['tongGiangVien'] == 0 && $thongKeTongQuan['tongSinhVien'] == 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Chưa có dữ liệu thống kê. Vui lòng thêm dữ liệu vào hệ thống để xem thống kê.
        </div>
    @endif

    <!-- Tabs điều hướng -->
    <ul class="nav nav-tabs mb-4" id="thongKeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tongquan-tab" data-bs-toggle="tab" data-bs-target="#tongquan" type="button" role="tab">Tổng quan hệ thống</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="daotao-tab" data-bs-toggle="tab" data-bs-target="#daotao" type="button" role="tab">Hoạt động đào tạo</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sinhvien-tab" data-bs-toggle="tab" data-bs-target="#sinhvien" type="button" role="tab">Hoạt động sinh viên</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="hethong-tab" data-bs-toggle="tab" data-bs-target="#hethong" type="button" role="tab">Hoạt động hệ thống</button>
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
                            <h5 class="card-title">Môn học</h5>
                            <p class="card-text fs-3">{{ $thongKeTongQuan['tongMonHoc'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-warning mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Giảng viên</h5>
                            <p class="card-text fs-3">{{ $thongKeTongQuan['tongGiangVien'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-danger mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Sinh viên</h5>
                            <p class="card-text fs-3">{{ $thongKeTongQuan['tongSinhVien'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hoạt động đào tạo -->
        <div class="tab-pane fade" id="daotao" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card border-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số học phần</h5>
                            <p class="card-text fs-3">{{ $thongKeDaoTao['tongHocPhan'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số lớp học phần</h5>
                            <p class="card-text fs-3">{{ $thongKeDaoTao['tongLopHocPhan'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Top Khoa có nhiều môn học nhất</h5>
                    <ul class="list-group mb-3">
                        @forelse($thongKeDaoTao['topKhoaNhieuMonHoc'] as $khoa)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $khoa->TenKhoa }}
                                <span class="badge bg-primary rounded-pill">{{ $khoa->soMonHoc }} môn học</span>
                            </li>
                        @empty
                            <li class="list-group-item">Chưa có dữ liệu</li>
                        @endforelse
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Top Giảng viên có nhiều học phần nhất</h5>
                    <ul class="list-group">
                        @forelse($thongKeDaoTao['topGiangVienNhieuHocPhan'] as $giangVien)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $giangVien->HoTen }}
                                <span class="badge bg-success rounded-pill">{{ $giangVien->soHocPhan }} học phần</span>
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
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-info mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Tổng số bài giảng</h5>
                            <p class="card-text fs-3">{{ $thongKeHeThong['tongBaiGiang'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Tổng số bài kiểm tra</h5>
                            <p class="card-text fs-3">{{ $thongKeHeThong['tongBaiKiemTra'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-danger mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Tổng số thông báo</h5>
                            <p class="card-text fs-3">{{ $thongKeHeThong['tongThongBao'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ thống kê theo tháng -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Thống kê hoạt động theo tháng ({{ date('Y') }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="chartTheoThang"></canvas>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dữ liệu cho biểu đồ
    const data = {
        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
        datasets: [
            {
                label: 'Bài giảng',
                data: @json($thongKeHeThong['thongKeTheoThang']['baiGiangTheoThang']),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            },
            {
                label: 'Bài kiểm tra',
                data: @json($thongKeHeThong['thongKeTheoThang']['baiKiemTraTheoThang']),
                borderColor: 'rgb(255, 205, 86)',
                backgroundColor: 'rgba(255, 205, 86, 0.2)',
                tension: 0.1
            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Thống kê hoạt động theo tháng'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    new Chart(document.getElementById('chartTheoThang'), config);
});
</script>
@endsection

