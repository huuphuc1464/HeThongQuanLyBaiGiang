@extends('layouts.teacherLayout')
@section('title','Giảng viên - Chi tiết bài học')
@section('tenTrang', $baiHoc->TenBaiGiang . ' / ' . $baiHoc->TenChuong . ' / ' . $baiHoc->TenBai)

@section('content')
<div class="row pe-0">
    <div class="col-md-12 pe-0">
        <div class="tile">
            <h3 class="tile-title text-primary mb-3">{{ $baiHoc->TenBai }}</h3>
            <div class="tile-body">
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <strong>Bài giảng:</strong> {{ $baiHoc->TenBaiGiang }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Chương:</strong> {{ $baiHoc->TenChuong }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Mô tả:</strong> {{ $baiHoc->MoTa ?? 'Không có' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Trạng thái:</strong>
                        @if ($baiHoc->TrangThai == 1)
                        <span class="badge bg-success">Đang hiển thị</span>
                        @else
                        <span class="badge bg-secondary">Đang ẩn</span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Ngày tạo:</strong> {{ \Carbon\Carbon::parse($baiHoc->created_at)->format('d/m/Y H:i:s') }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Ngày cập nhật:</strong> {{ \Carbon\Carbon::parse($baiHoc->updated_at)->format('d/m/Y H:i:s') }}
                    </div>
                </div>

                <!-- Nội dung -->
                <div class="mb-3">
                    <h5 class="text-primary">Nội dung bài học:</h5>
                    <div class="p-3 bg-light rounded" style="min-height: 150px;">
                        {!! $baiHoc->NoiDung !!}
                    </div>
                </div>

                <!-- File đính kèm -->
                @if(count($files) > 0)
                <div class="mb-4" id="binhLuanSection">
                    <h5 class="text-primary">Tài liệu đính kèm:</h5>
                    <div style="max-height: calc(5 * 48px); overflow-y: auto; border: 1px solid #ddd; border-radius: 4px;">
                        <ul class="list-group mb-0">
                            @foreach ($files as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center" style="height: 48px;">
                                <span class="text-truncate" style="max-width: 70%">{{ basename($file->DuongDan) }}</span>
                                <a href="{{ asset($file->DuongDan) }}" download class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Tải xuống
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif



                <!-- Hành động -->
                <div class="mt-4">
                    <a href="{{ route('giangvien.bai-giang.chuong.danh-sach', ['maBaiGiang' => $baiHoc->MaBaiGiang]) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <a href="{{ route('giangvien.bai-giang.chuong.bai.form-sua', ['maBaiGiang' => $baiHoc->MaBaiGiang, 'maChuong' => $baiHoc->MaChuong, 'maBai' => $baiHoc->MaBai]) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                </div>

            </div>
        </div>
    </div>
    <x-binh-luan :bai-giang="$baiHoc" />
</div>

<!-- Nút chuyển -->
<button id="scrollToggleBtn" class="btn btn-primary rounded-circle" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; width: 50px; height: 50px;">
    <i id="scrollToggleIcon" class="fas fa-comment"></i>
</button>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('scrollToggleBtn');
        const icon = document.getElementById('scrollToggleIcon');
        const commentSection = document.getElementById('binhLuanSection'); // ID phần bình luận

        let isAtComment = false;

        btn.addEventListener('click', function() {
            if (!isAtComment) {
                commentSection.scrollIntoView({
                    behavior: 'smooth'
                });
            } else {
                window.scrollTo({
                    top: 0
                    , behavior: 'smooth'
                });
            }
        });

        // Theo dõi vị trí cuộn để đổi biểu tượng
        window.addEventListener('scroll', function() {
            const commentTop = commentSection.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            // Nếu phần bình luận đã gần trong màn hình
            if (commentTop < windowHeight / 2) {
                isAtComment = true;
                icon.className = 'fas fa-chevron-up'; // biểu tượng lên
                btn.title = 'Quay về chi tiết';
            } else {
                isAtComment = false;
                icon.className = 'fas fa-comment'; // biểu tượng bình luận
                btn.title = 'Đi đến bình luận';
            }
        });
    });

</script>
@endsection
