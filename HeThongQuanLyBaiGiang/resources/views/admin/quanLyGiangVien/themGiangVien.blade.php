@extends('layouts.adminLayout')
@section('title','Quản trị viên - Thêm giảng viên')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Thêm giảng viên</h3>
                <div class="tile-body">
                    <form action="{{ route('admin.giang-vien.them') }}" method="POST" class="row" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group col-md-4">
                            <label class="control-label">Họ và tên <span class="text-danger">*</span> </label>
                            <input class="form-control" type="text" name="HoTen" maxlength="100" placeholder="Nhập họ và tên" required value="{{ old('HoTen') }}">
                            @error('HoTen') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Địa chỉ email <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="Email" maxlength="100" placeholder="Nhập địa chỉ email" required value="{{ old('Email') }}">
                            @error('Email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Địa chỉ thường trú <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="DiaChi" maxlength="255" required placeholder="Nhập địa chỉ thường trú" value="{{ old('DiaChi') }}">
                            @error('DiaChi') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="SoDienThoai" maxlength="10" minlength="10" placeholder="Nhập số điện thoại" required value="{{ old('SoDienThoai') }}">
                            @error('SoDienThoai') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Ngày sinh <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="NgaySinh" min="1950-01-01" max="{{ now()->subYears(17)->format('Y-m-d') }}" required value="{{ old('NgaySinh') }}">
                            @error('NgaySinh') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Giới tính <span class="text-danger">*</span></label>
                            <select class="form-control" name="GioiTinh" required>
                                <option value="" disabled {{ old('GioiTinh') == null ? 'selected' : '' }}>-- Chọn giới tính --</option>
                                <option value="Nam" {{ old('GioiTinh') == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('GioiTinh') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            </select>
                            @error('GioiTinh') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label">Ảnh đại diện</label>
                            <label class="col-12"><small>Chỉ chấp nhận hình ảnh có định dạng JPG, JPEG, PNG. Kích thước tối đa 2MB.</small></label>
                            <div id="myfileupload">
                                <input type="file" id="uploadfile" name="AnhDaiDien" accept="image/*" onchange="readURL(this);" />
                            </div>
                            @error('AnhDaiDien')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror

                            <div id="thumbbox" class="mt-2">
                                <img height="200" width="200" alt="ảnh đại diện" id="thumbimage" style="display: none" />
                                <a class="removeimg" href="javascript:"></a>
                            </div>
                            <div id="boxchoice" class="mt-2">
                                <a href="javascript:" class="Choicefile btn btn-outline-primary">
                                    <i class='fas fa-upload'></i> Chọn ảnh
                                </a>
                                <p style="clear:both"></p>
                                <p class="filename mt-2"></p>
                            </div>
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
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('./css/doiThongTinCaNhan.css') }}">
@endsection


@section('scripts')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('thumbimage').src = e.target.result;
                document.getElementById('thumbimage').style.display = 'block';
                document.querySelector('.removeimg').style.display = 'block';
                document.querySelector('.filename').textContent = input.files[0].name;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.Choicefile')?.addEventListener('click', () => {
            document.getElementById('uploadfile').click();
        });

        document.querySelector('.removeimg')?.addEventListener('click', () => {
            document.getElementById('uploadfile').value = '';
            document.getElementById('thumbimage').style.display = 'none';
            document.querySelector('.removeimg').style.display = 'none';
            document.querySelector('.filename').textContent = '';
        });
    });

</script>
@endsection
