<div class="col-md-12 pe-0">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Bình luận</h5>
        </div>
        <div class="card-body">
            {{-- Form thêm bình luận --}}
            <form method="POST" action="{{ route('binhluan.guibinhluan') }}">
                @csrf
                <input type="hidden" name="MaBai" value="{{ $baiGiang->MaBai }}">
                <div class="d-flex mb-3">
                    <textarea name="NoiDung" class="form-control me-2" rows="2" placeholder="Nhập bình luận mới..."
                        required>{{ old('NoiDung') }}</textarea>
                    <button type="submit" class="btn btn-success" style="width: 40px; height: 40px;"><i
                            class="fas fa-paper-plane"></i></button>
                </div>
                @error('NoiDung')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </form>

            <hr>

            {{-- Danh sách bình luận --}}
            @forelse($binhLuans as $binhLuan)
            <div class="mb-3">
                <div class="d-flex align-items-start">
                    <img src="{{ asset($binhLuan->AnhDaiDien ?? '/AnhDaiDien/default-avatar.png') }}"
                        class="rounded-circle me-2" width="40" height="40" />
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong class="text-primary">{{ $binhLuan->HoTen }}</strong>
                                <small class="text-muted ms-2">
                                    {{ \Carbon\Carbon::parse($binhLuan->created_at)->diffForHumans() }}
                                    @if ($binhLuan->updated_at != $binhLuan->created_at)
                                    <em> - Đã chỉnh sửa
                                        ({{ \Carbon\Carbon::parse($binhLuan->updated_at)->diffForHumans() }})</em>
                                    @endif
                                </small>
                            </div>

                            @if($binhLuan->MaNguoiGui == Auth::id())
                            {{-- Dropdown 3 chấm --}}
                            <div class="dropdown">
                                <button class="btn btn-sm p-0 text-secondary border-0 bg-transparent" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item text-primary" href="javascript:void(0);"
                                            onclick="hienFormSua({{ $binhLuan->MaBinhLuan }})">
                                            <i class="fas fa-edit me-1"></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('binhluan.xoa', $binhLuan->MaBinhLuan) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"
                                                onclick="return confirm('Xác nhận xóa bình luận?')">
                                                <i class="fas fa-trash-alt me-1"></i> Xóa
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        </div>

                        {{-- Nội dung --}}
                        <p class="mb-1" id="nd-hien-{{ $binhLuan->MaBinhLuan }}">{{ $binhLuan->NoiDung }}</p>

                        {{-- Form sửa --}}
                        <form method="POST" action="{{ route('binhluan.capnhat') }}" class="d-none"
                            id="form-sua-{{ $binhLuan->MaBinhLuan }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="MaBinhLuan" value="{{ $binhLuan->MaBinhLuan }}">
                            <textarea name="NoiDung" class="form-control mb-2" rows="2"
                                required>{{ $binhLuan->NoiDung }}</textarea>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="anFormSua({{ $binhLuan->MaBinhLuan }})">Hủy</button>
                                <button type="submit" class="btn btn-sm btn-primary">Lưu</button>
                            </div>
                        </form>

                        <div class="mb-2">
                            <a href="javascript:void(0);" class="text-primary text-decoration-none"
                                onclick="hienFormTraLoi({{ $binhLuan->MaBinhLuan }})">
                                <i class="fas fa-share fa-rotate-270 fa-flip-vertical"></i> Phản hồi
                            </a>
                        </div>

                        {{-- Form trả lời --}}
                        <form method="POST" action="{{ route('binhluan.traloi') }}" class="ms-4 mt-2 d-none"
                            id="form-tra-loi-{{ $binhLuan->MaBinhLuan }}">
                            @csrf
                            <input type="hidden" name="MaBinhLuan" value="{{ $binhLuan->MaBinhLuan }}">
                            <input type="hidden" name="MaBai" value="{{ $baiGiang->MaBai }}">
                            <textarea name="NoiDung" class="form-control mb-2" rows="2" placeholder="Trả lời..."
                                required></textarea>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="anFormTraLoi({{ $binhLuan->MaBinhLuan }})">Hủy</button>
                                <button type="submit" class="btn btn-sm btn-primary">Trả lời</button>
                            </div>
                        </form>

                        {{-- Trả lời --}}
                        @if($binhLuan->traLoi->count())
                        <a href="javascript:void(0);" class="text-decoration-none ms-4"
                            onclick="toggleTraLoi({{ $binhLuan->MaBinhLuan }})">
                            <small class="text-muted">Xem {{ $binhLuan->traLoi->count() }} câu trả lời</small>
                        </a>

                        <div id="ds-tra-loi-{{ $binhLuan->MaBinhLuan }}" class="mt-2 d-none">
                            @foreach($binhLuan->traLoi as $traLoi)
                            <div class="d-flex ms-4 mt-2 border-start ps-3">
                                <img src="{{ asset($binhLuan->AnhDaiDien ??'/AnhDaiDien/default-avatar.png') }}"
                                    class="rounded-circle me-2" width="32" height="32" />
                                <div>
                                    <strong class="text-primary">{{ $traLoi->HoTen }}</strong>
                                    <small class="text-muted ms-2">{{
                                        \Carbon\Carbon::parse($traLoi->created_at)->diffForHumans() }}</small>
                                    <p class="mb-1">{{ $traLoi->NoiDung }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <hr>
            @empty
            <p class="text-muted">Chưa có bình luận nào.</p>
            @endforelse
        </div>
    </div>
</div>


<script>
    function hienFormTraLoi(id) {
        const form = document.getElementById('form-tra-loi-' + id);
        if (form) form.classList.remove('d-none');
    }

    function anFormTraLoi(id) {
        const form = document.getElementById('form-tra-loi-' + id);
        if (form) {
            form.classList.add('d-none');
            const textarea = form.querySelector('textarea[name="NoiDung"]');
            if (textarea) textarea.value = '';
        }
    }

    function toggleTraLoi(id) {
        const container = document.getElementById('ds-tra-loi-' + id);
        if (container.classList.contains('d-none')) {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }

    function hienFormSua(id) {
        document.getElementById('nd-hien-' + id)?.classList.add('d-none');
        document.getElementById('form-sua-' + id)?.classList.remove('d-none');
    }

    function anFormSua(id) {
        document.getElementById('form-sua-' + id)?.classList.add('d-none');
        document.getElementById('nd-hien-' + id)?.classList.remove('d-none');
    }

</script>