@extends('layouts.teacherLayout')

@section('title', 'Quản lý Lớp học phần')

@section('styles')
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .table thead th {
        background-color: #e9ecef;
        border-bottom: 2px solid #dee2e6;
    }
    .pagination .page-link {
        color: #007bff;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }
    .table-responsive {
        min-height: 200px;
    }
    .action-buttons .btn {
        margin-right: 5px;
    }
    .status-select {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .status-option-hien {
        background-color: #d4edda;
        color: #155724;
    }
    .status-option-an {
        background-color: #e9ecef;
        color: #495057;
    }
    .form-check-input.custom-hocphan:checked {
        background-color: #198754;
        border-color: #198754;
    }
    .form-check-input.custom-hocphan {
        background-color: #e7f1ff;
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,.15);
    }
    .hocphan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-top: 8px;
    }
    .hocphan-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(13,110,253,0.08);
        padding: 20px 22px 14px 22px;
        display: flex;
        align-items: flex-start;
        transition: box-shadow 0.2s, background 0.2s;
        border: none;
        position: relative;
    }
    .hocphan-card:hover {
        background: #f0f8ff;
        box-shadow: 0 6px 24px rgba(25,135,84,0.13);
    }
    .hocphan-card .form-check-input.custom-hocphan {
        width: 1.4em;
        height: 1.4em;
        margin-right: 16px;
        margin-top: 0.2em;
        accent-color: #0d6efd;
    }
    .hocphan-card .icon-book {
        color: #0d6efd;
        font-size: 1.3em;
        margin-right: 10px;
        margin-top: 2px;
    }
    .hocphan-card .form-check-label {
        font-weight: 600;
        font-size: 1.13rem;
        display: flex;
        align-items: center;
    }
    .hocphan-card .small.text-muted {
        margin-left: 2.2em;
        font-size: 0.97em;
    }
    @media (max-width: 600px) {
        .hocphan-grid { grid-template-columns: 1fr; }
        .hocphan-card { padding: 16px 10px 10px 10px; }
    }
</style>
@endsection
    
@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Quản lý Lớp học phần</h5>
            <button type="button" class="btn btn-success" onclick="openLopHocPhanModal()">
                <i class="fas fa-plus me-1"></i> Thêm mới
            </button>
        </div>
        <div class="card-body">
            <div class="row justify-content-between mb-3">
                <div class="col-md-auto">
                    <form method="GET" action="{{ route('giangvien.lophocphan.danhsach') }}" class="d-flex align-items-center">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <label for="per_page" class="me-2">Hiện</label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                        <span class="ms-2">mục</span>
                    </form>
                </div>
                <div class="col-md-auto">
                    <form method="GET" action="{{ route('giangvien.lophocphan.danhsach') }}">
                        <div class="input-group">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                            <input type="search" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 10%;">Mã lớp</th>
                            <th style="width: 25%;">Tên lớp học phần</th>
                            <th style="width: 20%;">Học phần</th>
                            <th style="width: 20%;">Mô tả</th>
                            <th style="width: 15%;">Thời gian tạo</th>
                            <th style="width: 10%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lopHocPhans as $lop)
                        <tr>
                            <td class="text-center">{{ $lop->MaLopHocPhan }}</td>
                            <td>{{ $lop->TenLopHocPhan }}</td>
                            <td>{{ $lop->hocPhan->TenHocPhan ?? '' }}</td>
                            <td>{{ $lop->MoTa }}</td>
                            <td class="text-center">{{ $lop->created_at ? $lop->created_at->format('d/m/Y') : '' }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button onclick="viewLopHocPhan({{ $lop->MaLopHocPhan }})" class="btn btn-sm btn-info" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                                    <button onclick="openLopHocPhanModal({{ $lop->MaLopHocPhan }})" class="btn btn-sm btn-primary" title="Sửa"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteLopHocPhan({{ $lop->MaLopHocPhan }}, '{{ $lop->TenLopHocPhan }}')" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Không có dữ liệu lớp học phần nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $lopHocPhans->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa Lớp học phần -->
<div class="modal fade" id="modalLopHocPhan" tabindex="-1" aria-labelledby="modalLopHocPhanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formLopHocPhan" method="POST">
                @csrf
                <div id="formMethod"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLopHocPhanLabel">Lớp học phần</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrors" class="alert alert-danger" style="display: none;"></div>
                    <div class="mb-3">
                        <label for="TenLopHocPhan" class="form-label">Tên lớp học phần <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="TenLopHocPhan" name="TenLopHocPhan" required>
                    </div>
                    <div class="mb-3">
                        <label for="MoTa" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="MoTa" name="MoTa" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn học phần <span class="text-danger">*</span></label>
                        <select class="form-select" name="MaHocPhan" id="MaHocPhan" required>
                            <option value="">-- Chọn học phần --</option>
                            @foreach($hocPhans as $hp)
                                <option value="{{ $hp->MaHocPhan }}">{{ $hp->TenHocPhan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xem chi tiết Lớp học phần -->
<div class="modal fade" id="modalXemLopHocPhan" tabindex="-1" aria-labelledby="xemLopHocPhanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xemLopHocPhanLabel">Chi tiết Lớp học phần</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="lopHocPhanDetail"></div>
        </div>
    </div>
</div>
<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="modalXacNhanXoaLopHocPhan" tabindex="-1" aria-labelledby="xacNhanXoaLopHocPhanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xacNhanXoaLopHocPhanLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa lớp học phần: <strong id="tenLopHocPhanXoa"></strong>?</p>
                <p class="text-danger">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formXoaLopHocPhan" method="POST" style="display: inline;">
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
    const lopHocPhanModal = new bootstrap.Modal(document.getElementById('modalLopHocPhan'));
    const formLopHocPhan = document.getElementById('formLopHocPhan');
    const modalLabel = document.getElementById('modalLopHocPhanLabel');
    const formMethod = document.getElementById('formMethod');

    function openLopHocPhanModal(id = null) {
        formLopHocPhan.reset();
        formLopHocPhan.querySelector('.is-invalid')?.classList.remove('is-invalid');
        document.getElementById('modalErrors').style.display = 'none';
        document.getElementById('modalErrors').innerHTML = '';
        // Bỏ check các checkbox
        formLopHocPhan.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);

        if (id) {
            // ---- CHẾ ĐỘ SỬA ----
            modalLabel.textContent = 'Sửa Lớp học phần';
            formMethod.innerHTML = `@method('PUT')`;
            formLopHocPhan.action = `{{ url('giang-vien/lop-hoc-phan') }}/${id}`;
            fetch(`{{ url('giang-vien/lop-hoc-phan') }}/${id}/chinh-sua`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('TenLopHocPhan').value = data.TenLopHocPhan;
                    document.getElementById('MoTa').value = data.MoTa || '';
                    document.getElementById('MaHocPhan').value = data.MaHocPhan;
                    lopHocPhanModal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi tải dữ liệu.');
                });
        } else {
            // ---- CHẾ ĐỘ THÊM ----
            modalLabel.textContent = 'Thêm Lớp học phần mới';
            formMethod.innerHTML = '';
            formLopHocPhan.action = `{{ route('giangvien.lophocphan.them-moi') }}`;
            lopHocPhanModal.show();
        }
    }

    function viewLopHocPhan(id) {
        let url = `{{ url('giang-vien/lop-hoc-phan') }}/${id}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const detailHtml = `
                    <dl class="row">
                        <dt class="col-sm-4">Mã lớp học phần:</dt><dd class="col-sm-8">${data.MaLopHocPhan}</dd>
                        <dt class="col-sm-4">Tên lớp học phần:</dt><dd class="col-sm-8">${data.TenLopHocPhan}</dd>
                        <dt class="col-sm-4">Học phần:</dt><dd class="col-sm-8">${data.hoc_phan ? data.hoc_phan.TenHocPhan : ''}</dd>
                        <dt class="col-sm-4">Mô tả:</dt><dd class="col-sm-8">${data.MoTa || ''}</dd>
                        <dt class="col-sm-4">Ngày tạo:</dt><dd class="col-sm-8">${new Date(data.created_at).toLocaleDateString('vi-VN')}</dd>
                    </dl>
                `;
                document.getElementById('lopHocPhanDetail').innerHTML = detailHtml;
                new bootstrap.Modal(document.getElementById('modalXemLopHocPhan')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải dữ liệu lớp học phần!');
            });
    }

    function deleteLopHocPhan(id, tenLopHocPhan) {
        document.getElementById('tenLopHocPhanXoa').textContent = tenLopHocPhan;
        document.getElementById('formXoaLopHocPhan').action = `{{ url('giang-vien/lop-hoc-phan') }}/${id}`;
        new bootstrap.Modal(document.getElementById('modalXacNhanXoaLopHocPhan')).show();
    }

    // Hiển thị lại modal với lỗi nếu có
    @if($errors->any())
        openLopHocPhanModal({{ old('MaLopHocPhan') ? old('MaLopHocPhan') : 'null' }});
        let errors = {!! json_encode($errors->getMessages()) !!};
        let errorHtml = '<ul>';
        for (let field in errors) {
            errors[field].forEach(error => {
                errorHtml += `<li>${error}</li>`;
            });
        }
        errorHtml += '</ul>';
        document.getElementById('modalErrors').innerHTML = errorHtml;
        document.getElementById('modalErrors').style.display = 'block';
        // Đánh dấu các trường bị lỗi
        Object.keys(errors).forEach(function(field) {
            let input = document.getElementById(field.charAt(0).toUpperCase() + field.slice(1));
            if (input) {
                input.classList.add('is-invalid');
            }
        });
    @endif
</script>
@endsection