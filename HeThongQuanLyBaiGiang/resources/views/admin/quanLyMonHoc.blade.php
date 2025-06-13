@extends('layouts.adminLayout')

@section('content')
<div class="container">
    <h2 class="mb-4">Quản lý Môn học</h2>

    <!-- Tìm kiếm và lọc môn học -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Tìm kiếm & Lọc Môn học</span>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemMonHoc">Thêm Môn học</button>
        </div>
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-5">
                    <input type="text" class="form-control" placeholder="Nhập tên môn học cần tìm">
                </div>
                <div class="col-md-5">
                    <select class="form-select" id="filterKhoa" name="filterKhoa">
                        <option value="">-- Lọc theo Khoa --</option>
                        <option value="1">Công nghệ thông tin</option>
                        <option value="2">Kinh tế</option>
                        <option value="3">Ngoại ngữ</option>
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
                    @for ($i = 1; $i <= 10; $i++)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>Môn học {{ $i }}</td>
                        <td>Khoa {{ ($i % 3) + 1 }}</td>
                        <td>Mô tả môn học {{ $i }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalSuaMonHoc">Sửa</button>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalXacNhanXoa">Xóa</button>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Phân trang -->
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Trước</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Sau</a>
                    </li>
                </ul>
            </nav>
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
                <form>
                    <div class="mb-3">
                        <label for="tenMonHoc" class="form-label">Tên Môn học</label>
                        <input type="text" class="form-control" id="tenMonHoc" name="tenMonHoc" required>
                    </div>
                    <div class="mb-3">
                        <label for="khoa" class="form-label">Khoa</label>
                        <select class="form-select" id="khoa" name="khoa" required>
                            <option value="">-- Chọn Khoa --</option>
                            <option value="1">Công nghệ thông tin</option>
                            <option value="2">Kinh tế</option>
                            <option value="3">Ngoại ngữ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="moTa" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="moTa" name="moTa" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Môn học (mẫu cứng) -->
<div class="modal fade" id="modalSuaMonHoc" tabindex="-1" aria-labelledby="suaMonHocLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suaMonHocLabel">Sửa Môn học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="tenMonHocSua" class="form-label">Tên Môn học</label>
                        <input type="text" class="form-control" id="tenMonHocSua" name="tenMonHocSua" value="Môn học mẫu">
                    </div>
                    <div class="mb-3">
                        <label for="khoaSua" class="form-label">Khoa</label>
                        <select class="form-select" id="khoaSua" name="khoaSua">
                            <option value="1">Công nghệ thông tin</option>
                            <option value="2">Kinh tế</option>
                            <option value="3">Ngoại ngữ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="moTaSua" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="moTaSua" name="moTaSua" rows="3">Mô tả mẫu</textarea>
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
                <button type="button" class="btn btn-danger">Xóa</button>
            </div>
        </div>
    </div>
</div>
@endsection
