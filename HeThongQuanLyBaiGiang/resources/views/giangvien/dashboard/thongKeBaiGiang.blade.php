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
                <div class="thongKe baiGiang khungIcon d-flex">
                    <i class="fa-solid fa-chalkboard-user icon"></i>
                    <div class="info">
                        <h4>Tổng bài giảng</h4>
                        <p><b>{{ $tongBaiGiang }} bài giảng</b></p>
                        <p class="info-tong">Tổng số bài giảng bạn đã được tạo.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thongKe chuong khungIcon">
                    <i class="fa-solid fa-book-open icon"></i>
                    <div class="info">
                        <h4>Tổng số chương</h4>
                        <p><b>{{ $tongChuong }} chương</b></p>
                        <p class="info-tong">Tổng số chương bạn đã tạo trong bài giảng.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thongKe bai khungIcon">
                    <i class="fa-solid fa-note-sticky icon"></i>
                    <div class="info">
                        <h4>Tổng số bài</h4>
                        <p><b>{{ $tongBai }} bài</b></p>
                        <p class="info-tong">Tổng số bài bạn đã tạo trong bài giảng.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="thongKe sinhVien khungIcon">
                    <i class="fa-solid fa-user-graduate icon"></i>
                    <div class="info">
                        <h4>Sinh viên</h4>
                        <p><b>{{ $tongSinhVien }} sinh viên</b></p>
                        <p class="info-tong">Tổng số lượng sinh viên đã tham gia.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="thongKe file khungIcon">
                    <i class="fa-solid fa-file-lines icon"></i>
                    <div class="info">
                        <h4>Tổng số file</h4>
                        <p><b>{{ $tongFile }} file</b></p>
                        <p class="info-tong">Tổng số file bài giảng bạn đã lưu trữ.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="thongKe muc khungIcon">
                    <i class="fa-solid fa-cloud icon"></i>
                    <div class="info">
                        <h4>Tổng dung lượng lưu trữ</h4>
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
                <h3 class="tile-title text-dark text-center mt-0">THỐNG KÊ THỜI GIAN TẠO BÀI GIẢNG TRONG NĂM
                    2025</h3>
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
    const thongKeTheoThang = @json($thongKeTheoThang);
    const baiTheoThang = @json($baiTheoThang);

    let lineChart;

    // Vẽ biểu đồ
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById("lineChartDemo").getContext("2d");
        const thongKeData = Array(12).fill(0);
        const baiData = Array(12).fill(0);

        for (const [thang, soLuong] of Object.entries(thongKeTheoThang)) {
            thongKeData[parseInt(thang) - 1] = soLuong;
        }

        for (const [thang, soLuong] of Object.entries(baiTheoThang)) {
            baiData[parseInt(thang) - 1] = soLuong;
        }

        lineChart = new Chart(ctx, {
            type: 'line'
            , data: {
                labels: [
                    "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6"
                    , "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
                ]
                , datasets: [
                    // {
                    //     label: "Số bài giảng"
                    //     , data: thongKeData
                    //     , borderColor: "rgba(54, 162, 235, 1)"
                    //     , backgroundColor: "rgba(54, 162, 235, 0.2)"
                    //     , pointBackgroundColor: "rgba(255, 99, 132, 1)"
                    //     , pointBorderColor: "#fff"
                    //     , pointHoverBackgroundColor: "#fff"
                    //     , pointHoverBorderColor: "rgba(255,99,132,1)"
                    //     , fill: true
                    //     , tension: 0.4
                    //     , borderWidth: 2
                    // }
                    // ,
                    {
                        label: "Số bài học"
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

        fetch(`/giang-vien/bieu-do-thong-ke?nam=${nam}&maBaiGiang=${maBaiGiang}`)
            .then(res => {
                return res.json();
            })
            .then(data => {
                const newData = Array(12).fill(0);
                for (const [thang, soLuong] of Object.entries(data)) {
                    newData[parseInt(thang) - 1] = soLuong;
                }
                lineChart.data.datasets[0].data = newData;
                lineChart.update();
            })
            .catch(err => {
                console.error('Lỗi khi fetch dữ liệu biểu đồ:', err);
            });


    });

</script>
@endsection
