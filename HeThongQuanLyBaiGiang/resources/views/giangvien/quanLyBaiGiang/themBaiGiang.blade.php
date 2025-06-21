@extends('layouts.teacherLayout')
@section('title','Giảng viên - Danh sách bài giảng')
@section('tenTrang', $hocPhan->TenHocPhan . ' / Thêm bài giảng')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Thêm bài giảng</h3>
            <div class="tile-body">
                <form action="{{ route('giang-vien.bai-giang.them', ['id' => $hocPhan->MaHocPhan]) }}" method="POST" class="row" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-md-4">
                        <label class="control-label">Tên học phần <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" value="{{ $hocPhan->TenHocPhan }}" readonly disabled>
                        <input type="hidden" name="MaHocPhan" value="{{ $hocPhan->MaHocPhan }}">
                        @error('MaHocPhan')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tên chương --}}
                    <div class="form-group col-md-4">
                        <label for="TenChuong">Tên chương</label>
                        <select class="form-control" id="selectChuong" onchange="onChangeChuong()" required>
                            <option value="">-- Chọn chương --</option>
                            @foreach ($chuongBai as $chuong => $bais)
                            <option value="{{ $chuong }}">{{ $chuong }}</option>
                            @endforeach
                            <option value="other">Khác</option>
                        </select>
                        <input type="text" id="inputChuongMoi" class="form-control mt-2" placeholder="Nhập chương mới" style="display: none;">
                        @error('TenChuong')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tên bài --}}
                    <div class="form-group col-md-4">
                        <label for="TenBai">Tên bài</label>
                        <select class="form-control" id="selectBai" onchange="onChangeBai()" required>
                            <option value="">-- Chọn bài --</option>
                        </select>
                        <input type="text" id="inputBaiMoi" class="form-control mt-2" placeholder="Nhập bài mới" style="display: none;">
                        @error('TenBai')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tên bài giảng --}}
                    <div class="form-group col-md-4">
                        <label for="TenBaiGiang">Tên bài giảng</label>
                        <input type="text" name="TenBaiGiang" class="form-control" required>
                        @error('TenBaiGiang')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div class="form-group col-md-4">
                        <label for="MoTa">Mô tả</label>
                        <input type="text" name="MoTa" class="form-control">
                        @error('MoTa')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Trạng thái --}}
                    <div class="form-group col-md-4">
                        <label for="TrangThai">Trạng thái</label>
                        <select name="TrangThai" class="form-control" required>
                            <option value="1" {{ old('TrangThai', 1) ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ old('TrangThai', 0) ? 'selected' : '' }}>Ẩn</option>
                        </select>
                        @error('TrangThai')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nội dung bài giảng --}}
                    <div class="form-group col-md-12">
                        <label for="MoTa">Nội dung bài giảng</label>
                        <textarea name="NoiDung" id="editor" class="form-control"></textarea>
                        @error('NoiDung')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-12 mt-1">
                        <button class="btn btn-success" type="submit">Lưu lại</button>
                        <button type="button" class="btn btn-danger" id="btn-cancel">Hủy bỏ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal elFinder --}}
<div class="modal fade" id="elfinderModal">
    <div class="modal-dialog modal-lg" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div id="elfinder" style="height: 500px;"></div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('./css/teacher/form.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/elfinder.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/elfinder.min.js') }}"></script>
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
    window.APP_URL = @json(url('/'));
    window.ELFINDER_URL = @json(route('elfinder.connector', [
        'maHocPhan' => $hocPhan->MaHocPhan,
        'maBaiGiang' => $baiGiang->MaBaiGiang ?? ''
    ]));
    window.ELFINDER_SOUND = @json(asset('assets/sounds'));
    window.chuongBai = @json($chuongBai);
    window.csrfToken = @json(csrf_token());
    window.maHocPhan = @json($hocPhan->MaHocPhan);
    window.maBaiGiang = @json($baiGiang->MaBaiGiang ?? '');

</script>

<script src="{{ asset('./js/teacher/baigiang.js') }}"></script>

<script>
    initTinyMCE(window.maHocPhan, window.csrfToken);

    document.getElementById('btn-cancel')?.addEventListener('click', () => {
        handleCancelBaiGiang({
            routeUrl: '{{ route('bai-giang.huy', ['maHocPhan' => $hocPhan->MaHocPhan]) }}'
            , maHocPhan: window.maHocPhan
            , maBaiGiang: window.maBaiGiang
        });
    });

</script>
@endsection
