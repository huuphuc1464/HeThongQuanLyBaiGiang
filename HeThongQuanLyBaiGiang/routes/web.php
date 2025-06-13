<?php

use App\Http\Controllers\Login;
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
});

Route::get('/', function () {
    return view('layouts.studentLayout');
});

Route::get('/dang-nhap', function () {
    return view('login.dangNhap');
})->name('login');

Route::get('/khoi-phuc-mat-khau', function () {
    return view('login.khoiPhucMatKhau');
})->name('forgot');

Route::get('/xac-nhan-otp', function () {
    return view('login.xacNhanOTP');
})->name('OTP');

Route::get('/dat-lai-mat-khau', function () {
    return view('login.datLaiMatKhau');
})->name('reset');




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