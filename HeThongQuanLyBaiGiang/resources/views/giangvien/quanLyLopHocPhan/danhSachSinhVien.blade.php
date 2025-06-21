@extends('layouts.teacherLayout')
@section('title','Giảng viên - Danh sách sinh viên')
@section('tenTrang', $lopHocPhan->TenLopHocPhan . ' / Danh sách sinh viên')

@section('content')
<div class="col-md-12">
    <div class="tile">
        <h3 class="tile-title">Danh sách sinh viên</h3>
        <div class="tile-body">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a class="btn btn-success btn-sm" href="" title="Thêm sự kiện Zoom">
                    <i class="fas fa-plus"></i> Thêm sinh viên vào lớp
                </a>
                <a class="btn btn-primary btn-sm" href="" title="Nhập Excel thêm sinh viên vào lớp học phần">
                    <i class="fas fa-file-excel"></i> Nhập excel
                </a>
                <a class="btn btn-warning btn-sm" href="{{ asset('./LopHocPhan/Template_Import_Student.xlsx') }}" download title="Tải mẫu Excel để thêm sinh viên vào lớp học phần">
                    <i class="fas fa-file-download"></i> Tải mẫu excel
                </a>
                <form method="GET" class="d-flex align-items-center gap-2 border rounded p-2 ms-auto flex-nowrap" style="min-width: 250px;" title="Tìm kiếm sinh viên theo tên, mssv, email">
                    <label for="search" class="fw-bold mb-0">Tìm kiếm:</label>
                    <input type="text" name="search" id="search" class="form-control form-control-sm w-auto" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="row element-button table-responsive mt-2">
            <table class="table table-hover table-bordered" id="zoomTable">
                <thead class="table-secondary">
                    <tr>
                        <th>Mã danh sách</th>
                        <th>Họ tên sinh viên</th>
                        <th>Hình ảnh</th>
                        <th>MSSV</th>
                        <th>Email sinh viên</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>Trạng thái</th>
                        <th>Tính năng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sinhViens as $sinhVien)
                    <tr>
                        <td>#{{ $sinhVien->MaDanhSachLop }}</td>
                        <td>{{ $sinhVien->HoTen  }}</td>
                        <td class="text-center">
                            <img src="{{ asset($sinhVien->AnhDaiDien ?? 'AnhDaiDien/default-avatar.png') }}" alt="Avatar" width="40" height="40" class="rounded-circle"></td>
                        <td>{{ $sinhVien->MSSV }}</td>
                        <td>{{ $sinhVien->Email }}</td>
                        <td>{{ \Carbon\Carbon::parse($sinhVien->NgaySinh)->format('d/m/Y') }}</td>
                        <td>{{ $sinhVien->GioiTinh }}</td>
                        <td>
                            @if ($sinhVien->TrangThai == 1)
                            <span class="badge bg-success">Đã xác nhận</span>
                            @else
                            <span class="badge bg-danger">Chưa xác nhận</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalXacNhanXoa" data-id="{{ $sinhVien->MaDanhSachLop }}" data-ten="{{ $sinhVien->HoTen }}" data-lophocphan="{{ $lopHocPhan->MaLopHocPhan  }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">Không tìm thấy kết quả nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <x-phan-trang :data="$sinhViens" label="sinh viên" />
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
                Bạn có chắc chắn muốn xóa sinh viên <strong id="tenSinhVien"></strong> khỏi lớp học phần ? </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formXoaSinhVien" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>


</style>
@endsection

@section('scripts')
<script>
    const modalXoa = document.getElementById('modalXacNhanXoa');
    modalXoa.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const maDanhSachLop = button.getAttribute('data-id');
        const tenSinhVien = button.getAttribute('data-ten');
        const maLopHocPhan = button.getAttribute('data-lophocphan');
        modalXoa.querySelector('#tenSinhVien').textContent = `"${tenSinhVien}"`;

        const form = modalXoa.querySelector('#formXoaSinhVien');
        form.action = `/giang-vien/lop-hoc-phan/${maLopHocPhan}/sinh-vien/xoa/${maDanhSachLop}`;

    });

</script>
@endsection
