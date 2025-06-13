@extends('layouts.adminLayout')
@section('title','Admin - Quản lý Khoa')
@section('content')
<div class="container">
    <h2 class="mb-4">Quản lý Khoa</h2>

    <!-- Tìm kiếm khoa -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Tìm kiếm Khoa</span>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemKhoa">Thêm Khoa</button>
        </div>
        <div class="card-body">
            <form class="row g-3" method="GET" action="{{ route('admin.quan-ly-khoa.danh-sach') }}">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="search" placeholder="Nhập tên khoa cần tìm" value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách các khoa -->
    <div class="card">
        <div class="card-header">Danh sách Khoa</div>
        <div class="card-body">
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

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Khoa</th>
                        <th>Mô tả</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSachKhoa as $index => $khoa)
                    <tr>
                        <td>{{ $danhSachKhoa->firstItem() + $index }}</td>
                        <td>{{ $khoa->TenKhoa }}</td>
                        <td>{{ $khoa->MoTa }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalSuaKhoa" 
                                data-id="{{ $khoa->MaKhoa }}"
                                data-ten="{{ $khoa->TenKhoa }}"
                                data-mota="{{ $khoa->MoTa }}">Sửa</button>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalXacNhanXoa"
                                data-id="{{ $khoa->MaKhoa }}">Xóa</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center">
                {{ $danhSachKhoa->appends(['search' => $search])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Khoa -->
<div class="modal fade" id="modalThemKhoa" tabindex="-1" aria-labelledby="themKhoaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="themKhoaLabel">Thêm Khoa Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.quan-ly-khoa.them-moi') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tenKhoa" class="form-label">Tên Khoa</label>
                        <input type="text" class="form-control" id="tenKhoa" name="TenKhoa" required>
                    </div>
                    <div class="mb-3">
                        <label for="moTa" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="moTa" name="MoTa" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Khoa -->
<div class="modal fade" id="modalSuaKhoa" tabindex="-1" aria-labelledby="suaKhoaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suaKhoaLabel">Sửa Khoa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSuaKhoa" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="tenKhoaSua" class="form-label">Tên Khoa</label>
                        <input type="text" class="form-control" id="tenKhoaSua" name="TenKhoa" required>
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
                Bạn có chắc chắn muốn xóa khoa này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formXoaKhoa" method="POST">
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
    // Xử lý sự kiện khi mở modal sửa
    $('#modalSuaKhoa').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var ten = button.data('ten');
        var mota = button.data('mota');
        
        var modal = $(this);
        modal.find('#formSuaKhoa').attr('action', '/admin/quan-ly-khoa/' + id);
        modal.find('#tenKhoaSua').val(ten);
        modal.find('#moTaSua').val(mota);
    });

    // Xử lý sự kiện khi mở modal xóa
    $('#modalXacNhanXoa').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        
        var modal = $(this);
        modal.find('#formXoaKhoa').attr('action', '/admin/quan-ly-khoa/' + id);
    });
</script>
@endpush
@endsection 