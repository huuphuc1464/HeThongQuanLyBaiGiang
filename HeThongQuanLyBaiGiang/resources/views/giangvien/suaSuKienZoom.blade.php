@extends('layouts.teacherLayout')
@section('title','Giảng viên - Cập nhật sự kiện Zoom')
@section('tenTrang', 'Danh sách sự kiện / Cập nhật sự kiện Zoom / ' . $suKienZoom->MaSuKienZoom)
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Cập nhật sự kiện Zoom</h3>
            <div class="tile-body">
                <form action="{{ route('giangvien.su-kien-zoom.sua', ['id' => $suKienZoom->MaSuKienZoom]) }}" method="POST" class="row">
                    @csrf
                    @method('PUT')

                    <div class="form-group col-md-4">
                        <label class="control-label">Tên lớp học phần</label>
                        <input class="form-control" type="text" disabled value="{{ $suKienZoom->TenLopHocPhan }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Tên sự kiện <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="TenSuKien" maxlength="100" required value="{{ old('TenSuKien', $suKienZoom->TenSuKien) }}">
                        @error('TenSuKien') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Mô tả sự kiện</label>
                        <input class="form-control" type="text" name="MoTa" maxlength="255" value="{{ old('MoTa', $suKienZoom->MoTa) }}">
                        @error('MoTa') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                        <input class="form-control" type="datetime-local" name="ThoiGianBatDau" value="{{ old('ThoiGianBatDau', \Carbon\Carbon::parse($suKienZoom->ThoiGianBatDau)->format('Y-m-d\TH:i')) }}" required>
                        @error('ThoiGianBatDau') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                        <input class="form-control" type="datetime-local" name="ThoiGianKetThuc" value="{{ old('ThoiGianKetThuc', \Carbon\Carbon::parse($suKienZoom->ThoiGianKetThuc)->format('Y-m-d\TH:i')) }}" required>
                        @error('ThoiGianKetThuc') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Mật khẩu sự kiện <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="MatKhauSuKien" maxlength="100" minlength="6" value="{{ old('MatKhauSuKien', $suKienZoom->MatKhauSuKien) }}">
                        @error('MatKhauSuKien') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-12 mt-1">
                        <button class="btn btn-success" type="submit">Cập nhật</button>
                        <a class="btn btn-danger" href="{{ url()->previous() }}">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('./css/teacher/form.css') }}">
@endsection
