@extends('layouts.teacherLayout')
@section('title','Giảng viên - Chỉnh sửa bài giảng')
@section('tenTrang', $hocPhan->TenHocPhan . ' / Chỉnh sửa bài giảng / ' . $baiGiang->MaBaiGiang)
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Chỉnh sửa bài giảng</h3>
            <div class="tile-body">
                <form action="{{ route('giang-vien.bai-giang.cap-nhat', ['maHocPhan' => $hocPhan->MaHocPhan, 'maBaiGiang' => $baiGiang->MaBaiGiang]) }}" method="POST" class="row">
                    @csrf
                    @method('PUT')
                    <div class="form-group col-md-4">
                        <label class="control-label">Tên học phần <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" value="{{ $hocPhan->TenHocPhan }}" readonly disabled>
                        <input type="hidden" name="MaHocPhan" value="{{ $hocPhan->MaHocPhan }}">
                        @error('MaHocPhan')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tên chương -->
                    <div class="form-group col-md-4">
                        <label for="TenChuong">Tên chương</label>
                        <select class="form-control" id="selectChuong" onchange="onChangeChuong()" {{ $baiGiang->TenChuong == 'other' ? '' : 'name=TenChuong required' }}>
                            <option value="">-- Chọn chương --</option>
                            @foreach ($chuongBai as $chuong => $bais)
                            <option value="{{ $chuong }}" {{ $baiGiang->TenChuong == $chuong ? 'selected' : '' }}>
                                {{ $chuong }}
                            </option>
                            @endforeach
                            <option value="other" {{ $baiGiang->TenChuong == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>

                        <input type="text" id="inputChuongMoi" class="form-control mt-2" placeholder="Nhập chương mới" value="{{ $baiGiang->TenChuong == 'other' ? $baiGiang->TenChuong : '' }}" style="display: {{ $baiGiang->TenChuong == 'other' ? 'block' : 'none' }}" {{ $baiGiang->TenChuong == 'other' ? 'name=TenChuong required' : '' }}>

                        @error('TenChuong')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tên bài -->
                    <div class="form-group col-md-4">
                        <label for="TenBai">Tên bài</label>
                        <select class="form-control" id="selectBai" onchange="onChangeBai()" {{ $baiGiang->TenBai == 'other' ? '' : 'name=TenBai required' }} data-selected="{{ $baiGiang->TenBai }}">
                            <option value="">-- Chọn bài --</option>
                        </select>

                        <input type="text" id="inputBaiMoi" class="form-control mt-2" placeholder="Nhập bài mới" value="{{ $baiGiang->TenBai == 'other' ? old('TenBai', $baiGiang->TenBai) : '' }}" style="display: {{ $baiGiang->TenBai == 'other' ? 'block' : 'none' }}" {{ $baiGiang->TenBai == 'other' ? 'name=TenBai required' : '' }}>

                        @error('TenBai')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tên bài giảng -->
                    <div class="form-group col-md-4">
                        <label for="TenBaiGiang">Tên bài giảng</label>
                        <input type="text" name="TenBaiGiang" class="form-control" value="{{ $baiGiang->TenBaiGiang }}" required>
                        @error('TenBaiGiang')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mô tả -->
                    <div class="form-group col-md-4">
                        <label for="MoTa">Mô tả</label>
                        <input type="text" name="MoTa" class="form-control" value="{{ $baiGiang->MoTa }}">
                        @error('MoTa')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nội dung bài giảng --}}
                    <div class="form-group col-md-12">
                        <label for="NoiDung">Nội dung bài giảng</label>
                        <textarea name="NoiDung" id="editor" class="form-control">{{ $baiGiang->NoiDung }}</textarea>
                        @error('NoiDung')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
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

<!-- Modal elFinder -->
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

@section('style')
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
    window.APP_URL = "{{ url('/') }}";
    window.ELFINDER_URL = '{{ route("elfinder.connector") }}';
    window.ELFINDER_SOUND = '{{ asset("assets/sounds") }}';
    window.chuongBai = @json($chuongBai);

</script>
<script src="{{ asset('./js/teacher/baigiang.js') }}"></script>
<script>
    initTinyMCE({{ $hocPhan -> MaHocPhan}}, '{{ csrf_token() }}');
    initCancelUpload({{ $hocPhan -> MaHocPhan }}, '{{ route("baiGiang.xoaTamUploads") }}', '{{ csrf_token() }}');
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chuong = @json($baiGiang -> TenChuong ?? '');
        const bai = @json($baiGiang -> TenBai ?? '');
        const chuongBai = @json($chuongBai);

        const selectBai = document.getElementById('selectBai');

        if (chuong && chuongBai.hasOwnProperty(chuong)) {
            // Xóa các option cũ
            selectBai.innerHTML = '<option value="">-- Chọn bài --</option>';

            // Render lại danh sách bài
            chuongBai[chuong].forEach(function(tenBai) {
                const option = document.createElement('option');
                option.value = tenBai;
                option.text = tenBai;
                selectBai.appendChild(option);
            });

            // Thêm option 'Khác'
            const optionOther = document.createElement('option');
            optionOther.value = 'other';
            optionOther.text = 'Khác';
            selectBai.appendChild(optionOther);

            // Gán selected cho bài hiện tại
            selectBai.value = bai;
        }
    });
    // Gửi yêu cầu xoá tệp tạm khi hủy
    function initCancelUpload(maHocPhan, routeUrl, csrfToken) {
        document.getElementById('btn-cancel').addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn hủy bỏ cập nhật bài giảng ko?')) {
                fetch(routeUrl, {
                        method: 'POST'
                        , headers: {
                            'X-CSRF-TOKEN': csrfToken
                            , 'Content-Type': 'application/json'
                        , }
                        , body: JSON.stringify({
                            MaHocPhan: maHocPhan
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        window.location.href = document.referrer;
                    });
            }
        });
    }
</script>

@endsection
