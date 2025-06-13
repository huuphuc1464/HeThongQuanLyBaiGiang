@extends('layouts.loginLayout')
@section('content')

<div class="login100-pic js-tilt" data-tilt>
    <img src="{{ asset('/img/login/otp.png') }}" alt="OTP">
</div>
<!--=====TIÊU ĐỀ======-->
<div class="login100-form validate-form">
    <span class="login100-form-title">
        <b>XÁC NHẬN OTP</b>
    </span>
    <form method="POST" action="{{ route('otp.xacNhan') }}">
        @csrf
        <input type="hidden" name="email" value="{{ request('email') }}">
        <div class="wrap-input100 validate-input">
            <input class="input100" type="text" placeholder="Nhập OTP khôi phục mật khẩu" name="otp" id="otp" value="" />
            <span class="symbol-input100">
                <i class='bx bx-check-shield'></i>
            </span>
        </div>
        <div class="container-login100-form-btn">
            <button type="submit" class="btn btn-primary">Xác nhận OTP</button>
        </div>

        <div class="text-center p-t-12">
            <a class="txt2" href="{{ route('forgot') }}">
                Trở về quên mật khẩu
            </a>
        </div>
    </form>
</div>

@endsection
