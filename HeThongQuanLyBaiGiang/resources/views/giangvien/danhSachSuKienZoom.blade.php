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
                    <div class="input-group" style="max-width: 420px; width: 100%;">
                        <button class="btn btn-secondary" type="button" id="searchBtn">Tìm kiếm</button>
                        <input type="text" class="form-control" placeholder="Tìm kiếm tên sự kiện, tên lớp học phần, ..." id="searchQuery">
                    </div>
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
                        <th>Link bắt đầu sự kiện</th>
                        <th>Link tham gia sự kiện</th>
                        <th>Mật khẩu</th>
                        <th>Trạng thái</th>
                        <th>Tính năng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSachSuKien as $suKien)
                    <tr>
                        <td>#{{ $suKien->MaSuKienZoom }}</td>
                        <td>{{ $suKien->TenLopHocPhan ?? '' }}</td>
                        <td>{{ $suKien->TenSuKien }}</td>
                        <td>{{ $suKien->MoTa }}</td>
                        <td>{{ \Carbon\Carbon::parse($suKien->ThoiGianBatDau)->format('H:i:s d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($suKien->ThoiGianKetThuc)->format('H:i:s d/m/Y') }}</td>
                        <td style="word-break: break-all;">
                            <a href="{{ $suKien->LinkBatDauSuKien }}" target="_blank">{{ Str::Limit($suKien->LinkBatDauSuKien, 37) }}</a>
                        </td>
                        <td style="word-break: break-all;">
                            <a href="{{ $suKien->LinkThamGiaSuKien }}" target="_blank">{{ $suKien->LinkThamGiaSuKien }}</a>
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
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                            <button class="btn btn-warning btn-sm mt-1"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">Không tìm thấy kết quả nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <x-phan-trang :data="$danhSachSuKien" label="sự kiện" />
        </div>
    </div>
</div>
</div>
@endsection

@section('style')
<style>
    .app-content {
        min-height: calc(100vh - 50px);
        padding: 10px;
        background-color: #f5f5f5;
        transition: margin-left 0.3s ease;
        margin-left: 250px;
        margin-top: 10px;
    }

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

@endsection
