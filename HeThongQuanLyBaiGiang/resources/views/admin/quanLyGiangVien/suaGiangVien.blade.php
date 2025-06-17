@extends('layouts.adminLayout')
@section('title', 'Quản trị viên - Sửa giảng viên')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Sửa giảng viên</h3>
                <div class="tile-body">
                    <form action="{{ route('admin.giang-vien.sua', $giangVien->MaNguoiDung) }}" method="POST" class="row" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group col-md-4">
                            <label class="control-label">Họ và tên <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="HoTen" maxlength="100" value="{{ old('HoTen', $giangVien->HoTen) }}" required>
                            @error('HoTen') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Địa chỉ thường trú</label>
                            <input class="form-control" type="text" name="DiaChi" maxlength="255" value="{{ old('DiaChi', $giangVien->DiaChi) }}">
                            @error('DiaChi') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Ngày sinh</label>
                            <input class="form-control" type="date" name="NgaySinh" value="{{ old('NgaySinh', $giangVien->NgaySinh) }}" min="1950-01-01" max="{{ now()->subYears(17)->format('Y-m-d') }}" required>
                            @error('NgaySinh') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Giới tính</label>
                            <select class="form-control" name="GioiTinh" required>
                                <option value="Nam" {{ old('GioiTinh', $giangVien->GioiTinh) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('GioiTinh', $giangVien->GioiTinh) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            </select>
                            @error('GioiTinh') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label">Ảnh đại diện</label>
                            <label class="col-12">
                                <small>Chỉ chấp nhận hình ảnh có định dạng JPG, JPEG, PNG. Kích thước tối đa 2MB.</small>
                            </label>

                            <div id="myfileupload">
                                <input type="file" id="uploadfile" name="AnhDaiDien" accept="image/*" onchange="readURL(this);" />
                            </div>
                            @error('AnhDaiDien')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror

                            <div id="thumbbox" class="mt-2">
                                <img height="200" width="200" alt="{{ $giangVien->AnhDaiDien }}" id="thumbimage" src="{{ $giangVien->AnhDaiDien ? asset($giangVien->AnhDaiDien) : '' }}" style="{{ $giangVien->AnhDaiDien ? '' : 'display: none;' }} object-fit: cover; border: 1px solid #ccc;" />

                                <a class="removeimg" href="javascript:" style="{{ $giangVien->AnhDaiDien ? '' : 'display: none;' }}"></a>
                            </div>

                            <div id="boxchoice" class="mt-2">
                                <a href="javascript:" class="Choicefile btn btn-outline-primary">
                                    <i class='fas fa-upload'></i> Chọn ảnh
                                </a>
                                <p class="filename mt-2"></p>
                            </div>
                        </div>



                        <div class="form-group col-12 mt-1">
                            <button class="btn btn-success" type="submit">Cập nhật</button>
                            <a class="btn btn-secondary" href="{{ route('admin.giang-vien.danh-sach') }}">Quay lại</a>
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
