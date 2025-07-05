@extends('layouts.adminLayout')
@section('title', 'Admin - Quản lý giảng viên')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Quản lý giảng viên</h2>
    <!-- Tìm kiếm giảng viên -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Tìm kiếm giảng viên</span>
            <a class="btn btn-primary" href="{{ route('admin.giang-vien.form-them') }}" title="Thêm giảng viên">
                <i class="fas fa-plus"></i> Thêm giảng viên
            </a>
        </div>
        <div class="card-body">
            <form class="row g-3" method="GET" action="{{ route('admin.giang-vien.danh-sach') }}">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="search"
                        placeholder="Nhập từ khóa tìm kiếm (tên, email, sđt)" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách giảng viên -->
    <div class="card table-responsive">
        <div class="card-header">Danh sách Giảng viên</div>
        <div class="card-body">
            <table class="table table-bordered align-middle text-center table-hover">
                <thead class="table-secondary">
                    <tr>
                        <th>Mã GV</th>
                        <th>Họ và tên</th>
                        <th>Ảnh đại diện</th>
                        <th>Địa chỉ</th>
                        <th>Email</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>SĐT</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSachGiangVien as $gv)
                    <tr>
                        <td>{{ $gv->MaNguoiDung }}</td>
                        <td>{{ $gv->HoTen }}</td>
                        <td>
                            @if($gv->AnhDaiDien)
                            <img src="{{ asset($gv->AnhDaiDien) }}" alt="Ảnh" class="img-card-person">

                            @else
                            <span class="text-muted">Không có</span>
                            @endif
                        </td>
                        <td>{{ $gv->DiaChi }}</td>
                        <td>{{ $gv->Email }}</td>
                        <td>{{ \Carbon\Carbon::parse($gv->NgaySinh)->format('d/m/Y') }}</td>
                        <td>{{ $gv->GioiTinh }}</td>
                        <td>{{ $gv->SoDienThoai }}</td>
                        <td>
                            <span class="badge {{ $gv->TrangThai == 1 ? 'bg-success' : 'bg-secondary' }}">
                                {{ $gv->TrangThai == 1 ? 'Hoạt động' : 'Bị khóa' }}
                            </span>
                        </td>
                        <td>
                            @if($gv->TrangThai == 1)
                            <a class="btn btn-warning btn-sm"
                                href="{{ route('admin.giang-vien.form-sua', ['id' => $gv->MaNguoiDung]) }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#modalXacNhanXoa" data-id="{{ $gv->MaNguoiDung }}"
                                data-ten="{{ $gv->HoTen }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            @else
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalXacNhanKhoiPhuc" data-id="{{ $gv->MaNguoiDung }}"
                                data-ten="{{ $gv->HoTen }}">
                                <i class="fas fa-undo-alt"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <x-phan-trang :data="$danhSachGiangVien" label="giảng viên" />
        </div>
    </div>
</div>

<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="modalXacNhanXoa" tabindex="-1" aria-labelledby="xacNhanXoaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xacNhanXoaLabel">Xác nhận xóa</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa giảng viên <strong id="tenGiangVien"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formXoaGiangVien" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác nhận Khôi phục -->
<div class="modal fade" id="modalXacNhanKhoiPhuc" tabindex="-1" aria-labelledby="xacNhanKhoiPhucLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xacNhanKhoiPhucLabel">Xác nhận khôi phục</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn khôi phục giảng viên <strong id="tenGiangVien"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formKhoiPhucGiangVien" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Khôi phục</button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalXoa = document.getElementById('modalXacNhanXoa');
        if (modalXoa) {
            modalXoa.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const maNguoiDung = button.getAttribute('data-id');
                const tenGiangVien = button.getAttribute('data-ten');
                modalXoa.querySelector('#tenGiangVien').textContent = `"${tenGiangVien}"`;
                const form = modalXoa.querySelector('#formXoaGiangVien');
                form.action = `/admin/giang-vien/xoa/${maNguoiDung}`;
            });
        }

        const modalKhoiPhuc = document.getElementById('modalXacNhanKhoiPhuc');
        if (modalKhoiPhuc) {
            modalKhoiPhuc.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const maNguoiDung = button.getAttribute('data-id');
                const tenGiangVien = button.getAttribute('data-ten');
                modalKhoiPhuc.querySelector('#tenGiangVien').textContent = `"${tenGiangVien}"`;
                const form = modalKhoiPhuc.querySelector('#formKhoiPhucGiangVien');
                form.action = `/admin/giang-vien/khoi-phuc/${maNguoiDung}`;
            });
        }
    });

</script>

@endsection


@section('styles')
<style>
    .img-card-person {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 50px;
        height: 50px;
        border-radius: .357rem;
        object-fit: cover;
    }
</style>
@endsection