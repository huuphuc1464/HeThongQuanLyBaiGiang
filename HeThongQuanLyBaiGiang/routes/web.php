<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Login;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Barryvdh\Elfinder\ElfinderController;

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

Route::get('/', function () {
    return view('layouts.studentLayout');
});

Route::get('/dang-nhap', [AuthController::class, 'hienThiFormLogin'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'dangNhap'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/xac-nhan-otp', [AuthController::class, 'xacNhanOTP'])->name('OTP');
Route::get('/khoi-phuc-mat-khau', [AuthController::class, 'hienThiFormKhoiPhuc'])->name('forgot');
Route::get('/dat-lai-mat-khau', [AuthController::class, 'hienThiFormDatLaiMatKhau'])->name('reset');




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