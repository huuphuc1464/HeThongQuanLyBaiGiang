<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Login;
use App\Http\Controllers\SinhVien\HomeController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Barryvdh\Elfinder\ElfinderController;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::any('elfinder/connector', [ElfinderController::class, 'showConnector'])->name('elfinder.connector');

Route::get('/editor', function () {
    return view('editor');
});

Route::get('/teacher', function () {
    return view('layouts.teacherLayout');
})->middleware(['auth', RoleMiddleware::class . ':2']);

Route::get('/dang-nhap', [AuthController::class, 'hienThiFormLogin'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'dangNhap'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/xac-nhan-otp', [AuthController::class, 'hienThiFormXacNhanOTP'])->name('otp.form');
Route::post('/xac-nhan-otp', [AuthController::class, 'xacNhanOTP'])->name('otp.xacNhan');
Route::get('/khoi-phuc-mat-khau', [AuthController::class, 'hienThiFormKhoiPhuc'])->name('forgot');
Route::post('/khoi-phuc-mat-khau/gui-otp', [AuthController::class, 'guiOtpKhoiPhuc'])->name('forgot.sendOtp');
Route::get('/dat-lai-mat-khau', [AuthController::class, 'hienThiFormDatLaiMatKhau'])->name('resetPass.form');
Route::post('/dat-lai-mat-khau', [AuthController::class, 'datLaiMatKhau'])->name('resetPass.submit');
Route::get('/doi-mat-khau-lan-dau-dang-nhap', [AuthController::class, 'hienThiFormDoiMatKhauLanDau'])->name('changePassFirst.form');
Route::post('/doi-mat-khau-lan-dau-dang-nhap', [AuthController::class, 'doiMatKhauLanDau'])->name('changePassFirst.submit');

Route::get('/', [HomeController::class, 'hienThiDanhSachBaiGiang'])->name('sinhvien.bai-giang')->middleware(['auth', RoleMiddleware::class . ':3']);




Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin');
    Route::get('/quan-ly-khoa', function () {
        return view('admin.quanLyKhoa');
    })->name('quanLyKhoa');
    Route::get('/quan-ly-mon-hoc', function () {
        return view('admin.quanLyMonHoc');
    })->name('quanLyMonHoc');
    Route::get('/quan-ly-giang-vien', function () {
        return view('admin.quanLyGiangVien');
    })->name('quanLyGiangVien');
    Route::get('/quan-ly-sinh-vien', function () {
        return view('admin.quanLySinhVien');
    })->name('quanLySinhVien');
});