@extends('layouts.adminLayout')
@section('title', 'Admin - Quản lý Môn học')
@section('content')
<div class="container">
    <h2 class="mb-4">Quản lý Môn học</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tìm kiếm và lọc môn học -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Tìm kiếm & Lọc Môn học</span>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemMonHoc">Thêm Môn học</button>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.mon-hoc.danh-sach') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="search" placeholder="Nhập tên môn học cần tìm" value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-5">
                    <select class="form-select" id="filterKhoa" name="filterKhoa">
                        <option value="">-- Lọc theo Khoa --</option>
                        @foreach($danhSachKhoa as $khoa)
                            <option value="{{ $khoa->MaKhoa }}" {{ (isset($filterKhoa) && $filterKhoa == $khoa->MaKhoa) ? 'selected' : '' }}>
                                {{ $khoa->TenKhoa }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách môn học -->
    <div class="card">
        <div class="card-header">Danh sách Môn học</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Môn học</th>
                        <th>Khoa</th>
                        <th>Mô tả</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSachMonHoc as $index => $monHoc)
                    <tr>
                        <td>{{ $danhSachMonHoc->firstItem() + $index }}</td>
                        <td>{{ $monHoc->TenMonHoc }}</td>
                        <td>{{ $monHoc->khoa->TenKhoa }}</td>
                        <td>{{ $monHoc->MoTa }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalSuaMonHoc"
                                data-id="{{ $monHoc->MaMonHoc }}"
                                data-ten="{{ e($monHoc->TenMonHoc) }}"
                                data-makhoa="{{ $monHoc->MaKhoa }}"
                                data-mota="{{ e($monHoc->MoTa) }}">Sửa</button>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalXacNhanXoa"
                                data-id="{{ $monHoc->MaMonHoc }}">Xóa</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center">
                {{ $danhSachMonHoc->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Môn học -->
<div class="modal fade" id="modalThemMonHoc" tabindex="-1" aria-labelledby="themMonHocLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="themMonHocLabel">Thêm Môn học Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.mon-hoc.them-moi') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tenMonHoc" class="form-label">Tên Môn học</label>
                        <input type="text" class="form-control @error('TenMonHoc') is-invalid @enderror" id="tenMonHoc" name="TenMonHoc" value="{{ old('TenMonHoc') }}" required>
                        @error('TenMonHoc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="khoa" class="form-label">Khoa</label>
                        <select class="form-select @error('MaKhoa') is-invalid @enderror" id="khoa" name="MaKhoa" required>
                            <option value="">-- Chọn Khoa --</option>
                            @foreach($danhSachKhoa as $khoa)
                                <option value="{{ $khoa->MaKhoa }}" {{ old('MaKhoa') == $khoa->MaKhoa ? 'selected' : '' }}>
                                    {{ $khoa->TenKhoa }}
                                </option>
                            @endforeach
                        </select>
                        @error('MaKhoa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="moTa" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('MoTa') is-invalid @enderror" id="moTa" name="MoTa" rows="3">{{ old('MoTa') }}</textarea>
                        @error('MoTa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Môn học -->
<div class="modal fade" id="modalSuaMonHoc" tabindex="-1" aria-labelledby="suaMonHocLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suaMonHocLabel">Sửa Môn học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSuaMonHoc" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="monHocId" name="id">
                    <div class="mb-3">
                        <label for="tenMonHocSua" class="form-label">Tên Môn học</label>
                        <input type="text" class="form-control @error('TenMonHoc') is-invalid @enderror" id="tenMonHocSua" name="TenMonHoc" value="{{ old('TenMonHoc') }}" required>
                        @error('TenMonHoc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="khoaSua" class="form-label">Khoa</label>
                        <select class="form-select @error('MaKhoa') is-invalid @enderror" id="khoaSua" name="MaKhoa" required>
                            @foreach($danhSachKhoa as $khoa)
                                <option value="{{ $khoa->MaKhoa }}">{{ $khoa->TenKhoa }}</option>
                            @endforeach
                        </select>
                        @error('MaKhoa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="moTaSua" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('MoTa') is-invalid @enderror" id="moTaSua" name="MoTa" rows="3">{{ old('MoTa') }}</textarea>
                        @error('MoTa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="modalXacNhanXoa" tabindex="-1" aria-labelledby="xacNhanXoaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xacNhanXoaLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa môn học này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formXoaMonHoc" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const modalSuaMonHoc = document.getElementById('modalSuaMonHoc');
    modalSuaMonHoc.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const ten = button.getAttribute('data-ten');
        const maKhoa = button.getAttribute('data-makhoa');
        const mota = button.getAttribute('data-mota');

        const formSuaMonHoc = this.querySelector('#formSuaMonHoc');
        const monHocId = this.querySelector('#monHocId');
        const tenMonHocSua = this.querySelector('#tenMonHocSua');
        const khoaSua = this.querySelector('#khoaSua');
        const moTaSua = this.querySelector('#moTaSua');

        // Đặt action cho form
        formSuaMonHoc.action = '{{ route("admin.mon-hoc.cap-nhat", ":id") }}'.replace(':id', id);

        // Điền dữ liệu vào các trường
        monHocId.value = id;
        tenMonHocSua.value = ten || '';
        khoaSua.value = maKhoa || '';
        moTaSua.value = mota || '';

        // Xóa lỗi và reset trạng thái khi mở modal
        tenMonHocSua.classList.remove('is-invalid');
        moTaSua.classList.remove('is-invalid');
        khoaSua.classList.remove('is-invalid');
        this.querySelectorAll('.invalid-feedback').forEach(feedback => feedback.innerHTML = '');
    });

    modalSuaMonHoc.addEventListener('hidden.bs.modal', function () {
        const form = this.querySelector('#formSuaMonHoc');
        form.reset(); // Reset form về trạng thái ban đầu
        // Xóa tất cả các lớp lỗi và thông báo
        form.querySelectorAll('.form-control, .form-select').forEach(input => input.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(feedback => feedback.innerHTML = '');
    });

    const modalXacNhanXoa = document.getElementById('modalXacNhanXoa');
    modalXacNhanXoa.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        
        const formXoaMonHoc = this.querySelector('#formXoaMonHoc');
        formXoaMonHoc.action = '{{ route("admin.mon-hoc.xoa", ":id") }}'.replace(':id', id);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const modalThemMonHoc = document.getElementById('modalThemMonHoc');
        const modalSuaMonHoc = document.getElementById('modalSuaMonHoc');
        
        @if($errors->any() && (old('TenMonHoc') || old('MoTa')))
            const targetModal = @if(old('id')) modalSuaMonHoc @else modalThemMonHoc @endif;
            const bsModal = new bootstrap.Modal(targetModal);
            bsModal.show();
        @endif
    });
</script>
@endsection