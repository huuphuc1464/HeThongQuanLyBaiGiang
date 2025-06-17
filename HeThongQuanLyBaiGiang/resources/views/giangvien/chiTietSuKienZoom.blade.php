@extends('layouts.teacherLayout')
@section('title','Giảng viên - Chi tiết sự kiện Zoom')
@section('tenTrang', 'Danh sách sự kiện / Chi tiết sự kiện Zoom / ' . $suKienZoom->MaSuKienZoom)
@section('content')
<div class="row">
    <div class="pb-2">
        <a class="btn btn-warning btn-sm" href="{{ route('giangvien.su-kien-zoom.form-sua', ['id' => $suKienZoom->MaSuKienZoom]) }}" title="Cập nhật sự kiện Zoom">
            <i class="fas fa-edit"></i></i> Cập nhật sự kiện Zoom
        </a>
    </div>
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Chi tiết sự kiện Zoom</h3>



            <div class="tile-body row">
                <div class="form-group col-md-4">
                    <label class="control-label">Tên lớp học phần</label>
                    <input class="form-control" type="text" readonly value="{{ $suKienZoom->TenLopHocPhan }}">
                </div>

                <div class="form-group col-md-4">
                    <label class="control-label">Tên sự kiện</label>
                    <input class="form-control" type="text" readonly value="{{ $suKienZoom->TenSuKien }}">
                </div>

                <div class="form-group col-md-4">
                    <label class="control-label">Mô tả sự kiện</label>
                    <input class="form-control" type="text" readonly value="{{ $suKienZoom->MoTa }}">
                </div>

                <div class="form-group col-md-4">
                    <label class="control-label">Thời gian bắt đầu</label>
                    <input class="form-control" type="text" readonly value="{{ \Carbon\Carbon::parse($suKienZoom->ThoiGianBatDau)->format('d/m/Y H:i') }}">
                </div>

                <div class="form-group col-md-4">
                    <label class="control-label">Thời gian kết thúc</label>
                    <input class="form-control" type="text" readonly value="{{ \Carbon\Carbon::parse($suKienZoom->ThoiGianKetThuc)->format('d/m/Y H:i') }}">
                </div>

                <div class="form-group col-md-4">
                    <label class="control-label">Link tham gia sự kiện</label>
                    <input class="form-control" type="text" readonly value="{{ $suKienZoom->LinkSuKien }}">
                </div>

                <div class="form-group col-md-4">
                    <label class="control-label">Mật khẩu sự kiện</label>
                    <input class="form-control" type="text" readonly value="{{ $suKienZoom->MatKhauSuKien }}">
                </div>

                <div class="form-group col-md-4">
                    <label class="control-label">Thời gian tạo</label>
                    <input class="form-control" type="text" readonly value="{{ \Carbon\Carbon::parse($suKienZoom->created_at)->format('d/m/Y H:i:s') }}">
                </div>

                <div class="form-group col-md-4">
                    <label class="control-label">Thời gian cập nhật cuối</label>
                    <input class="form-control" type="text" readonly value="{{ \Carbon\Carbon::parse($suKienZoom->updated_at)->format('d/m/Y H:i:s') }}">
                </div>

                <div class="form-group col-md-4">
                    <label class="control-label">Khóa chủ trì <small>(Không chia sẻ cho bất kỳ ai)</small> </label>
                    <input class="form-control" type="text" readonly value="{{ $suKienZoom->KhoaChuTri }}">
                </div>

                <div class="form-group col-12 mt-2">
                    <a class="btn btn-secondary" href="{{ route('giangvien.su-kien-zoom.danhsach') }}">Quay lại</a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('./css/teacher/form.css') }}">
@endsection
