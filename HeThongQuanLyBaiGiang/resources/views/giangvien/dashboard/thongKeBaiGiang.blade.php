@extends('layouts.teacherLayout')
@section('title','Giảng viên - Thống kê bài giảng')
@section('tenTrang', 'Dashboard / Thống kê bài giảng')
@section('content')
<div class="row">
    {{-- Left --}}
    <div class="col-md-12 col-lg-6">
        <div class="row">
            <form action="{{ route('giangvien.dashboard') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-12">
                        <label for="MaBaiGiang" class="form-label"><b>Chọn bài giảng cần thống kê:</b></label>

                        <select name="MaBaiGiang" id="MaBaiGiang" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Tất cả bài giảng --</option>
                            @foreach ($danhSachBaiGiang as $bg)
                            <option value="{{ $bg->MaBaiGiang }}" {{ request('MaBaiGiang') == $bg->MaBaiGiang ? 'selected' : '' }}>
                                {{ $bg->TenBaiGiang }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            <div class="col-md-6">
                <div class="thongKe tk1 khungIcon d-flex">
                    <i class="fa-solid fa-book-open icon"></i>
                    <div class="info">
                        <h4>Bài giảng</h4>
                        <p><b>{{ $tongBaiGiang }} bài giảng</b></p>
                        <p class="info-tong">Tổng số bài giảng bạn đã được tạo.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thongKe tk2 khungIcon">
                    <i class="fa-solid fa-folder icon"></i>
                    <div class="info">
                        <h4>Chương</h4>
                        <p><b>{{ $tongChuong }} chương</b></p>
                        <p class="info-tong">Tổng số chương bạn đã tạo trong bài giảng.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thongKe tk3 khungIcon">
                    <i class="fa-solid fa-note-sticky icon"></i>
                    <div class="info">
                        <h4>Bài học</h4>
                        <p><b>{{ $tongBai }} bài học</b></p>
                        <p class="info-tong">Tổng số bài bạn đã tạo trong bài giảng.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="thongKe tk4 khungIcon">
                    <i class="fa-solid fa-chalkboard-teacher icon"></i>
                    <div class="info">
                        <h4>Lớp học phần</h4>
                        <p><b>{{ $tongLopHocPhan }} lớp học phần</b></p>
                        <p class="info-tong">Tổng số lớp học phần đã tạo trong bài giảng.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thongKe tk1 khungIcon">
                    <i class="fa-solid fa-user-graduate icon"></i>
                    <div class="info">
                        <h4>Sinh viên</h4>
                        <p><b>{{ $tongSinhVien }} sinh viên</b></p>
                        <p class="info-tong">Tổng số lượng sinh viên đã tham gia lớp học phần.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thongKe tk2 khungIcon">
                    <i class="fa-solid fa-video icon"></i>
                    <div class="info">
                        <h4>Sự kiện Zoom</h4>
                        <p><b>{{ $tongSuKienZoom }} sự kiện Zoom</b></p>
                        <p class="info-tong">Tổng số lượng sự kiện Zoom đã tạo trong các lớp học phần.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thongKe tk3 khungIcon">
                    <i class="fas fa-clipboard-list icon"></i>
                    <div class="info">
                        <h4>Bài kiểm tra</h4>
                        <p><b>{{ $tongBaiKiemTra }} bài kiểm tra</b></p>
                        <p class="info-tong">Tổng số lượng bài kiểm tra đã tạo trong lớp học phần.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thongKe tk4 khungIcon">
                    <i class="fa-solid fa-cloud icon"></i>
                    <div class="info">
                        <h4>Dung lượng lưu trữ</h4>
                        <p><b>{{ $tongDungLuong }} MB</b></p>
                        <p class="info-tong">Tổng dung lượng lưu trữ file bài giảng.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--End left--}}

    {{-- Right --}}
    <div class="col-md-12 col-lg-6">
        <div class="row">
            <div class="form-group col-md-8 pb-3">
                <label for="selectNamThongKe" class="form-label"><b>Năm</b></label>
                <select class="form-control" id="selectNamThongKe">
                    <option value="">-- Chọn năm thống kê --</option>
                    @foreach ($namThongKe as $nam)
                    <option value="{{ $nam }}">{{ $nam }}</option>
                    @endforeach
                </select>
            </div>
            <div class="tile">
                <h3 class="tile-title text-dark text-center mt-0" id="namThongKe">THỐNG KÊ THỜI GIAN TẠO BÀI GIẢNG TRONG NĂM {{ $namThongKe->first() }}
                </h3>
                <div class="embed-responsive embed-responsive-16by9">
                    <canvas class="embed-responsive-item" id="lineChartDemo"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{-- End right --}}
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('./css/teacher/thongKe.css') }}">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const baiTheoThang = @json($baiTheoThang);
    const quizTheoThang = @json($quizTheoThang);
    const zoomTheoThang = @json($zoomTheoThang);
    let lineChart;

    // Vẽ biểu đồ
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById("lineChartDemo").getContext("2d");
        const quizData = Array(12).fill(0);
        const zoomData = Array(12).fill(0);
        const baiData = Array(12).fill(0);

        for (const [thang, soLuong] of Object.entries(baiTheoThang)) {
            baiData[parseInt(thang) - 1] = soLuong;
        }

        for (const [thang, soLuong] of Object.entries(quizTheoThang)) {
            quizData[parseInt(thang) - 1] = soLuong;
        }

        for (const [thang, soLuong] of Object.entries(zoomTheoThang)) {
            zoomData[parseInt(thang) - 1] = soLuong;
        }

        lineChart = new Chart(ctx, {
            type: 'line'
            , data: {
                labels: [
                    "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6"
                    , "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
                ]
                , datasets: [{
                        label: "Bài học"
                        , data: baiData
                        , borderColor: "rgba(255, 159, 64, 1)"
                        , backgroundColor: "rgba(255, 159, 64, 0.2)"
                        , pointBackgroundColor: "rgba(255, 159, 64, 1)"
                        , pointBorderColor: "#fff"
                        , pointHoverBackgroundColor: "#fff"
                        , pointHoverBorderColor: "rgba(255, 159, 64, 1)"
                        , fill: true
                        , tension: 0.4
                        , borderWidth: 2
                    }, {
                        label: "Bài kiểm tra"
                        , data: quizData
                        , borderColor: "rgba(54, 162, 235, 1)"
                        , backgroundColor: "rgba(54, 162, 235, 0.2)"
                        , pointBackgroundColor: "rgba(54, 162, 235, 1)"
                        , pointBorderColor: "#fff"
                        , pointHoverBackgroundColor: "#fff"
                        , pointHoverBorderColor: "rgba(54, 162, 235, 1)"
                        , fill: true
                        , tension: 0.4
                        , borderWidth: 2
                    }

                    , {
                        label: "Sự kiện Zoom"
                        , data: zoomData
                        , borderColor: "rgba(75, 192, 192, 1)"
                        , backgroundColor: "rgba(75, 192, 192, 0.2)"
                        , pointBackgroundColor: "rgba(75, 192, 192, 1)"
                        , pointBorderColor: "#fff"
                        , pointHoverBackgroundColor: "#fff"
                        , pointHoverBorderColor: "rgba(75, 192, 192, 1)"
                        , fill: true
                        , tension: 0.4
                        , borderWidth: 2
                    }
                ]
            }
            , options: {
                responsive: true
                , plugins: {
                    legend: {
                        display: true
                        , position: 'top'
                        , labels: {
                            color: '#333'
                            , font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    });

    // Vẽ lại biểu đồ khi chọn năm khác
    document.getElementById('selectNamThongKe').addEventListener('change', function() {
        const nam = document.getElementById('selectNamThongKe').value;
        const maBaiGiang = document.getElementById('MaBaiGiang').value;

        document.getElementById('namThongKe').textContent =
            `THỐNG KÊ THỜI GIAN TẠO BÀI GIẢNG TRONG NĂM ${nam}`;

        fetch(`/giang-vien/bieu-do-thong-ke?nam=${nam}&maBaiGiang=${maBaiGiang}`)
            .then(res => res.json())
            .then(data => {
                const convert = source => {
                    const result = Array(12).fill(0);
                    for (const [thang, value] of Object.entries(source || {})) {
                        result[parseInt(thang) - 1] = value;
                    }
                    return result;
                };

                lineChart.data.datasets[0].data = convert(data.baiGiang);
                lineChart.data.datasets[1].data = convert(data.baiKiemTra);
                lineChart.data.datasets[2].data = convert(data.suKienZoom);

                lineChart.update();
            })
            .catch(error => console.error('Lỗi khi fetch biểu đồ:', error));
    });

</script>
@endsection
