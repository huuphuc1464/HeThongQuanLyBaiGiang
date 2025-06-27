<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Thay đổi thông tin cá nhân</h3>
            <div class="tile-body">
                <form action="{{ route('doi-thong-tin.submit') }}" method="POST" class="row" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group col-md-4">
                        <label class="control-label">Mã tài khoản</label>
                        <input class="form-control" type="text" readonly disabled value="{{ $user->MaNguoiDung }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Tên tài khoản</label>
                        <input class="form-control" type="text" readonly disabled value="{{ $user->TenTaiKhoan }}">
                    </div>

                    @if($user->MaVaiTro == 3)
                    <div class="form-group col-md-4">
                        <label class="control-label">Mã số sinh viên</label>
                        <input class="form-control" type="text" readonly disabled value="{{ $user->MSSV }}">
                    </div>
                    @endif

                    <div class="form-group col-md-4">
                        <label class="control-label">Địa chỉ email</label>
                        <input class="form-control" type="text" readonly disabled value="{{ $user->Email }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Họ và tên <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="HoTen" maxlength="100" required value="{{ old('HoTen', $user->HoTen) }}">
                        @error('HoTen') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Địa chỉ thường trú <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="DiaChi" maxlength="255" required value="{{ old('DiaChi', $user->DiaChi) }}">
                        @error('DiaChi') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="SoDienThoai" maxlength="10" minlength="10" required value="{{ old('SoDienThoai', $user->SoDienThoai) }}">
                        @error('SoDienThoai') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Ngày sinh <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" name="NgaySinh" min="1950-01-01" max="{{ now()->subYears(17)->format('Y-m-d') }}" required value="{{ old('NgaySinh', $user->NgaySinh) }}">
                        @error('NgaySinh') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Giới tính <span class="text-danger">*</span></label>
                        <select class="form-control" name="GioiTinh" required>
                            <option value="" disabled {{ old('GioiTinh', $user->GioiTinh) == null ? 'selected' : '' }}>-- Chọn giới tính --</option>
                            <option value="Nam" {{ old('GioiTinh', $user->GioiTinh) == 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ old('GioiTinh', $user->GioiTinh) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                        </select>
                        @error('GioiTinh') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>


                    <div class="form-group col-md-12">
                        <label class="control-label">Ảnh đại diện <span class="text-danger">*</span></label>
                        <label class="col-12"><small>Chỉ chấp nhận hình ảnh có định dạng JPG, JPEG, PNG. Kích thước tối đa 2MB.</small></label>
                        <div id="myfileupload">
                            <input type="file" id="uploadfile" name="AnhDaiDien" accept=".jpg, .jpeg, .png" onchange="readURL(this);" />
                        </div>
                        @error('AnhDaiDien')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror

                        <div id="thumbbox" class="mt-2">
                            <img height="200" width="200" alt="{{ $user->AnhDaiDien}}" id="thumbimage" src="{{ asset($user->AnhDaiDien) }}" style="{{ $user->AnhDaiDien ? '' : 'display: none;' }} object-fit: cover; border: 1px solid #ccc;" />
                            <a class="removeimg" href="javascript:" style="{{ $user->AnhDaiDien ? '' : 'display: none;' }}"></a>
                        </div>
                        <div id="boxchoice" class="mt-2">
                            <a href="javascript:" class="Choicefile btn btn-outline-primary">
                                <i class='fas fa-upload'></i> Chọn ảnh
                            </a>
                            <p class="filename mt-2"></p>
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <button class="btn btn-success" type="submit">Lưu lại</button>
                        <a class="btn btn-danger" href="{{ url()->previous() }}">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
