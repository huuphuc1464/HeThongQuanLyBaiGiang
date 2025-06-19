@extends('layouts.teacherLayout')
@section('title','Chi tiết bài giảng')
@section('tenTrang', $hocPhan->TenHocPhan . ' / Chi tiết bài giảng / ' . $baiGiang->TenBaiGiang)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title text-primary mb-3">{{ $baiGiang->TenBaiGiang }}</h3>
            <div class="tile-body">
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <strong>Chương:</strong> {{ $baiGiang->TenChuong }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Bài:</strong> {{ $baiGiang->TenBai }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Mô tả:</strong> {{ $baiGiang->MoTa ?? 'Không có' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Trạng thái:</strong>
                        @if ($baiGiang->TrangThai == 1)
                        <span class="badge bg-success">Đang hiển thị</span>
                        @else
                        <span class="badge bg-secondary">Đang ẩn / xóa</span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Ngày tạo:</strong> {{ \Carbon\Carbon::parse($baiGiang->created_at)->format('d/m/Y H:i') }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Ngày cập nhật:</strong> {{ \Carbon\Carbon::parse($baiGiang->updated_at)->format('d/m/Y H:i') }}
                    </div>
                </div>

                <!-- Nội dung -->
                <div class="mb3">
                    <h5 class="text-primary">Nội dung bài giảng:</h5>
                    <div class="p-3 bg-light rounded" style="min-height: 150px;">
                        {!! $baiGiang->NoiDung !!}
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
                    <a href="{{ route('giang-vien.bai-giang', ['id' => $hocPhan->MaHocPhan]) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <a href="{{ route('giang-vien.bai-giang.form-sua', ['maHocPhan' => $hocPhan->MaHocPhan, 'maBaiGiang' => $baiGiang->MaBaiGiang]) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
