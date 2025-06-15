@extends('layouts.studentLayout')
@section('title','Giảng viên - Thay đổi thông tin cá nhân')

@section('sidebar')
@include('layouts.sidebarTrangChu', ['danhSachLopHocPhanSidebar' => $danhSachLopHocPhanSidebar])
@endsection

@section('content')
@include('components.thayDoiThongTinCaNhan')
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('./css/doiThongTinCaNhan.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('./js/doiThongTinCaNhan.js') }}"></script>
@endsection
