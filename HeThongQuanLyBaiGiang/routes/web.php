<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Login;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Barryvdh\Elfinder\ElfinderController;
use App\Http\Controllers\Admin\KhoaController;
use App\Http\Controllers\Admin\MonHocController;
use App\Http\Controllers\Admin\GiangVienController;
use App\Http\Controllers\Admin\SinhVienController;
use App\Http\Controllers\Admin\DashboardController;

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

Route::get('/', function () {
    return view('layouts.studentLayout');
});

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


Route::prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin');
    Route::get('/quan-ly-khoa', [KhoaController::class, 'danhSach'])->name('admin.quan-ly-khoa.danh-sach');
    Route::post('/quan-ly-khoa', [KhoaController::class, 'themMoi'])->name('admin.quan-ly-khoa.them-moi');
    Route::put('/quan-ly-khoa/{khoa}', [KhoaController::class, 'capNhat'])->name('admin.quan-ly-khoa.cap-nhat');
    Route::delete('/quan-ly-khoa/{khoa}', [KhoaController::class, 'xoa'])->name('admin.quan-ly-khoa.xoa');
    Route::get('/quan-ly-mon-hoc', [MonHocController::class, 'danhSach'])->name('admin.quan-ly-mon-hoc.danh-sach');
    Route::get('/quan-ly-giang-vien', [GiangVienController::class, 'danhSach'])->name('admin.quan-ly-giang-vien.danh-sach');
    Route::post('/quan-ly-giang-vien', [GiangVienController::class, 'themMoi'])->name('admin.quan-ly-giang-vien.them-moi');
    Route::put('/quan-ly-giang-vien/{giangVien}', [GiangVienController::class, 'capNhat'])->name('admin.quan-ly-giang-vien.cap-nhat');
    Route::delete('/quan-ly-giang-vien/{giangVien}', [GiangVienController::class, 'xoa'])->name('admin.quan-ly-giang-vien.xoa');
    Route::get('/quan-ly-sinh-vien', [SinhVienController::class, 'danhSach'])->name('admin.quan-ly-sinh-vien.danh-sach');
    Route::post('/quan-ly-sinh-vien', [SinhVienController::class, 'themMoi'])->name('admin.quan-ly-sinh-vien.them-moi');
    Route::put('/quan-ly-sinh-vien/{sinhVien}', [SinhVienController::class, 'capNhat'])->name('admin.quan-ly-sinh-vien.cap-nhat');
    Route::delete('/quan-ly-sinh-vien/{sinhVien}', [SinhVienController::class, 'xoa'])->name('admin.quan-ly-sinh-vien.xoa');
});