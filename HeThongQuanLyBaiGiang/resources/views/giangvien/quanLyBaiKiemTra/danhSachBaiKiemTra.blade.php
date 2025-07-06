@extends('layouts.teacherLayout')

@section('title', 'Giảng viên - Danh sách bài kiểm tra')
@section('tenTrang', 'Danh sách bài kiểm tra')

@section('styles')
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .action-buttons .btn {
        margin-right: 5px;
    }

    .badge {
        font-size: 0.9em;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Quản lý Bài kiểm tra</h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('giangvien.bai-kiem-tra.form-them') }}" class="btn btn-success me-2">
                        <i class="fas fa-plus me-1"></i> Thêm mới
                    </a>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importBaiKiemTra"
                        title="Import bài kiểm tra bằng file Excel">
                        <i class="fas fa-file-import"></i> Import
                    </button>
                    <a class="btn btn-warning" href="{{ asset('./BaiKiemTra/Template_Import_BaiKiemTra.xlsx') }}"
                        download title="Tải mẫu Excel">
                        <i class="fas fa-file-excel"></i> Mẫu Excel
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Form tìm kiếm và lọc -->
            <div class="row justify-content-between mb-3">
                <div class="col-md-auto">
                    <form method="GET" action="{{ route('giangvien.bai-kiem-tra.danh-sach') }}"
                        class="d-flex align-items-center">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="filterClass" value="{{ request('filterClass') }}">
                        <label for="per_page" class="me-2">Hiện</label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm"
                            onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10)==10?'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page')==25?'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page')==50?'selected' : '' }}>50</option>
                        </select>
                        <span class="ms-2">mục</span>
                    </form>
                </div>
                <div class="col-md-auto">
                    <form method="GET" action="{{ route('giangvien.bai-kiem-tra.danh-sach') }}"
                        class="d-flex align-items-center">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                        <div class="input-group me-2">
                            <input type="search" name="search" class="form-control" placeholder="Tìm kiếm..."
                                value="{{ request('search') }}" title="Tìm kiếm theo tên bài kiểm tra, mô tả, tên lớp học phần">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                        <select name="filterClass" id="filterClass" class="form-select form-select-sm"
                            onchange="this.form.submit()">
                            <option value="">Tất cả lớp học phần</option>
                            @foreach ($lopHocPhan as $lop)
                            <option value="{{ $lop->MaLopHocPhan }}" {{ request('filterClass')==$lop->MaLopHocPhan ?
                                'selected' : '' }}>
                                {{ $lop->TenLopHocPhan }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <!-- Bảng danh sách bài kiểm tra -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 5%;">STT</th>
                            <th style="width: 25%;">Tên bài kiểm tra</th>
                            <th style="width: 20%;">Lớp học phần</th>
                            <th style="width: 15%;">Thời gian bắt đầu</th>
                            <th style="width: 15%;">Thời gian kết thúc</th>
                            <th style="width: 10%;">Thời gian làm bài</th>
                            <th style="width: 10%;">Cho phép xem kết quả</th>
                            <th style="width: 10%;">Trạng thái</th>
                            <th style="width: 10%;">Số lượng SV đã làm</th>
                            <th style="width: 20%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($baiKiemTras as $index => $baiKiemTra)
                        <tr>
                            <td class="text-center">{{ $baiKiemTras->firstItem() + $index }}</td>
                            <td>{{ $baiKiemTra->TenBaiKiemTra }}</td>
                            <td>{{ $baiKiemTra->TenLopHocPhan }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($baiKiemTra->ThoiGianBatDau)->format('H:i:s
                                d/m/Y') }}</td>
                            <td class="text-center">{{
                                \Carbon\Carbon::parse($baiKiemTra->ThoiGianKetThuc)->format('H:i:s d/m/Y') }}</td>
                            <td class="text-center">{{ $baiKiemTra->ThoiGianLamBai }} phút</td>
                            <td class="text-center">
                                <span class="badge {{ $baiKiemTra->ChoPhepXemKetQua ? 'bg-success' : 'bg-danger' }}">
                                    {{ $baiKiemTra->ChoPhepXemKetQua ? 'Có' : 'Không' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $baiKiemTra->TrangThai?'bg-success' : 'bg-danger' }}">
                                    {{ $baiKiemTra->TrangThai?'Hiện' : 'Ẩn' }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{ DB::table('ket_qua_bai_kiem_tra')
                                ->where('MaBaiKiemTra', $baiKiemTra->MaBaiKiemTra)
                                ->distinct('MaSinhVien')
                                ->count('MaSinhVien') }}
                            </td>
                            <td class="text-center action-buttons">
                                <div class="btn-group">
                                    <a href="{{ route('giangvien.bai-kiem-tra.chi-tiet', $baiKiemTra->MaBaiKiemTra) }}"
                                        class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('giangvien.bai-kiem-tra.form-sua', $baiKiemTra->MaBaiKiemTra) }}"
                                        class="btn btn-sm btn-primary" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $baiKiemTra->MaBaiKiemTra }}" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <a href="{{ route('giangvien.bai-kiem-tra.xuat-ket-qua', ['id' => $baiKiemTra->MaBaiKiemTra]) }}"
                                        class="btn btn-sm btn-primary" title="Xuất kết quả bài làm của sinh viên">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('giangvien.bai-kiem-tra.xuat-bai-kiem-tra', ['id' => $baiKiemTra->MaBaiKiemTra]) }}"
                                        class="btn btn-sm btn-secondary" title="Xuất bài kiểm tra">
                                        <i class="fas fa-file-export"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#duplicateModal{{ $baiKiemTra->MaBaiKiemTra }}"
                                        title="Nhân bản">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Xóa -->
                        <div class="modal fade" id="deleteModal{{ $baiKiemTra->MaBaiKiemTra }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel{{ $baiKiemTra->MaBaiKiemTra }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $baiKiemTra->MaBaiKiemTra }}">Xác
                                            nhận xóa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn có chắc chắn muốn xóa bài kiểm tra <strong>{{ $baiKiemTra->TenBaiKiemTra
                                            }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Hủy</button>
                                        <form
                                            action="{{ route('giangvien.bai-kiem-tra.xoa', $baiKiemTra->MaBaiKiemTra) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Xóa</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Nhân bản -->
                        <div class="modal fade" id="duplicateModal{{ $baiKiemTra->MaBaiKiemTra }}" tabindex="-1"
                            aria-labelledby="duplicateModalLabel{{ $baiKiemTra->MaBaiKiemTra }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="duplicateModalLabel{{ $baiKiemTra->MaBaiKiemTra }}">
                                            Nhân bản bài kiểm tra: <strong>{{ $baiKiemTra->TenBaiKiemTra }}</strong>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('giangvien.bai-kiem-tra.nhan-ban') }}" method="POST">
                                            @csrf
                                            @method('')
                                            <input type="hidden" name="MaBaiKiemTra"
                                                value="{{ $baiKiemTra->MaBaiKiemTra }}">
                                            <div class="mb-3">
                                                <label for="duplicateClass{{ $baiKiemTra->MaBaiKiemTra }}"
                                                    class="form-label">Chọn lớp học phần đích <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select @error('MaLopHocPhan') is-invalid @enderror"
                                                    id="duplicateClass{{ $baiKiemTra->MaBaiKiemTra }}"
                                                    name="MaLopHocPhan" required>
                                                    <option value="">Chọn lớp học phần</option>
                                                    @foreach ($lopHocPhan as $lop)
                                                    <option value="{{ $lop->MaLopHocPhan }}">{{ $lop->TenLopHocPhan }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('MaLopHocPhan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="status{{ $baiKiemTra->MaBaiKiemTra }}"
                                                    class="form-label">Trạng thái</label>
                                                <select class="form-select" name="TrangThai"
                                                    id="status{{ $baiKiemTra->MaBaiKiemTra }}">
                                                    <option value="1">Hiện</option>
                                                    <option value="0" selected>Ẩn</option>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-primary">Nhân bản</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Không có bài kiểm tra nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <x-phan-trang :data="$baiKiemTras" label="sự kiện" />
        </div>
    </div>
</div>

<!-- Modal import bài kiểm tra bằng file excel -->
<div class="modal fade" id="importBaiKiemTra" tabindex="-1" aria-labelledby="importBaiKiemTraModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('giangvien.bai-kiem-tra.import') }}" method="POST" enctype="multipart/form-data"
            class="modal-content p-4">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="importBaiKiemTraModalLabel">Thêm bài kiểm tra bằng file Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
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


@endsection

@section('scripts')
<script>
    // Xử lý tìm kiếm và lọc - chỉ áp dụng cho các form trong phần header của card-body
    document.querySelectorAll('.card-body > .row.justify-content-between form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const search = form.querySelector('input[name="search"]') ? .value.trim();
            const filterClass = form.querySelector('select[name="filterClass"]') ? .value;
            if (!search && !filterClass && form.querySelector('select[name="per_page"]') === null) {
                e.preventDefault();
                window.location.href = "{{ route('giangvien.bai-kiem-tra.danh-sach') }}";
            }
        });
    });

    // Xử lý modal nhân bản
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        button.addEventListener('click', function () {
            const modalId = this.getAttribute('data-bs-target');
            const modal = document.querySelector(modalId);
            modal.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
        });
    });

</script>
@endsection