@extends('layouts.teacherLayout')
@section('title','Giảng viên - Danh sách sự kiện Zoom')
@section('tenTrang', 'Danh sách sự kiện Zoom')
@section('content')
<div class="col-md-12">
    <div class="tile">
        <h3 class="tile-title">Danh sách sự kiện Zoom</h3>
        <div class="tile-body">
            <div class="row pb-2 align-items-center">
                <div class="col-12 col-md-4 p-0 mb-2 mb-md-0">
                    <a class="btn btn-success btn-sm" href="{{ route('giangvien.su-kien-zoom.form-them') }}" title="Thêm sự kiện Zoom">
                        <i class="fas fa-plus"></i> Thêm sự kiện Zoom
                    </a>
                </div>
                <div class="col-12 col-md-8 p-0 d-flex justify-content-end">
                    <form method="GET" class="d-flex align-items-center gap-2 border rounded p-2 ms-auto flex-nowrap" style="min-width: 250px;" title="Tìm kiếm tên sự kiện, tên lớp học phần, mô tả, link sự kiện...">
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
                        <th>Mã sự kiện</th>
                        <th>Tên lớp học phần</th>
                        <th>Tên sự kiện</th>
                        <th>Mô tả</th>
                        <th>Thời gian bắt đầu</th>
                        <th>Thời gian kết thúc</th>
                        <th>Khóa chủ trì</th>
                        <th>Link tham gia sự kiện</th>
                        <th>Mật khẩu</th>
                        <th>Trạng thái</th>
                        <th>Tính năng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSachSuKien as $suKien)
                    <tr style="cursor: pointer;" data-href="{{ route('giangvien.su-kien-zoom.chi-tiet', ['id' => $suKien->MaSuKienZoom]) }}">
                        <td>#{{ $suKien->MaSuKienZoom }}</td>
                        <td class="highlight-target">{{ $suKien->TenLopHocPhan ?? '' }}</td>
                        <td class="highlight-target">{{ $suKien->TenSuKien }}</td>
                        <td class="highlight-target">{{ $suKien->MoTa }}</td>
                        <td>{{ \Carbon\Carbon::parse($suKien->ThoiGianBatDau)->format('H:i:s d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($suKien->ThoiGianKetThuc)->format('H:i:s d/m/Y') }}</td>
                        <td>{{ $suKien->KhoaChuTri }}</td>
                        <td style="word-break: break-all;" class="highlight-target">
                            <a href="{{ $suKien->LinkSuKien }}" target="_blank">{{ $suKien->LinkSuKien }}</a>
                        </td>
                        <td>{{ $suKien->MatKhauSuKien }}</td>
                        <td>
                            @if ($suKien->ThoiGianBatDau > now())
                            <span class="badge bg-secondary">Chưa bắt đầu</span>
                            @elseif ($suKien->ThoiGianKetThuc >= now() && $suKien->ThoiGianBatDau <= now()) <span class="badge bg-success">Đang diễn ra</span>
                                @else
                                <span class="badge bg-danger">Đã kết thúc</span>
                                @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalXacNhanXoa" data-id="{{ $suKien->MaSuKienZoom }}" data-ten="{{ $suKien->TenSuKien }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <a class="btn btn-warning btn-sm mt-1" href="{{ route('giangvien.su-kien-zoom.form-sua', ['id' => $suKien->MaSuKienZoom]) }}"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">Không tìm thấy kết quả nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <x-phan-trang :data="$danhSachSuKien" label="sự kiện" />
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
                Bạn có chắc chắn muốn xóa sự kiện Zoom <strong id="tenSuKienZoom"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="formXoaSuKien" method="POST">
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
    .tile {
        position: relative;
        background: #ffffff;
        border-radius: .375rem;
        padding: 20px;
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
        margin-bottom: 30px;
        transition: all 0.3s ease-in-out;
    }

    .tile .tile-title {
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 20px;
        border-bottom: 2px solid #FFD43B;
        padding-bottom: 10px;
        padding-left: 5px;
        color: black;
    }

    .element-button {
        position: relative;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

</style>
@endsection

@section('scripts')
<script>
    const modalXoa = document.getElementById('modalXacNhanXoa');
    modalXoa.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const suKienId = button.getAttribute('data-id');
        const tenSuKien = button.getAttribute('data-ten');

        modalXoa.querySelector('#tenSuKienZoom').textContent = `"${tenSuKien}"`;

        const form = modalXoa.querySelector('#formXoaSuKien');
        form.action = `/giang-vien/su-kien-zoom/xoa/${suKienId}`;
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('tr[data-href]');
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.closest('a') || e.target.closest('button')) return;
                window.location.href = row.dataset.href;
            });
        });
    });

</script>
@endsection
