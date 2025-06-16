@extends('layouts.adminLayout')

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
                            <button class="btn btn-sm btn-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalSuaMonHoc"
                                    data-monhoc="{{ json_encode($monHoc) }}">Sửa</button>
                            <button class="btn btn-sm btn-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalXacNhanXoa"
                                    data-monhoc-id="{{ $monHoc->MaMonHoc }}">Xóa</button>
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
            {{ $danhSachMonHoc->appends(request()->query())->links() }}
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
                        <input type="text" class="form-control @error('TenMonHoc') is-invalid @enderror" 
                               id="tenMonHoc" name="TenMonHoc" required value="{{ old('TenMonHoc') }}">
                        @error('TenMonHoc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="khoa" class="form-label">Khoa</label>
                        <select class="form-select @error('MaKhoa') is-invalid @enderror" 
                                id="khoa" name="MaKhoa" required>
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
                        <textarea class="form-control" id="moTa" name="MoTa" rows="3">{{ old('MoTa') }}</textarea>
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
                    <div class="mb-3">
                        <label for="tenMonHocSua" class="form-label">Tên Môn học</label>
                        <input type="text" class="form-control @error('TenMonHoc') is-invalid @enderror" 
                               id="tenMonHocSua" name="TenMonHoc" required>
                        @error('TenMonHoc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="khoaSua" class="form-label">Khoa</label>
                        <select class="form-select @error('MaKhoa') is-invalid @enderror" 
                                id="khoaSua" name="MaKhoa" required>
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
                        <textarea class="form-control" id="moTaSua" name="MoTa" rows="3"></textarea>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý modal sửa môn học
    const modalSuaMonHoc = document.getElementById('modalSuaMonHoc');
    modalSuaMonHoc.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const monHoc = JSON.parse(button.getAttribute('data-monhoc'));
        const form = document.getElementById('formSuaMonHoc');
        
        form.action = `/admin/mon-hoc/${monHoc.MaMonHoc}`;
        form.querySelector('#tenMonHocSua').value = monHoc.TenMonHoc;
        form.querySelector('#khoaSua').value = monHoc.MaKhoa;
        form.querySelector('#moTaSua').value = monHoc.MoTa;
    });

    // Xử lý modal xóa môn học
    const modalXacNhanXoa = document.getElementById('modalXacNhanXoa');
    modalXacNhanXoa.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const monHocId = button.getAttribute('data-monhoc-id');
        const form = document.getElementById('formXoaMonHoc');
        
        form.action = `/admin/mon-hoc/${monHocId}`;
    });
});
</script>
@endpush
@endsection
