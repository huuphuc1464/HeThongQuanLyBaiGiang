@extends('layouts.studentLayout')

@section('title', 'Kết quả bài kiểm tra')

@section('style')
<link rel="stylesheet" href="{{ asset('css/student/baikiemtra.css') }}">

@endsection

@section('sidebar')
@include('layouts.sidebarBaiKiemTra')
@endsection

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Kết quả bài kiểm tra: {{ $baiKiemTra->TenBaiKiemTra }}</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Lớp học phần:</strong> {{ $baiKiemTra->lopHocPhan->TenLopHocPhan }}</p>
                    <p><strong>Giảng viên:</strong> {{ $baiKiemTra->giangVien->HoTen }}</p>
                    <p><strong>Thời gian làm bài:</strong> {{ $baiKiemTra->ThoiGianLamBai }} phút</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Ngày nộp bài:</strong> {{ \Carbon\Carbon::parse($ketQua->NgayNop)->format('H:i:s d/m/Y')
                        }}</p>
                    <p><strong>Tổng số câu:</strong> {{ $ketQua->TongSoCauHoi }} câu</p>
                    @if(!isset($khongChoXemKetQua))
                    <p><strong>Số câu đúng:</strong> {{ $ketQua->TongCauDung }} câu</p>
                    <p><strong>Điểm số:</strong> {{ number_format($ketQua->TongCauDung/$ketQua->TongSoCauHoi * 10, 1) }}
                        điểm</p>
                    @endif
                </div>
            </div>

            @if(isset($khongChoXemKetQua))
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Giảng viên không cho phép xem chi tiết kết quả bài làm.
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">STT</th>
                            <th style="width: 55%">Câu hỏi</th>
                            <th style="width: 15%">Đáp án của bạn</th>
                            <th style="width: 15%">Đáp án đúng</th>
                            <th style="width: 10%">Kết quả</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ketQua->chiTietKetQua as $index => $chiTiet)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="textarea-mock">{{ $chiTiet->cauHoi->CauHoi }}</div>
                            </td>
                            <td class="text-center">{{ $chiTiet->DapAnSinhVien }}</td>
                            <td class="text-center toggle-dapan-dung" title="Đáp án đúng">
                                <span>
                                    {{ $chiTiet->cauHoi->DapAnDung }}
                                </span>
                                <br>
                                <span class="text-success small dapan-dung-noidung" style="display:none;">
                                    @php
                                    $dapAnDung = $chiTiet->cauHoi['DapAn' . $chiTiet->cauHoi->DapAnDung];
                                    @endphp
                                    {{ $dapAnDung }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($chiTiet->KetQua)
                                <span class="badge bg-success">Đúng</span>
                                @else
                                <span class="badge bg-danger">Sai</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('danh-sach-bai-kiem-tra') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-dapan-dung').forEach(function (el) {
            el.addEventListener('click', function () {
                let content = this.querySelector('.dapan-dung-noidung');
                console.log(this);
                if (content) {
                    content.style.display = (content.style.display === 'none' || content.style.display === '') ? 'inline' : 'none';
                }
            });
        });
    });
</script>
@endsection