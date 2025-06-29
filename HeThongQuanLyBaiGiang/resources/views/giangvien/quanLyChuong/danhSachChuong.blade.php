@extends('layouts.teacherLayout')
@section('title','Giảng viên - Danh sách chương')
@section('tenTrang', $baiGiang->TenBaiGiang . ' / Danh sách chương')
@section('content')
<div class="container my-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalChuong" data-mode="add" data-ma-bai-giang="{{ $baiGiang->MaBaiGiang }}">
                        <i class="fas fa-plus"></i> Thêm chương mới
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
            <div class="accordion" id="accordionChuong">
                @foreach ($chuongList as $maChuong => $chuong)
                @php
                $chuongId = 'chuong-' . Str::slug($chuong['TenChuong']) . '-' . uniqid();
                @endphp

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-{{ $chuongId }}">
                        <div class="d-flex align-items-center bg-light px-3 py-2">
                            <button class="accordion-button collapsed flex-grow-1 justify-content-start bg-light border-0 fw-bold" style="font-size: 18px" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $chuongId }}" aria-expanded="false" aria-controls="collapse-{{ $chuongId }}">
                                <i class="fas fa-folder me-3 text-warning"></i> {{ $chuong['TenChuong'] }}
                            </button>

                            {{-- Toggle bật tắt --}}
                            <div class="form-check form-switch m-0 ms-2" 
                            style="transform: scale(0.65);">
                                <input class="form-check-input border border-secondary" 
                                type="checkbox" id="switchChuong{{ $maChuong }}" 
                                onclick="this.checked = !this.checked; 
                                moModalTrangThai('{{ route('giangvien.bai-giang.chuong.doi-trang-thai', [$baiGiang->MaBaiGiang, $maChuong]) }}', 
                                '{{ $chuong['TenChuong'] }}', 
                                {{ $chuong['TrangThai'] }}, true)" 
                                title="Ẩn / Hiện chương" 
                                {{ $chuong['TrangThai'] == 1 ? 'checked' : '' }}>
                            </div>

                            {{-- Nút sửa chương --}}
                            <button type="button" class="btn btn-warning btn-sm me-1 ms-2 d-inline-flex align-items-center" style="white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#modalChuong" data-mode="edit" data-ma-chuong="{{ $maChuong }}" data-ma-bai-giang="{{ $baiGiang->MaBaiGiang }}" title="Sửa chương">
                                <i class="fas fa-edit me-1"></i> Sửa
                            </button>

                            {{-- Nút thêm bài --}}
                            <a class="btn btn-success btn-sm me-2 d-inline-flex align-items-center" style="white-space: nowrap;" href="{{ route('giangvien.bai-giang.chuong.bai.form-them', ['maBaiGiang' => $baiGiang->MaBaiGiang, 'maChuong' => $maChuong]) }}" title="Thêm bài">
                                <i class="fas fa-plus me-1"></i> Thêm bài
                            </a>
                        </div>
                    </h2>

                    <div id="collapse-{{ $chuongId }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $chuongId }}" data-bs-parent="#accordionChuong">
                        <div class="accordion-body">
                            <ul class="list-group">
                                @foreach ($chuong['Bai'] as $tenBai => $dsBaiChiTiet)
                                @php $bai = $dsBaiChiTiet->first(); @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span>{{ $bai->TenBai }}</span><br>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge {{ $bai->TrangThai ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $bai->TrangThai ? 'Hiện' : 'Ẩn' }}
                                        </span>
                                        <span class="text-muted px-3">
                                            <strong>Cập nhật:</strong> {{ \Carbon\Carbon::parse($bai->updated_at)->timezone('Asia/Ho_Chi_Minh')->format('H:i:s d/m/Y') }}
                                        </span>
                                        @if ($bai->TrangThai == 1)
                                        <button type="button" 
                                        class="btn btn-danger btn-sm me-1" 
                                        aria-label="Ẩn bài {{ $bai->TenBai }}" 
                                        onclick="moModalTrangThai('{{ route('giangvien.bai-giang.chuong.bai.doi-trang-thai', ['maBaiGiang' => $baiGiang->MaBaiGiang, 'maChuong' => $maChuong, 'maBai' => $bai->MaBai]) }}', '{{ $bai->TenBai }}', 1)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @else
                                        <button type="button" 
                                        class="btn btn-success btn-sm me-1" 
                                        aria-label="Khôi phục bài {{ $bai->TenBai }}" onclick="moModalTrangThai('{{ route('giangvien.bai-giang.chuong.bai.doi-trang-thai', ['maBaiGiang' => $baiGiang->MaBaiGiang, 'maChuong' => $maChuong, 'maBai' => $bai->MaBai]) }}', '{{ $bai->TenBai }}', 0)">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        @endif
                                        <a href="{{ route('giangvien.bai-giang.chuong.bai.form-sua', ['maBaiGiang' => $baiGiang->MaBaiGiang, 'maChuong' => $maChuong, 'maBai' => $bai->MaBai]) }}" class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('giangvien.bai-giang.chuong.bai.chi-tiet', ['maBaiGiang' => $baiGiang->MaBaiGiang, 'maChuong' => $maChuong, 'maBai' => $bai->MaBai]) }}" class="btn btn-primary btn-sm">
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

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalChuong'));
        modal.show();

        const form = document.getElementById('formChuong');

        @if(session('actionUrl'))
        form.action = @json(session('actionUrl'));
        @endif

        @if(session('isEditing'))
        document.getElementById('formMethod').value = 'PUT';
        @else
        document.getElementById('formMethod').value = 'POST';
        @endif
    });

</script>
@endif

{{-- Modal Thêm/Sửa chương --}}
<div class="modal fade" id="modalChuong" tabindex="-1" aria-labelledby="modalChuongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="formChuong" method="POST" action="">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalChuongLabel">Thêm chương</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrorsChuong" class="alert alert-danger" style="display: none;"></div>
                    <input type="hidden" id="MaChuong" name="MaChuong">
                    <div class="mb-3">
                        <label for="TenChuong" class="form-label fw-semibold">Tên chương <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="TenChuong" name="TenChuong" maxlength="255" required placeholder="Nhập tên chương mới" title="Nhập tên chương mới" value="{{ old('TenChuong') }}">
                        @error('TenChuong')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="MoTa" class="form-label fw-semibold">Mô tả</label>
                        <textarea class="form-control" id="MoTa" name="MoTa" rows="3" maxlength="255" title="Nhập mô tả chương" placeholder="Nhập mô tả chương">{{ old('MoTa') }}</textarea>
                        @error('MoTa')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="TrangThai" class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select" id="TrangThai" name="TrangThai" required title="Chọn trạng thái chương">
                            <option value="">-- Chọn trạng thái --</option>
                            <option value="1" {{ old('TrangThai') == 1 ? 'selected' : '' }}>Hiện</option>
                            <option value="0" {{ old('TrangThai') == 0 ? 'selected' : '' }}>Ẩn</option>
                        </select>
                        @error('TrangThai')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitChuong">Lưu chương</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Model trạng thái --}}
<div class="modal fade" id="modalTrangThai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formTrangThai" method="POST">
            @csrf
            @method('POST')
            <input type="hidden" name="trangThai" id="inputTrangThai">
            <div class="modal-content">
                <div class="modal-header bg-warning bg-opacity-10 border-bottom-0">
                    <h5 class="modal-title text-warning fw-semibold">
                        <i class="fas fa-exclamation-triangle me-2"></i> Xác nhận hành động
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="noiDungTrangThai">
                    <!-- Nội dung sẽ được chèn bằng JS -->
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xác nhận</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function moModalTrangThai(url, tenDoiTuong = '', trangThai = 1, isChuong = false) {
        const action = trangThai == 1 ? 'ẩn' : 'hiện';
        const loai = isChuong ? 'chương và các bài liên quan' : 'bài';
        const noiDung = `Bạn có chắc chắn muốn <strong>${action}</strong> ${loai} <strong>${tenDoiTuong}</strong> không?`;
        document.getElementById('formTrangThai').action = url;
        document.getElementById('inputTrangThai').value = trangThai == 1 ? 0 : 1;
        document.getElementById('noiDungTrangThai').innerHTML = noiDung;
        const modal = new bootstrap.Modal(document.getElementById('modalTrangThai'));
        modal.show();
    }

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalChuong');
        const form = document.getElementById('formChuong');
        const methodInput = document.getElementById('formMethod');
        const title = modal.querySelector('.modal-title');
        const btnSubmit = document.getElementById('btnSubmitChuong');

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const mode = button.getAttribute('data-mode'); // add | edit
            const maBaiGiang = button.getAttribute('data-ma-bai-giang');
            const maChuong = button.getAttribute('data-ma-chuong');
            const actionAdd = `/giang-vien/bai-giang/${maBaiGiang}/chuong/them`;

            if (mode === 'add') {
                form.reset();
                form.action = actionAdd;
                methodInput.value = 'POST';
                title.textContent = 'Thêm chương';
                btnSubmit.textContent = 'Lưu chương';
            }

            if (mode === 'edit') {
                const actionEdit = `/giang-vien/bai-giang/${maBaiGiang}/chuong/cap-nhat/${maChuong}`;
                form.action = actionEdit;
                methodInput.value = 'PUT';
                title.textContent = 'Cập nhật chương';
                btnSubmit.textContent = 'Cập nhật';

                fetch(`/giang-vien/bai-giang/${maBaiGiang}/chuong/thong-tin/${maChuong}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('MaChuong').value = data.MaChuong;
                        document.getElementById('TenChuong').value = data.TenChuong;
                        document.getElementById('MoTa').value = data.MoTa ?? '';
                        document.getElementById('TrangThai').value = data.TrangThaiChuong;
                    })
                    .catch(error => {
                        alert('Không thể tải dữ liệu chương.');
                        console.error(error);
                    });
            }
        });
    });

</script>

@endsection
