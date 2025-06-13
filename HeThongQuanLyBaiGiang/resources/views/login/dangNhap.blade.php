@extends('layouts.loginLayout')
@section('content')

<div class="login100-pic js-tilt" data-tilt>
    <img src="{{ asset('/img/login/trang-chu.png') }}" alt="TRANG CHỦ">
</div>
<!--=====TIÊU ĐỀ======-->
<div class="login100-form validate-form">
    <span class="login100-form-title">
        <b>ĐĂNG NHẬP HỆ THỐNG BÀI GIẢNG</b>
    </span>
    <!--=====FORM INPUT TÀI KHOẢN VÀ PASSWORD======-->
    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="wrap-input100 validate-input">
            <input class="input100" type="text" placeholder="Tên tài khoản" name="TenTaiKhoan" id="username" required>
            <span class="symbol-input100">
                <i class='bx bx-user'></i>
            </span>
        </div>
        <div class="wrap-input100 validate-input">
            <input autocomplete="off" class="input100" type="password" placeholder="Mật khẩu" name="MatKhau" id="password-field" required>
            <span toggle="#password-field" class="bx fa-fw bx-hide field-icon click-eye"></span>
            <span class="symbol-input100">
                <i class='bx bx-key'></i>
            </span>
        </div>

        <!--=====ĐĂNG NHẬP======-->
        <div class="container-login100-form-btn">
            <button type="submit" class="btn btn-primary">Đăng nhập</button>
        </div>
        <!--=====LINK TÌM MẬT KHẨU======-->
        <div class="text-right p-t-12">
            <a class="txt2" href="{{ route('forgot') }}">
                Bạn quên mật khẩu?
            </a>
        </div>
    </form>
</div>

@endsection
