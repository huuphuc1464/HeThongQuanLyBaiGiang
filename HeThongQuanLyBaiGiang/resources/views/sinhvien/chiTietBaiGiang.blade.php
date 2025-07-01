@extends('layouts.lopHocPhanLayout')

@section('title', $bai->TenBai)

@section('tab-content')
<div class="container-fluid card shadow-sm mb-4">
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title text-primary my-3 text-center">{{ $bai->TenBai }}</h3>
                <div class="tile-body">
                    <div class="row mb-3">
                        <div class="col-md-5 mb-2 ms-2">
                            <strong>Bài giảng:</strong> {{ $bai->TenBaiGiangCha }}
                        </div>
                        <div class="col-md-6 mb-2 ms-2">
                            <strong>Chương:</strong> {{ $bai->TenChuong }}
                        </div>
                        <div class="col-md-5 mb-2 ms-2">
                            <strong>Mô tả:</strong> {{ $bai->MoTa ?? 'Không có' }}
                        </div>
                        <div class="col-md-6 mb-2 ms-2">
                            <strong>Ngày tạo:</strong> {{ \Carbon\Carbon::parse($bai->created_at)->format('d/m/Y H:i:s')
                            }}
                        </div>
                        <div class="col-md-5 mb-2 ms-2">
                            <strong>Ngày cập nhật:</strong> {{ \Carbon\Carbon::parse($bai->updated_at)->format('d/m/Y
                            H:i:s') }}
                        </div>
                    </div>
                    <hr>
                    {{-- Nội dung --}}
                    <div class="mb3">
                        <h5 class="text-primary">Nội dung bài giảng:</h5>
                        <div class="p-3 bg-light rounded" style="min-height: 150px;">
                            {!! $bai->NoiDung !!}
                        </div>
                    </div>

                    {{-- File đính kèm --}}
                    @if(count($files) > 0)
                    <div class="mb-4">
                        <h5 class="text-primary">Tài liệu đính kèm:</h5>
                        <div div style="max-height: calc(5 * 48px); overflow-y: auto; border: 1px solid #ddd; border-radius: 4px;">

                            <ul class="list-group">
                                @foreach ($files as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ basename($file->DuongDan) }}</span>
                                    <a href="{{ asset($file->DuongDan) }}" download class="btn btn-sm btn-info">
                                        <i class="fas fa-download"></i> Tải xuống
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div id="binhLuanSection">
    <x-binh-luan :bai-giang="$bai" />
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
        const commentSection = document.getElementById('binhLuanSection');
        if (!btn || !icon || !commentSection) return;

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

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        isAtComment = true;
                        icon.className = 'fas fa-chevron-up';
                        btn.title = 'Quay về chi tiết';
                    } else {
                        isAtComment = false;
                        icon.className = 'fas fa-comment';
                        btn.title = 'Đi đến bình luận';
                    }
                });
            }, {
                threshold: 0.5
            }
        );

        observer.observe(commentSection);
    });

</script>

    
    
@endsection
