<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GiangVien\HomeController as GiangVienHomeController;
use App\Http\Controllers\Login;
use App\Http\Controllers\SinhVien\HomeController as SinhVienHomeController;
use App\Http\Controllers\GiangVien\SuKienZoomController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Barryvdh\Elfinder\ElfinderController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\KhoaController;
use App\Http\Controllers\Admin\MonHocController;
use App\Http\Controllers\Admin\DashboardController;
Route::get('/', function () {
    return view('welcome');
});

Route::any('elfinder/connector', [ElfinderController::class, 'showConnector'])->name('elfinder.connector');

Route::get('/editor', function () {
    return view('editor');
});

Route::get('/giang-vien', function () {
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


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes cho quản lý khoa
    Route::get('/khoa', [KhoaController::class, 'danhSach'])->name('khoa.danh-sach');
    Route::post('/khoa', [KhoaController::class, 'themMoi'])->name('khoa.them-moi');
    Route::put('/khoa/{khoa}', [KhoaController::class, 'capNhat'])->name('khoa.cap-nhat');
    Route::delete('/khoa/{khoa}', [KhoaController::class, 'xoa'])->name('khoa.xoa');

    // Routes cho quản lý môn học
    Route::get('/mon-hoc', [MonHocController::class, 'danhSach'])->name('mon-hoc.danh-sach');
    Route::post('/mon-hoc', [MonHocController::class, 'themMoi'])->name('mon-hoc.them-moi');
    Route::put('/mon-hoc/{monHoc}', [MonHocController::class, 'capNhat'])->name('mon-hoc.cap-nhat');
    Route::delete('/mon-hoc/{monHoc}', [MonHocController::class, 'xoa'])->name('mon-hoc.xoa');
});


<<<<<<< HEAD
=======
Route::get('/giang-vien/su-kien-zoom', [SuKienZoomController::class, 'danhSachSuKien'])->name('giangvien.su-kien-zoom.danhsach');
>>>>>>> ffdbde21bde249125995daebe9f5d25167930f1d
