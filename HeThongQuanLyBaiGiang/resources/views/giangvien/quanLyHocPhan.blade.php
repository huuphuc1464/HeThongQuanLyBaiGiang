@extends('layouts.teacherLayout')
@section('title', 'Danh sách học phần')
@section('tenTrang', 'Danh sách học phần')
@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Quản lý Học phần</h5>
            <button type="button" class="btn btn-success" onclick="openHocPhanModal()">
                <i class="fas fa-plus me-1"></i> Thêm mới
            </button>
        </div>
        <div class="card-body">
            <div class="row justify-content-between mb-3">
                <div class="col-md-auto">
                    <form method="GET" action="{{ route('giangvien.hocphan.danh-sach') }}"
                        class="d-flex align-items-center">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <label for="per_page" class="me-2">Hiện</label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm"
                            onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10)==10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page')==25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page')==50 ? 'selected' : '' }}>50</option>
                        </select>
                        <span class="ms-2">mục</span>
                    </form>
                </div>
                <div class="col-md-auto">
                    <form method="GET" action="{{ route('giangvien.hocphan.danh-sach') }}">
                        <div class="input-group">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                            <input type="search" name="search" class="form-control" placeholder="Tìm kiếm..."
                                value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 10%;">Mã học phần</th>
                            <th style="width: 30%;">Tên học phần</th>
                            <th style="width: 20%;">Môn học</th>
                            <th style="width: 15%;">Bài giảng</th>
                            <th style="width: 15%;">Thời gian tạo</th>
                            <th style="width: 10%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachHocPhan as $hocPhan)
                        <tr>
                            <td class="text-center">{{ $hocPhan->MaHocPhan }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $hocPhan->AnhHocPhan ? asset('storage/' . $hocPhan->AnhHocPhan) : asset('img/login/default-avatar.png') }}"
                                        alt="{{ $hocPhan->TenHocPhan }}" class="rounded me-3"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">{{ $hocPhan->TenHocPhan }}</h6>
                                        <small class="text-muted">{{ Str::limit($hocPhan->MoTa, 70) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $hocPhan->monHoc->TenMonHoc ?? 'N/A' }}</td>
                            <td class="text-center">
                                <a href="/giang-vien/hoc-phan/{{$hocPhan->MaHocPhan}}/bai-giang"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-chalkboard-teacher me-1"></i> ({{ $hocPhan->bai_giang_count }})
                                </a>
                            </td>
                            <td class="text-center">{{ $hocPhan->created_at->format('H:i:s d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button onclick="viewHocPhan({{ $hocPhan->MaHocPhan }})" class="btn btn-sm btn-info"
                                        title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                                    <button onclick="openHocPhanModal({{ $hocPhan->MaHocPhan }})"
                                        class="btn btn-sm btn-primary" title="Sửa"><i class="fas fa-edit"></i></button>
                                    <button
                                        onclick="deleteHocPhan({{ $hocPhan->MaHocPhan }}, '{{ $hocPhan->TenHocPhan }}')"
                                        class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Không có dữ liệu học phần nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $danhSachHocPhan->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa Học phần -->
<div class="modal fade" id="modalHocPhan" tabindex="-1" aria-labelledby="modalHocPhanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formHocPhan" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="formMethod"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHocPhanLabel">Học phần</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrors" class="alert alert-danger" style="display: none;"></div>
                    <div class="mb-3">
                        <label for="TenHocPhan" class="form-label">Tên Học phần <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="TenHocPhan" name="TenHocPhan" required>
                    </div>
                    <div class="mb-3">
                        <label for="MaMonHoc" class="form-label">Thuộc Môn học <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="MaMonHoc" name="MaMonHoc" required>
                            <option value="">-- Chọn Môn học --</option>
                            @foreach($danhSachMonHoc as $monHoc)
                            <option value="{{ $monHoc->MaMonHoc }}">{{ $monHoc->TenMonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="AnhHocPhan" class="form-label">Hình ảnh</label>
                        <input class="form-control" type="file" id="AnhHocPhan" name="AnhHocPhan" accept="image/*">
                        <div id="currentImage" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label for="MoTa" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="MoTa" name="MoTa" rows="3"></textarea>
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

<!-- Các modal khác (Xem, Xóa) vẫn giữ nguyên -->
<!-- Modal Xem chi tiết Học phần -->
<div class="modal fade" id="modalXemHocPhan" tabindex="-1" aria-labelledby="xemHocPhanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xemHocPhanLabel">Chi tiết Học phần</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="hocPhanDetail"></div>
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
                <p>Bạn có chắc chắn muốn xóa học phần: <strong id="tenHocPhanXoa"></strong>?</p>
                <p class="text-danger">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formXoaHocPhan" method="POST" style="display: inline;">
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
    const hocPhanModal = new bootstrap.Modal(document.getElementById('modalHocPhan'));
    const formHocPhan = document.getElementById('formHocPhan');
    const modalLabel = document.getElementById('modalHocPhanLabel');
    const formMethod = document.getElementById('formMethod');

    function openHocPhanModal(id = null) {
        formHocPhan.reset();
        document.getElementById('currentImage').innerHTML = '';
        formHocPhan.querySelector('.is-invalid')?.classList.remove('is-invalid');
        document.getElementById('modalErrors').style.display = 'none';
        document.getElementById('modalErrors').innerHTML = '';

        if (id) {
            // ---- CHẾ ĐỘ SỬA ----
            modalLabel.textContent = 'Sửa Học phần';
            formMethod.innerHTML = `@method('PUT')`;
            formHocPhan.action = `{{ url('giang-vien/hoc-phan') }}/${id}`;

            fetch(`{{ url('giang-vien/hoc-phan') }}/${id}/chinh-sua`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('TenHocPhan').value = data.TenHocPhan;
                    document.getElementById('MaMonHoc').value = data.MaMonHoc;
                    document.getElementById('MoTa').value = data.MoTa || '';

                    if (data.AnhHocPhan) {
                        document.getElementById('currentImage').innerHTML = `<img src="{{ asset('storage') }}/${data.AnhHocPhan}" class="img-thumbnail" style="max-width: 100px;">`;
                    }
                    hocPhanModal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi tải dữ liệu.');
                });
        } else {
            // ---- CHẾ ĐỘ THÊM ----
            modalLabel.textContent = 'Thêm Học phần mới';
            formMethod.innerHTML = '';
            formHocPhan.action = `{{ route('giangvien.hocphan.them-moi') }}`;
            hocPhanModal.show();
        }
    }

    function viewHocPhan(id) {
        let url = `{{ url('giang-vien/hoc-phan') }}/${id}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const imageUrl = data.AnhHocPhan ? `{{ asset('storage') }}/${data.AnhHocPhan}` : `{{ asset('img/login/default-avatar.png') }}`;
                const detailHtml = `
                    <div class="row">
                        <div class="col-md-4">
                            <img src="${imageUrl}" class="img-fluid rounded mb-3">
                        </div>
                        <div class="col-md-8">
                            <dl class="row">
                                <dt class="col-sm-4">Mã học phần:</dt><dd class="col-sm-8">${data.MaHocPhan}</dd>
                                <dt class="col-sm-4">Tên học phần:</dt><dd class="col-sm-8">${data.TenHocPhan}</dd>
                                <dt class="col-sm-4">Môn học:</dt><dd class="col-sm-8">${data.mon_hoc ? data.mon_hoc.TenMonHoc : 'N/A'}</dd>
                                <dt class="col-sm-4">Người tạo:</dt><dd class="col-sm-8">${data.nguoi_tao ? data.nguoi_tao.HoTen : 'N/A'}</dd>
                                <dt class="col-sm-4">Ngày tạo:</dt><dd class="col-sm-8">${new Date(data.created_at).toLocaleDateString('vi-VN')}</dd>
                                <dt class="col-sm-4">Số bài giảng:</dt><dd class="col-sm-8">${data.bai_giang ? data.bai_giang.length : 0}</dd>
                            </dl>
                        </div>
                        <div class="col-12 mt-2">
                            <h6>Mô tả:</h6>
                            <p>${data.MoTa || 'Không có mô tả'}</p>
                        </div>
                    </div>
                `;
                document.getElementById('hocPhanDetail').innerHTML = detailHtml;
                new bootstrap.Modal(document.getElementById('modalXemHocPhan')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải dữ liệu học phần!');
            });
    }

    function deleteHocPhan(id, tenHocPhan) {
        document.getElementById('tenHocPhanXoa').textContent = tenHocPhan;
        document.getElementById('formXoaHocPhan').action = `{{ url('giang-vien/hoc-phan') }}/${id}`;
        new bootstrap.Modal(document.getElementById('modalXacNhanXoa')).show();
    }

    // Hiển thị lại modal với lỗi nếu có
    @if ($errors -> any())
        openHocPhanModal({{ old('MaHocPhan') ?old('MaHocPhan'): 'null' }});

    let errors = {!! json_encode($errors -> getMessages())!!};
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
    Object.keys(errors).forEach(function (field) {
        let input = document.getElementById(field.charAt(0).toUpperCase() + field.slice(1));
        if (input) {
            input.classList.add('is-invalid');
        }
    });
    @endif
</script>
@endsection