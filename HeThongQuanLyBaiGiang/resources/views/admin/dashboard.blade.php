@extends('layouts.adminLayout')

@section('content')
<div class="container">
    <h2 class="mb-4">Thống kê hệ thống</h2>

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
    </ul>

    <div class="tab-content" id="thongKeTabContent">
        <!-- Tổng quan hệ thống -->
        <div class="tab-pane fade show active" id="tongquan" role="tabpanel">
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-bg-primary mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Khoa</h5>
                            <p class="card-text fs-3">10</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-success mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Môn học</h5>
                            <p class="card-text fs-3">50</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-warning mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Giảng viên</h5>
                            <p class="card-text fs-3">20</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-danger mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Sinh viên</h5>
                            <p class="card-text fs-3">500</p>
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
                            <p class="card-text fs-3">80</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số lớp học phần</h5>
                            <p class="card-text fs-3">120</p>
                        </div>
                    </div>
                </div>
            </div>

            <h5>Top Khoa có nhiều môn học nhất</h5>
            <ul class="list-group mb-3">
                <li class="list-group-item">Khoa CNTT - 20 môn học</li>
                <li class="list-group-item">Khoa Kinh tế - 15 môn học</li>
                <li class="list-group-item">Khoa Ngoại ngữ - 10 môn học</li>
            </ul>

            <h5>Top Giảng viên có nhiều học phần nhất</h5>
            <ul class="list-group">
                <li class="list-group-item">Nguyễn Văn A - 12 học phần</li>
                <li class="list-group-item">Trần Thị B - 10 học phần</li>
                <li class="list-group-item">Lê Văn C - 9 học phần</li>
            </ul>
        </div>

        <!-- Hoạt động sinh viên -->
        <div class="tab-pane fade" id="sinhvien" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card border-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số sinh viên đã tham gia lớp học phần</h5>
                            <p class="card-text fs-3">450</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Trung bình sinh viên/lớp học phần</h5>
                            <p class="card-text fs-3">35</p>
                        </div>
                    </div>
                </div>
            </div>
            <h5>Lớp học phần có nhiều sinh viên nhất</h5>
            <ul class="list-group">
                <li class="list-group-item">Lớp CNTT101 - 50 sinh viên</li>
            </ul>
        </div>
    </div>
</div>
@endsection
