@extends('layouts.studentLayout')

@section('sidebar')
@include('layouts.sidebarLopHocPhan', ['danhSachBaiGiangSidebar' => $danhSachBaiGiangSidebar])
@endsection

@section('content')
<div class="content-area" id="mainContent" tabindex="-1">
    <ul class="nav nav-tabs justify-content-left custom-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $tab == 'bai-giang' ? 'active' : '' }}"
                href="{{ route('bai-giang.bai.tab', ['id' => $id, 'tab' => 'bai-giang']) }}">Bài giảng</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $tab == 'bai-kiem-tra' ? 'active' : '' }}"
                href="{{ route('bai-giang.bai.tab', ['id' => $id, 'tab' => 'bai-kiem-tra']) }}">Bài kiểm tra</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $tab == 'su-kien-zoom' ? 'active' : '' }}"
                href="{{ route('bai-giang.bai.tab', ['id' => $id, 'tab' => 'su-kien-zoom']) }}">Sự kiện Zoom</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $tab == 'moi-nguoi' ? 'active' : '' }}"
                href="{{ route('bai-giang.bai.tab', ['id' => $id, 'tab' => 'moi-nguoi']) }}">Mọi người</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="tab-content">
            @yield('tab-content')
        </div>
    </div>
</div>
@endsection