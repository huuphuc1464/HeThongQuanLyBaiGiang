@extends('layouts.teacherLayout')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh Sách Học Phần</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalThemHocPhan">
                <i class="fas fa-plus"></i> Thêm học phần
            </button>
            <form class="d-flex" method="GET" action="{{ route('giangvien.hocphan.danh-sach') }}" style="width: 300px;">
                <input class="form-control me-2" type="search" name="search" placeholder="Tìm kiếm học phần..." aria-label="Search" value="{{ $search ?? '' }}">
                <button class="btn btn-outline-primary" type="submit">Tìm</button>
            </form>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-2 d-flex align-items-center">
                    <label class="me-2">Hiện</label>
                    <select class="form-select" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                    <span class="ms-2">học phần</span>
                </div>
            </div>

            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã học phần</th>
                        <th>Tên học phần</th>
                        <th>Hình ảnh</th>
                        <th>Tên môn học</th>
                        <th>Người tạo</th>
                        <th>Thời gian tạo</th>
                        <th>Danh sách bài giảng</th>
                        <th>Tính năng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSachHocPhan as $hocPhan)
                    <tr>
                        <td>{{ $hocPhan->MaHocPhan }}</td>
                        <td>{{ $hocPhan->TenHocPhan }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $hocPhan->AnhHocPhan ? asset('storage/' . $hocPhan->AnhHocPhan) : asset('img/default-image.jpg') }}" 
                                     alt="{{ $hocPhan->TenHocPhan }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <strong>{{ $hocPhan->TenHocPhan }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>{{ $hocPhan->monHoc->TenMonHoc ?? 'N/A' }}</td>
                        <td>{{ $hocPhan->nguoiTao->HoTen ?? 'N/A' }}</td>
                        <td>{{ $hocPhan->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="/giang-vien/hoc-phan/{{$hocPhan->MaHocPhan}}/bai-giang" class="btn btn-sm btn-warning" title="Danh sách bài giảng">
                                <i class="fas fa-chalkboard-teacher me-1"></i> Bài giảng ({{ $hocPhan->baiGiang->count() }})
                            </a>
                        </td>
                        <td>
                            <button onclick="editHocPhan({{ $hocPhan->MaHocPhan }})" class="btn btn-sm btn-primary me-1" title="Sửa"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteHocPhan({{ $hocPhan->MaHocPhan }}, '{{ $hocPhan->TenHocPhan }}')" class="btn btn-sm btn-danger me-1" title="Xóa"><i class="fas fa-trash"></i></button>
                            <button onclick="viewHocPhan({{ $hocPhan->MaHocPhan }})" class="btn btn-sm btn-info me-1" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Không có dữ liệu học phần nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center">
                {{ $danhSachHocPhan->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Học phần -->
<div class="modal fade" id="modalThemHocPhan" tabindex="-1" aria-labelledby="themHocPhanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="themHocPhanLabel">Thêm Học phần mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('giangvien.hocphan.them-moi') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tenHocPhan" class="form-label">Tên Học phần <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('TenHocPhan') is-invalid @enderror" 
                               id="tenHocPhan" name="TenHocPhan" value="{{ old('TenHocPhan') }}" required>
                        @error('TenHocPhan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="monHoc" class="form-label">Thuộc Môn học <span class="text-danger">*</span></label>
                        <select class="form-select @error('MaMonHoc') is-invalid @enderror" id="monHoc" name="MaMonHoc" required>
                            <option value="">-- Chọn Môn học --</option>
                            @foreach($danhSachMonHoc as $monHoc)
                                <option value="{{ $monHoc->MaMonHoc }}" {{ old('MaMonHoc') == $monHoc->MaMonHoc ? 'selected' : '' }}>
                                    {{ $monHoc->TenMonHoc }}
                                </option>
                            @endforeach
                        </select>
                        @error('MaMonHoc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="hinhAnh" class="form-label">Hình ảnh</label>
                        <input class="form-control @error('AnhHocPhan') is-invalid @enderror" type="file" 
                               id="hinhAnh" name="AnhHocPhan" accept="image/*">
                        @error('AnhHocPhan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="moTa" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('MoTa') is-invalid @enderror" 
                                  id="moTa" name="MoTa" rows="3">{{ old('MoTa') }}</textarea>
                        @error('MoTa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

<!-- Modal Sửa Học phần -->
<div class="modal fade" id="modalSuaHocPhan" tabindex="-1" aria-labelledby="suaHocPhanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suaHocPhanLabel">Sửa Học phần</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSuaHocPhan" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tenHocPhanSua" class="form-label">Tên Học phần <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tenHocPhanSua" name="TenHocPhan" required>
                    </div>
                    <div class="mb-3">
                        <label for="monHocSua" class="form-label">Thuộc Môn học <span class="text-danger">*</span></label>
                        <select class="form-select" id="monHocSua" name="MaMonHoc" required>
                            <option value="">-- Chọn Môn học --</option>
                            @foreach($danhSachMonHoc as $monHoc)
                                <option value="{{ $monHoc->MaMonHoc }}">{{ $monHoc->TenMonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hinhAnhSua" class="form-label">Hình ảnh</label>
                        <input class="form-control" type="file" id="hinhAnhSua" name="AnhHocPhan" accept="image/*">
                        <div id="currentImage" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label for="moTaSua" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="moTaSua" name="MoTa" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xem chi tiết Học phần -->
<div class="modal fade" id="modalXemHocPhan" tabindex="-1" aria-labelledby="xemHocPhanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xemHocPhanLabel">Chi tiết Học phần</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="hocPhanDetail">
                
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
function editHocPhan(id) {
    let url = "{{ route('giangvien.hocphan.chinh-sua', ['id' => ':id']) }}".replace(':id', id);
    fetch(url)
        .then(response => response.json())
        .then(data => {
            document.getElementById('tenHocPhanSua').value = data.TenHocPhan;
            document.getElementById('monHocSua').value = data.MaMonHoc;
            document.getElementById('moTaSua').value = data.MoTa || '';
            
            // Hiển thị hình ảnh hiện tại
            const currentImageDiv = document.getElementById('currentImage');
            if (data.AnhHocPhan) {
                currentImageDiv.innerHTML = `<img src="{{ asset('storage') }}/${data.AnhHocPhan}" class="img-thumbnail" style="max-width: 100px;">`;
            } else {
                currentImageDiv.innerHTML = '<p class="text-muted">Không có hình ảnh</p>';
            }
            
            let formActionUrl = "{{ route('giangvien.hocphan.cap-nhat', ['id' => ':id']) }}".replace(':id', id);
            document.getElementById('formSuaHocPhan').action = formActionUrl;
            new bootstrap.Modal(document.getElementById('modalSuaHocPhan')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải dữ liệu học phần!');
        });
}

function viewHocPhan(id) {
    let url = "{{ route('giangvien.hocphan.chi-tiet', ['id' => ':id']) }}".replace(':id', id);
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const imageUrl = data.AnhHocPhan ? `{{ asset('storage') }}/${data.AnhHocPhan}` : `{{ asset('img/default-image.jpg') }}`;
            const detailHtml = `
                <div class="row">
                    <div class="col-md-4">
                        <img src="${imageUrl}" class="img-fluid rounded">
                    </div>
                    <div class="col-md-8">
                        <h6>Thông tin cơ bản:</h6>
                        <p><strong>Mã học phần:</strong> ${data.MaHocPhan}</p>
                        <p><strong>Tên học phần:</strong> ${data.TenHocPhan}</p>
                        <p><strong>Môn học:</strong> ${data.mon_hoc ? data.mon_hoc.TenMonHoc : 'N/A'}</p>
                        <p><strong>Mô tả:</strong> ${data.MoTa || 'Không có mô tả'}</p>
                        <p><strong>Người tạo:</strong> ${data.nguoi_tao ? data.nguoi_tao.HoTen : 'N/A'}</p>
                        <p><strong>Ngày tạo:</strong> ${new Date(data.created_at).toLocaleDateString('vi-VN')}</p>
                        <p><strong>Số bài giảng:</strong> ${data.bai_giang ? data.bai_giang.length : 0}</p>
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
    let formActionUrl = "{{ route('giangvien.hocphan.xoa', ['id' => ':id']) }}".replace(':id', id);
    document.getElementById('formXoaHocPhan').action = formActionUrl;
    new bootstrap.Modal(document.getElementById('modalXacNhanXoa')).show();
}
</script>
@endsection
