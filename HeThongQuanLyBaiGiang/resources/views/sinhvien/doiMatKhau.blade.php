@extends('layouts.studentLayout')

@section('title', 'Đổi mật khẩu')

@section('sidebar')
@include('layouts.sidebarTrangChu', ['danhSachLopHocPhanSidebar' => $danhSachLopHocPhanSidebar])
@endsection

@section('content')
@include('components.doiMatKhau')
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('./css/doiMatKhau.css') }}">
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(".click-eye").click(function() {
        const $icon = $(this);
        const $input = $($icon.attr("toggle"));

        const isPassword = $input.attr("type") === "password";
        $input.attr("type", isPassword ? "text" : "password");

        // Đổi icon mắt (hiện/ẩn)
        $icon.toggleClass("fa-eye fa-eye-slash");
    });

</script>
@endsection
