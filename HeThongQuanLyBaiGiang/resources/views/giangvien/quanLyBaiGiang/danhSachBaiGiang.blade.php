@extends('layouts.teacherLayout')
@section('title','Giảng viên - Danh sách bài giảng')
@section('tenTrang', 'Danh sách bài giảng')
@section('content')
<div class="col-md-12">
    <div class="tile">
        <h3 class="tile-title">Danh sách bài giảng</h3>
        <div class="tile-body">
            <div class="row pb-2 align-items-center">
                <div class="col-12 col-md-4 p-0 mb-2 mb-md-0">
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalThem" title="Thêm bài giảng">
                        <i class="fas fa-plus"></i> Thêm bài giảng
                    </button>
                </div>
                <div class="col-12 col-md-8 p-0 d-flex justify-content-end">
                    <form method="GET" class="d-flex align-items-center gap-2 border rounded p-2 ms-auto flex-nowrap" style="min-width: 250px;" title="Tìm kiếm theo tên bài giảng, mô tả, tên khoa">
                        <label for="search" class="fw-bold mb-0">Tìm kiếm:</label>
                        <input type="text" name="search" id="search" class="form-control form-control-sm w-auto" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row element-button table-responsive">
            <table class="table table-hover table-bordered" id="zoomTable">
                <thead class="table-secondary">
                    <tr>
                        <th>Mã</th>
                        <th>Ảnh bài giảng</th>
                        <th>Tên bài giảng</th>
                        <th>Tên khoa</th>
                        <th>Mô tả</th>
                        <th>Thời gian tạo</th>
                        <th>Trạng thái</th>
                        <th>Tính năng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSachBaiGiang as $baiGiang)
                    <tr style="cursor: pointer;" data-href="{{ route('giangvien.bai-giang.chuong.danh-sach', ['maBaiGiang'=> $baiGiang->MaBaiGiang]) }}">
                        <td>#{{ $baiGiang->MaBaiGiang }}</td>
                        <td class="text-center">
                            <img src="{{ asset($baiGiang->AnhBaiGiang ?? 'img/hocphan/default.png') }}" alt="{{ $baiGiang->TenBaiGiang ?? "Default" }}" width="40" height="40" class="rounded-circle">
                        </td>
                        <td>{{ $baiGiang->TenBaiGiang }}</td>
                        <td>{{ $baiGiang->TenKhoa }}</td>
                        <td>{{ $baiGiang->MoTa }}</td>
                        <td>{{ \Carbon\Carbon::parse($baiGiang->created_at)->timezone('Asia/Ho_Chi_Minh')->format('H:i:s d/m/Y') }}</td>
                        <td>
                            @if($baiGiang->TrangThai)
                            <span class="badge bg-success">Hiện</span>
                            @else
                            <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">

                                @if($baiGiang->TrangThai)
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalXacNhanXoa" data-id="{{ $baiGiang->MaBaiGiang }}" data-ten="{{ $baiGiang->TenBaiGiang }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @else
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalXacNhanKhoiPhuc" data-id="{{ $baiGiang->MaBaiGiang }}" data-ten="{{ $baiGiang->TenBaiGiang }}">
                                    <i class="fas fa-undo"></i>
                                </button>
                                @endif
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSua" data-action="{{ route('giangvien.bai-giang.cap-nhat', ['id'=>$baiGiang->MaBaiGiang]) }}" data-bai-giang='@json($baiGiang)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">Không tìm thấy kết quả nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <x-phan-trang :data="$danhSachBaiGiang" label="bài giảng" />
        </div>
    </div>
</div>

@if ($errors->any() && session('form_action') === 'them')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const myModal = new bootstrap.Modal(document.getElementById('modalThem'));
        myModal.show();
    });

</script>
@endif

@if ($errors->any() && session('form_action') === 'sua')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const id = @json(session('MaBaiGiang'));
        document.getElementById('formSuaBaiGiang').action = `/giang-vien/bai-giang/cap-nhat/${id}`;
        const modalSua = new bootstrap.Modal(document.getElementById('modalSua'));
        modalSua.show();
    });

</script>
@endif


<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="modalXacNhanXoa" tabindex="-1" aria-labelledby="xacNhanXoaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xacNhanXoaLabel">Xác nhận xóa</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa bài giảng <strong id="tenBaiGiangXoa"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formXoaBaiGiang" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác nhận Khôi phục -->
<div class="modal fade" id="modalXacNhanKhoiPhuc" tabindex="-1" aria-labelledby="xacNhanKhoiPhucLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xacNhanKhoiPhucLabel">Xác nhận khôi phục</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn khôi phục bài giảng <strong id="tenBaiGiangKhoiPhuc"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formKhoiPhucBaiGiang" method="POST">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn btn-success">Khôi phục</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm bài giảng -->
<div class="modal fade" id="modalThem" tabindex="-1" aria-labelledby="modalThemLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="formBaiGiang" method="POST" action="{{ route('giangvien.bai-giang.them') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalThemLabel">Thêm bài giảng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrors" class="alert alert-danger" style="display: none;"></div>

                    <div class="mb-3">
                        <label for="TenBaiGiang" class="form-label fw-semibold">Tên bài giảng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="TenBaiGiang" name="TenBaiGiang" required value="{{ old('TenBaiGiang') }}">
                        @error('TenBaiGiang')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="MaKhoa" class="form-label fw-semibold">Thuộc khoa <span class="text-danger">*</span></label>
                        <select class="form-select" id="MaKhoa" name="MaKhoa" required>
                            <option value="">-- Chọn khoa --</option>
                            @foreach($danhSachKhoa as $khoa)
                            <option value="{{ $khoa->MaKhoa }}" {{ old('MaKhoa') == $khoa->MaKhoa ? 'selected' : '' }}>{{ $khoa->TenKhoa }}</option>
                            @endforeach
                        </select>
                        @error('MaKhoa')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="TrangThai" class="form-label fw-semibold">Trạng thái<span class="text-danger">*</span></label>
                        <select class="form-select" id="TrangThai" name="TrangThai" required>
                            <option value="">-- Chọn khoa --</option>
                            <option value="1" {{ old('TrangThai') == 1 ? 'selected' : '' }}>Hiện</option>
                            <option value="0" {{ old('TrangThai') == 0 ? 'selected' : '' }}>Ẩn</option>
                        </select>
                        @error('TrangThai')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="AnhBaiGiang" class="form-label fw-semibold">Hình ảnh</label>
                        <small class="text-mute d-block mb-2">(JPG, JPEG, PNG. Tối đa 2MB)</small>
                        <input class="form-control" type="file" id="AnhBaiGiang" name="AnhBaiGiang" accept="image/*">
                        <div id="AnhBaiGiangError" class="text-danger mt-1" style="display: none;"></div>
                        <div class="mt-2">
                            <img id="previewAnhBaiGiang" src="#" alt="Preview ảnh" style="max-width: 150px; display: none;" class="img-thumbnail">
                        </div>
                        @error('AnhBaiGiang')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="MoTa" class="form-label fw-semibold">Mô tả</label>
                        <textarea class="form-control" id="MoTa" name="MoTa" rows="3">{{ old('MoTa') }}</textarea>
                        @error('MoTa')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu bài giảng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa bài giảng -->
<div class="modal fade" id="modalSua" tabindex="-1" aria-labelledby="modalSuaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="formSuaBaiGiang" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSuaLabel">Sửa bài giảng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div id="modalSuaErrors" class="alert alert-danger" style="display: none;"></div>

                    <div class="mb-3">
                        <label for="TenBaiGiangSua" class="form-label">Tên bài giảng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="TenBaiGiangSua" name="TenBaiGiang" required value="{{ old('TenBaiGiang') }}">
                        @error('TenBaiGiang')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="MaKhoaSua" class="form-label">Thuộc khoa <span class="text-danger">*</span></label>
                        <select class="form-select" id="MaKhoaSua" name="MaKhoa" required>
                            <option value="">-- Chọn khoa --</option>
                            @foreach($danhSachKhoa as $khoa)
                            <option value="{{ $khoa->MaKhoa }}" {{ old('MaKhoa') == $khoa->MaKhoa ? 'selected' : '' }}>{{ $khoa->TenKhoa }}</option>
                            @endforeach
                        </select>
                        @error('MaKhoa')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="TrangThaiSua" class="form-label fw-semibold">Trạng thái<span class="text-danger">*</span></label>
                        <select class="form-select" id="TrangThaiSua" name="TrangThai" required>
                            <option value="">-- Chọn trạng thái --</option>
                            <option value="1" {{ old('TrangThai') == 1 ? 'selected' : '' }}>Hiện</option>
                            <option value="0" {{ old('TrangThai') == 0 ? 'selected' : '' }}>Ẩn</option>
                        </select>
                        @error('TrangThai')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="AnhBaiGiangSua" class="form-label">Hình ảnh</label>
                        <small class="text-mute d-block mb-2">(JPG, JPEG, PNG. Tối đa 2MB)</small>
                        <input class="form-control" type="file" id="AnhBaiGiangSua" name="AnhBaiGiang" accept="image/*">
                        <div id="AnhBaiGiangSuaError" class="text-danger mt-1" style="display: none;"></div>
                        <div class="mt-2">
                            <img id="previewAnhBaiGiangSua" src="#" alt="Ảnh hiện tại" class="img-thumbnail" style="max-width: 150px; display: none;">
                        </div>
                        @error('AnhBaiGiang')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="MoTaSua" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="MoTaSua" name="MoTa" rows="3"></textarea>
                        @error('MoTa')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .modal-body {
        max-height: 70vh;
    }

    .tile {
        position: relative;
        background: #ffffff;
        border-radius: .375rem;
        padding: 20px;
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
        margin-bottom: 30px;
        transition: all 0.3s ease-in-out;
    }

    .tile .tile-title {
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 20px;
        border-bottom: 2px solid #FFD43B;
        padding-bottom: 10px;
        padding-left: 5px;
        color: black;
    }

    .element-button {
        position: relative;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

</style>
@endsection

@section('scripts')
<script>
    // Hàm preview ảnh
    function handleImagePreview(inputEl, previewEl, errorEl) {
        const file = inputEl.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024;

        errorEl.style.display = 'none';
        errorEl.textContent = '';
        previewEl.style.display = 'none';

        if (file) {
            if (!allowedTypes.includes(file.type)) {
                errorEl.textContent = 'Chỉ chấp nhận ảnh JPG, JPEG, PNG.';
                errorEl.style.display = 'block';
                inputEl.value = '';
                return;
            }

            if (file.size > maxSize) {
                errorEl.textContent = 'Kích thước ảnh không được vượt quá 2MB.';
                errorEl.style.display = 'block';
                inputEl.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                previewEl.src = e.target.result;
                previewEl.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Xử lý modal xác nhận (xóa / khôi phục)
    function setupModalConfirm(modalId, formSelector, textSelector, routeBase) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-ten');

            modal.querySelector(textSelector).textContent = `"${name}"`;
            modal.querySelector(formSelector).action = `${routeBase}/${id}`;
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const modalSua = document.getElementById('modalSua');
        if (modalSua) {
            const form = modalSua.querySelector('#formSuaBaiGiang');
            const preview = modalSua.querySelector('#previewAnhBaiGiangSua');
            const inputAnh = modalSua.querySelector('#AnhBaiGiangSua');
            const errorDiv = modalSua.querySelector('#AnhBaiGiangSuaError');

            modalSua.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const baiGiang = JSON.parse(button.getAttribute('data-bai-giang'));
                const action = button.getAttribute('data-action');

                form.action = action;

                modalSua.querySelector('#TenBaiGiangSua').value = baiGiang.TenBaiGiang || '';
                modalSua.querySelector('#MaKhoaSua').value = baiGiang.MaKhoa || '';
                modalSua.querySelector('#MoTaSua').value = baiGiang.MoTa || '';
                modalSua.querySelector('#TrangThaiSua').value = baiGiang.TrangThai ?? '';

                preview.style.display = 'none';
                preview.src = '#';
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
                inputAnh.value = '';

                if (baiGiang.AnhBaiGiang) {
                    preview.src = '{{ asset('') }}' + baiGiang.AnhBaiGiang;
                    preview.style.display = 'block';
                }
            });

            inputAnh.addEventListener('change', () => handleImagePreview(inputAnh, preview, errorDiv));
        }

        // Preview ảnh cho form thêm bài giảng
        const inputAnhThem = document.getElementById('AnhBaiGiang');
        if (inputAnhThem) {
            const previewThem = document.getElementById('previewAnhBaiGiang');
            const errorThem = document.getElementById('AnhBaiGiangError');
            inputAnhThem.addEventListener('change', () => handleImagePreview(inputAnhThem, previewThem, errorThem));
        }

        // Click row nếu không click vào button/link
        document.querySelectorAll('tr[data-href]').forEach(row => {
            row.addEventListener('click', e => {
                if (!e.target.closest('a, button')) {
                    window.location.href = row.dataset.href;
                }
            });
        });
    });

    // Gắn modal xác nhận
    setupModalConfirm('modalXacNhanXoa', '#formXoaBaiGiang', '#tenBaiGiangXoa', '/giang-vien/bai-giang/xoa');
    setupModalConfirm('modalXacNhanKhoiPhuc', '#formKhoiPhucBaiGiang', '#tenBaiGiangKhoiPhuc', '/giang-vien/bai-giang/khoi-phuc');

</script>




@endsection
