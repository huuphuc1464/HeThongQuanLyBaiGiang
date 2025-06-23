@extends('layouts.lopHocPhanLayout')

@section('title', $baiGiang->TenBaiGiang)

@section('tab-content')
<div class="container-fluid card shadow-sm mb-4">
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title text-primary my-3 text-center">{{ $baiGiang->TenBaiGiang }}</h3>
                <div class="tile-body">
                    <div class="row mb-3">
                        <div class="col-md-5 mb-2 ms-2">
                            <strong>Chương:</strong> {{ $baiGiang->TenChuong }}
                        </div>
                        <div class="col-md-6 mb-2 ms-2">
                            <strong>Bài:</strong> {{ $baiGiang->TenBai }}
                        </div>
                        <div class="col-md-5 mb-2 ms-2">
                            <strong>Mô tả:</strong> {{ $baiGiang->MoTa ?? 'Không có' }}
                        </div>
                        <div class="col-md-6 mb-2 ms-2">
                            <strong>Ngày tạo:</strong> {{ \Carbon\Carbon::parse($baiGiang->created_at)->format('d/m/Y H:i:s') }}
                        </div>
                        <div class="col-md-5 mb-2 ms-2">
                            <strong>Ngày cập nhật:</strong> {{ \Carbon\Carbon::parse($baiGiang->updated_at)->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                    <hr>
                    {{-- Nội dung --}}
                    <div class="mb3">
                        <h5 class="text-primary">Nội dung bài giảng:</h5>
                        <div class="p-3 bg-light rounded" style="min-height: 150px;">
                            {!! $baiGiang->NoiDung !!}
                        </div>
                    </div>

                    {{-- File đính kèm --}}
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
                </div>
            </div>
        </div>
    </div>
</div>
<x-binh-luan :bai-giang="$baiGiang" />

@endsection
