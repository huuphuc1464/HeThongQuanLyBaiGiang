@extends('layouts.lopHocPhanLayout')

@section('title','Danh sách bài giảng')

@section('tab-content')
<div class="container-fluid my-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="accordion" id="chuong">
                <div class="mb-4">
                    <h3 class="fw-bold">Tên lớp học phần: {{ $lopHocPhan->TenLopHocPhan }}</h3>
                    <p>Tên học phần: {{ $hocPhan->TenHocPhan }}.</p>
                    <p>Mô tả lớp học phần: {{ $lopHocPhan->MoTa ?? 'Không có mô tả' }}</p>
                    <p>Người tạo: {{ $giangVien->HoTen ?? 'Không rõ' }}</p>
                    <h5 class="fw-bold mt-4">Nội dung bài giảng</h5>
                    <p>
                        {{ $baiGiangs->count() }} chương &nbsp;&bull;&nbsp;
                        {{ $baiGiangs->flatMap(fn($bai) => $bai)->count() }} bài học
                    </p>
                </div>
                @foreach ($baiGiangs as $tenChuong => $cacBai)
                @php
                $chuongId = Str::slug($tenChuong) . '-' . uniqid();
                $isFirstChuong = $loop->first ? 'show' : '';
                @endphp
                <div class="accordion-item border">
                    <h2 class="accordion-header" id="heading-{{ $chuongId }}">
                        <button class="accordion-button collapsed fw-bold text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $chuongId }}" aria-expanded="false" aria-controls="collapse-{{ $chuongId }}">
                            {{ $tenChuong }}
                        </button>
                    </h2>
                    <div id="collapse-{{ $chuongId }}" class="accordion-collapse collapse {{ $isFirstChuong }}" aria-labelledby="heading-{{ $chuongId }}" data-bs-parent="#chuong">
                        <div class="accordion-body">
                            <div class="accordion" id="bai-{{ $chuongId }}">
                                @foreach ($cacBai as $key => $baiGiangs)
                                @php
                                $parts = explode('|', $key);
                                $tenBai = $parts[0];
                                $maBaiGiang = $parts[1] ?? null;
                                $baiId = Str::slug($tenBai) . '-' . uniqid();
                                @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $baiId }}">
                                        <button class="accordion-button collapsed fw-bold text-dark bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $baiId }}" aria-expanded="false" aria-controls="collapse-{{ $baiId }}">
                                            {{ $tenBai }}
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $baiId }}" class="accordion-collapse collapse show" aria-labelledby="heading-{{ $baiId }}" data-bs-parent="#bai-{{ $chuongId }}">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                @foreach ($baiGiangs as $baiGiang)
                                                <li class="list-group-item p-0">
                                                    <a href="{{ route('bai-giang.chi-tiet', ['id' => $id, 'maBaiGiang' => $baiGiang->MaBaiGiang]) }}" class="d-flex justify-content-between align-items-center px-3 py-2 text-decoration-none text-dark w-100 h-100">
                                                        <div>
                                                            <span>{{ $baiGiang->TenBaiGiang }}</span><br>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-muted">
                                                                <strong>Cập nhật:</strong>
                                                                {{ \Carbon\Carbon::parse($baiGiang->updated_at)->format('H:i:s d/m/Y') }}
                                                            </span>
                                                        </div>
                                                    </a>
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
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
