@extends('layouts.teacherLayout')
@section('title','Giảng viên - Danh sách bài giảng')
@section('tenTrang', $hocPhan->TenHocPhan . ' / Danh sách bài giảng')
@section('content')
<div class="container my-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('giang-vien.bai-giang.form-them', ['id' => $hocPhan->MaHocPhan]) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Thêm bài giảng
                    </a>
                    <button type="button" class="btn btn-warning">
                        <i class="fas fa-signal"></i> Thống kê
                    </button>
                </div>

                <form method="GET" class="d-flex align-items-center gap-2 border rounded p-2 flex-nowrap" style="min-width: 250px;" title="Tìm kiếm bài giảng theo tên chương, bài, mục, bài giảng, mô tả.">
                    <label for="search" class="fw-bold mb-0">Tìm kiếm:</label>
                    <input type="text" name="search" id="search" class="form-control form-control-sm w-auto" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <hr />

            <div class="accordion" id="chuong">
                @foreach ($baiGiangs as $tenChuong => $cacBai)
                @php $chuongId = Str::slug($tenChuong) . '-' . uniqid(); @endphp
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-{{ $chuongId }}">
                        <button class="accordion-button collapsed show fw-bold text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $chuongId }}" aria-expanded="false" aria-controls="collapse-{{ $chuongId }}">
                            {{ $tenChuong }}
                        </button>
                    </h2>
                    <div id="collapse-{{ $chuongId }}" class="accordion-collapse collapse show" aria-labelledby="heading-{{ $chuongId }}" data-bs-parent="#chuong">
                        <div class="accordion-body">
                            <div class="accordion" id="bai-{{ $chuongId }}">
                                @foreach ($cacBai as $tenBai => $baiGiangs)
                                @php $baiId = Str::slug($tenBai) . '-' . uniqid(); @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $baiId }}">
                                        <button class="accordion-button collapsed fw-bold text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $baiId }}" aria-expanded="false" aria-controls="collapse-{{ $baiId }}">
                                            {{ $tenBai }}
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $baiId }}" class="accordion-collapse collapse show" aria-labelledby="heading-{{ $baiId }}" data-bs-parent="#bai-{{ $chuongId }}">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                @foreach ($baiGiangs as $baiGiang)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span> {{ $baiGiang->TenBaiGiang }}</span><br>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge {{ $baiGiang->TrangThai ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $baiGiang->TrangThai ? 'Hiện' : 'Ẩn' }}
                                                        </span>
                                                        <span class="text-muted px-3">
                                                            <strong>Cập nhật:</strong>
                                                            {{ \Carbon\Carbon::parse($baiGiang->updated_at)->format('H:i:s d/m/Y') }}
                                                        </span>

                                                        @if ($baiGiang->TrangThai == 1)
                                                        <button type="button" class="btn btn-danger btn-sm me-1" aria-label="Ẩn bài giảng {{ $baiGiang->TenBaiGiang }}" onclick="moModalTrangThai(
                                                                                '{{ route('baiGiang.thayDoiTrangThai', ['maHocPhan' => $baiGiang->MaHocPhan, 'maBaiGiang' => $baiGiang->MaBaiGiang]) }}',
                                                                                '{{ $baiGiang->TenBaiGiang }}',
                                                                                1)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        @else
                                                        <button type="button" class="btn btn-success btn-sm me-1" aria-label="Khôi phục bài giảng {{ $baiGiang->TenBaiGiang }}" onclick="moModalTrangThai(
                                                                                '{{ route('baiGiang.thayDoiTrangThai', ['maHocPhan' => $baiGiang->MaHocPhan, 'maBaiGiang' => $baiGiang->MaBaiGiang]) }}',
                                                                                '{{ $baiGiang->TenBaiGiang }}',
                                                                                0)">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                        @endif

                                                        <a href="#" class="btn btn-warning btn-sm me-1">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-info-circle"></i>
                                                        </a>
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>


        </div>
    </div>
</div>


{{-- Model chuyển trạng thái Ẩn(Xóa) / Hiện --}}
<div class="modal fade" id="modalTrangThai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formTrangThai" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cảnh báo</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="noiDungTrangThai"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Xác nhận</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function moModalTrangThai(url, tenMuc, trangThai) {
        const action = trangThai ? 'ẩn (xóa)' : 'khôi phục';
        document.getElementById('formTrangThai').action = url;
        document.getElementById('noiDungTrangThai').innerHTML =
            `Bạn có chắc chắn muốn <strong>${action}</strong> bài giảng <strong>${tenMuc}</strong> không?`;
        new bootstrap.Modal(document.getElementById('modalTrangThai')).show();
    }

</script>
@endsection
