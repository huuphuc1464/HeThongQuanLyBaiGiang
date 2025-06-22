@extends('layouts.teacherLayout')
@section('title','Giảng viên - Danh sách bài kiểm tra')
@section('tenTrang', 'Danh sách bài kiểm tra')
@section('content')
<div class="col-md-12">
    <div class="tile">
        <h3 class="tile-title">Danh sách bài kiểm tra</h3>
        <div class="tile-body">
            <div class="row pb-2 align-items-center">
                <div class="col-12 col-md-auto d-flex flex-wrap gap-2 align-items-center mb-2 mb-md-0">
                    <a class="btn btn-primary btn-sm" href="#" title="Thêm bài kiểm tra">
                        <i class="fas fa-plus"></i> Thêm
                    </a>
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#importBaiKiemTra" title="Import bài kiểm tra bằng file Excel">
                        <i class="fas fa-file-import"></i> Import
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#nhanBanBaiKiemTra" title="Nhân bản bài kiểm tra">
                        <i class="fas fa-clone"></i> Nhân bản
                    </button>
                    <a class="btn btn-success btn-sm" href="{{ asset('./BaiKiemTra/Template_Import_BaiKiemTra.xlsx') }}" download title="Tải mẫu Excel">
                        <i class="fas fa-file-excel"></i> Mẫu Excel
                    </a>
                </div>
                <div class="col-12 col-md d-flex justify-content-md-end">
                    <form method="GET" class="d-flex align-items-center gap-2 border rounded p-2 flex-nowrap" style="min-width: 250px;" title="Tìm kiếm bài kiểm tra">
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
                        <th>Mã bài kiểm tra</th>
                        <th>Tên bài kiểm tra</th>
                        <th>Tên lớp học phần</th>
                        <th>Thời gian bắt đầu</th>
                        <th>Thời gian kết thúc</th>
                        <th>Số lượng sinh viên đã làm</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Tính năng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#1</td>
                        <td>Bài kiểm tra 1</td>
                        <td>Lớp học phần laravel</td>
                        <td>{{ \Carbon\Carbon::parse(now())->format('H:i:s d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse(now())->format('H:i:s d/m/Y') }}</td>
                        <td>10/15</td>
                        <td>Đây là mô tả bài kiểm tra</td>
                        <td>
                            <span class="badge bg-success">Hiện</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-primary" title="Sửa"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                <button class="btn btn-sm btn-success" title="Xuất kết quả bài kiểm tra"><i class="fas fa-file-download"></i></button>
                                <button class="btn btn-sm btn-warning" title="Xuất bài kiểm tra"><i class="fas fa-file-export"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal import bài kiểm tra bằng file excel -->
<div class="modal fade" id="importBaiKiemTra" tabindex="-1" aria-labelledby="importBaiKiemTraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="importBaiKiemTraModalLabel">Thêm bài kiểm tra bằng file Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('giangvien.bai-kiem-tra.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <label for="lop_hoc_phan" class="fw-semibold">Chọn lớp học phần:</label>
                    <select name="MaLopHocPhan" class="form-select my-3" id="lop_hoc_phan" required>
                        <option value="" disabled selected>-- Chọn lớp học phần --</option>
                        @foreach ($lopHocPhan as $lop)
                        <option value="{{ $lop->MaLopHocPhan }}">
                            {{ $lop->TenLopHocPhan }}
                        </option>
                        @endforeach
                    </select>
                    <label for="excelFile" class="fw-semibold">Chọn file Excel chứa dữ liệu bài kiểm tra:</label>
                    <small class="text-muted d-block pt-1 pb-2">Chỉ chấp nhận file Excel theo mẫu (.xlsx)</small>
                    <input type="file" name="file" accept=".xlsx" class="form-control mb-3" required>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Thêm</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal nhân bản bài kiểm tra -->
<div class="modal fade" id="nhanBanBaiKiemTra" tabindex="-1" aria-labelledby="nhanBanBaiKiemTraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="nhanBanBaiKiemTraModalLabel">Nhân bản bài kiểm tra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <label for="bai_kiem_tra" class="fw-semibold">Chọn bài kiểm tả muốn nhân bản:</label>
                    <select name="MaBaiKiemTra" class="form-select my-3" id="bai_kiem_tra" required>
                        <option value="" disabled selected>-- Chọn bài kiểm tra --</option>
                        @foreach ($lopHocPhan as $lop)
                        <option value="{{ $lop->MaLopHocPhan }}">
                            {{ $lop->TenLopHocPhan }}
                        </option>
                        @endforeach
                    </select>
                    <label for="lop_hoc_phan" class="fw-semibold">Chọn lớp học phần:</label>
                    <select name="MaLopHocPhan" class="form-select my-3" id="lop_hoc_phan" required>
                        <option value="" disabled selected>-- Chọn lớp học phần --</option>
                        @foreach ($lopHocPhan as $lop)
                        <option value="{{ $lop->MaLopHocPhan }}">
                            {{ $lop->TenLopHocPhan }}
                        </option>
                        @endforeach
                    </select>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Thêm</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('styles')

@endsection

@section('scripts')

@endsection
