@extends('layouts.loginLayout')
@section('content')

<div class="login100-pic js-tilt" data-tilt>
    <img src="{{ asset('/img/login/dat-lai-mat-khau.png') }}" alt="ĐẶT LẠI MẬT KHẨU">
</div>
<!--=====TIÊU ĐỀ======-->
<form class="login100-form validate-form">
    <span class="login100-form-title">
        <b>ĐẶT LẠI MẬT KHẨU</b>
    </span>
    <form action="">
        <div class="wrap-input100 validate-input">
            <input autocomplete="off" class="input100" type="password" placeholder="Nhập mật khẩu mới" name="newPassword" id="newPassword">
            <span toggle="#newPassword" class="bx fa-fw bx-hide field-icon click-eye"></span>
            <span class="symbol-input100">
                <i class='bx bx-lock-open-alt'></i>
            </span>
        </div>
        <div class="wrap-input100 validate-input">
            <input autocomplete="off" class="input100" type="password" placeholder="Xác nhận mật khẩu mới" name="confirmPassword" id="confirmPassword">
            <span toggle="#confirmPassword" class="bx fa-fw bx-hide field-icon click-eye"></span>
            <span class="symbol-input100">
                <i class='bx bx-lock-alt'></i>
            </span>
        </div>
        <div class="container-login100-form-btn">
            <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
        </div>
        <div class="text-center p-t-12">
            <a class="txt2" href="{{ route('login') }}">
                Trở về trang chủ
            </a>
        </div>
    </form>
</form>


@endsection
