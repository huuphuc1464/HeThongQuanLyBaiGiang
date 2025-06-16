@extends('layouts.teacherLayout')
@section('title','Giảng viên - Thêm sự kiện Zoom')
@section('tenTrang', 'Thêm sự kiện Zoom')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Thêm sự kiện Zoom</h3>
            <div class="tile-body">
                <form action="{{ route('giangvien.su-kien-zoom.them') }}" method="POST" class="row">
                    @csrf
                    <div class="form-group col-md-4">
                        <label class="control-label">Lớp học phần <span class="text-danger">*</span></label>
                        <select class="form-control" name="MaLopHocPhan" required>
                            <option value="" disabled {{ old('MaLopHocPhan') == null ? 'selected' : '' }}>-- Chọn lớp học phần --</option>
                            @foreach($lopHocPhan as $lop)
                            <option value="{{ $lop->MaLopHocPhan }}" {{ old('MaLopHocPhan', $user->MaLopHocPhan ?? '') == $lop->MaLopHocPhan ? 'selected' : '' }}>
                                {{ $lop->TenLopHocPhan }}
                            </option>
                            @endforeach
                        </select>
                        @error('MaLopHocPhan') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Tên sự kiện <span class="text-danger">*</span> </label>
                        <input class="form-control" type="text" name="TenSuKien" maxlength="100" placeholder="Nhập tên sự kiện" required value="{{ old('TenSuKien') }}">
                        @error('TenSuKien') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Mô tả sự kiện</label>
                        <input class="form-control" type="text" name="MoTa" maxlength="255" placeholder="Nhập mô tả sự kiện" value="{{ old('MoTa') }}">
                        @error('MoTa') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                        <input class="form-control" type="datetime-local" name="ThoiGianBatDau" min="{{ now()->format('Y-m-d\TH:i') }}" required value="{{ old('ThoiGianBatDau') ? \Carbon\Carbon::parse(old('ThoiGianBatDau'))->format('Y-m-d\TH:i') : '' }}">
                        @error('ThoiGianBatDau') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                        <input class="form-control" type="datetime-local" name="ThoiGianKetThuc" min="{{ now()->format('Y-m-d\TH:i') }}" required value="{{ old('ThoiGianKetThuc') ? \Carbon\Carbon::parse(old('ThoiGianKetThuc'))->format('Y-m-d\TH:i') : '' }}">
                        @error('ThoiGianKetThuc') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Mật khẩu sự kiện<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="MatKhauSuKien" maxlength="100" minlength="6 " placeholder="Nhập mật khẩu sự kiện" value="{{ old('MatKhauSuKien') }}">
                        @error('MatKhauSuKien') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-12 mt-1">
                        <button class="btn btn-success" type="submit">Lưu lại</button>
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
