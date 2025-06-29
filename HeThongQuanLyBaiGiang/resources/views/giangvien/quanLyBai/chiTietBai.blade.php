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
                <div class="mb-4">
                    <h5 class="text-primary">Tài liệu đính kèm:</h5>
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
    {{-- <x-binh-luan :bai-giang="$baiHoc" /> --}}
</div>


@endsection
