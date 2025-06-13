@extends('layouts.loginLayout')
@section('content')
<div class="login100-pic js-tilt" data-tilt>
    <img src="{{ asset('img/login/khoi-phuc-mat-khau.png') }}" alt="KHÔI PHỤC MẬT KHẨU">
</div>
<div class="login100-form validate-form">
    <span class="login100-form-title">
        <b>KHÔI PHỤC MẬT KHẨU</b>
    </span>
    <form action="{{ route('forgot.sendOtp') }}" method="POST">
        @csrf
        <div class="wrap-input100 validate-input" data-validate="Bạn cần nhập đúng thông tin như: ex@abc.xyz">
            <input class="input100" type="text" placeholder="Nhập email" name="email" id="emailInput" value="" />

            <span class="symbol-input100">
                <i class='bx bx-mail-send'></i>
            </span>
        </div>
        <div class="container-login100-form-btn">
            <button type="submit" class="btn btn-primary">Lấy mã OTP</button>
        </div>

        <div class="text-center p-t-12">
            <a class="txt2" href="{{ route('login') }}">
                Trở về đăng nhập
            </a>
        </div>
    </form>
</div>

@endsection
