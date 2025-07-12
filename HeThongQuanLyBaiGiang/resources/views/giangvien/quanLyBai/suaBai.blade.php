@extends('layouts.teacherLayout')
@section('title','Giảng viên - Chỉnh sửa bài học')
@section('tenTrang', $baiHoc->TenBaiGiang . ' / ' . $baiHoc->TenChuong . ' / ' . $baiHoc->TenBai )

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Chỉnh sửa bài học</h3>
            <div class="tile-body">
                <form action="#" method="POST" class="row">
                    @csrf
                    @method('PUT')
                    <div class="form-group col-md-6">
                        <label class="control-label">Tên bài giảng <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" value="{{ $baiHoc->TenBaiGiang }}" readonly disabled>
                        <input type="hidden" name="MaBaiGiang" value="{{ $baiHoc->MaBaiGiang }}">
                        @error('MaBaiGiang')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label class="control-label">Tên chương <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" value="{{ $baiHoc->TenChuong }}" readonly disabled>
                        <input type="hidden" name="MaChuong" value="{{ $baiHoc->MaChuong }}">
                        @error('MaChuong')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tên bài học --}}
                    <div class="form-group col-md-4">
                        <label for="TenBai">Tên bài học <span class="text-danger">*</span></label>
                        <input type="text" name="TenBai" class="form-control" value="{{ old('TenBai', $baiHoc->TenBai) }}" required>
                        @error('TenBai')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div class="form-group col-md-4">
                        <label for="MoTa">Mô tả</label>
                        <input type="text" name="MoTa" class="form-control" value="{{ old('MoTa',$baiHoc->MoTa) }}">
                        @error('MoTa')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Trạng thái --}}
                    <div class="form-group col-md-4">
                        <label for="TrangThai">Trạng thái <span class="text-danger">*</span></label>
                        <select name="TrangThai" class="form-control" required>
                            <option value="1" {{ old('TrangThai', $baiHoc->TrangThai) == 1 ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ old('TrangThai', $baiHoc->TrangThai) == 0 ? 'selected' : '' }}>Ẩn</option>
                        </select>
                        @error('TrangThai')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- Nội dung bài học --}}
                    <div class="form-group col-md-12">
                        <label for="NoiDung">Nội dung bài học <span class="text-danger">*</span></label>
                        @error('NoiDung')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <textarea name="NoiDung" id="editor" class="form-control">{{ old('NoiDung', $baiHoc->NoiDung) }}</textarea>
                    </div>

                    <div class="form-group col-12 mt-1">
                        <button class="btn btn-success" type="submit">Cập nhật</button>
                        <button type="button" class="btn btn-danger" id="btn-cancel">Hủy bỏ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<input type="file" id="upload-docx" accept=".docx" style="display: none;">

{{-- Modal elFinder --}}
<div class="modal fade" id="elfinderModal" style="z-index: 10560 !important;">
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
<script src="https://cdn.tiny.cloud/1/n1ijx6jzdaxublu2kj9hbij8t6amt8tiqyhd8o6injflv8og/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<link rel="stylesheet" href="{{ asset('./css/teacher/form.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/elfinder.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@endsection

@section('scripts')
<script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/elfinder.min.js') }}"></script>
{{-- <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script> --}}
<script>
    window.APP_URL = @json(url('/'));
    window.ELFINDER_URL = @json(route('elfinder.connector', [
        'maBaiGiang' => $baiHoc -> MaBaiGiang
        , 'maBai' => $baiHoc -> MaBai
    ]));
    window.ELFINDER_BASE_URL = @json(route('elfinder.connector'));
    window.ELFINDER_SOUND = @json(asset('assets/sounds'));
    window.csrfToken = @json(csrf_token());
    window.maBaiGiang = @json($baiHoc -> MaBaiGiang);
    window.maBai = @json($baiHoc -> MaBai ?? '');
    window.cancelRoute = '{{ route('giangvien.bai-giang.chuong.bai.huy', ['maBaiGiang' => $baiHoc->MaBaiGiang, 'maChuong' => $baiHoc->MaChuong, 'maBai' => $baiHoc->MaBai]) }}';
</script>

<script src="{{ asset('./js/teacher/baigiang.js') }}"></script>

@endsection
