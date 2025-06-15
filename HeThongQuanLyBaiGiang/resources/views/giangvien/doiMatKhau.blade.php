@extends('layouts.teacherLayout')
@section('title','Giảng viên - Đổi mật khẩu')
@section('tenTrang', 'Đổi mật khẩu')
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
