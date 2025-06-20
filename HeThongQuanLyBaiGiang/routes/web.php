<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GiangVien\HomeController as GiangVienHomeController;
use App\Http\Controllers\Login;
use App\Http\Controllers\SinhVien\HomeController as SinhVienHomeController;
use App\Http\Controllers\GiangVien\SuKienZoomController;
use App\Http\Controllers\GiangVien\HocPhanController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\KhoaController;
use App\Http\Controllers\Admin\MonHocController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GiangVienController;
use App\Http\Controllers\ElfinderController;
use App\Http\Controllers\GiangVien\BaiGiangController;
use App\Http\Controllers\SinhVien\BaiGiangController as SinhVienBaiGiangController;
use App\Models\BaiGiang;
use Illuminate\Support\Facades\Auth;
use App\Models\MonHoc;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/editor', function () {
    return view('editor');
});

Route::get('/giang-vien', function () {
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
Route::get('/', [SinhVienHomeController::class, 'hienThiDanhSachBaiGiang'])->name('sinhvien.bai-giang')->middleware(['auth', RoleMiddleware::class . ':3']);
Route::get('/doi-mat-khau', [SinhVienHomeController::class, 'hienFormDoiMatKhau'])->name('sinhvien.doi-mat-khau');
Route::get('/giang-vien/doi-mat-khau', [GiangVienHomeController::class, 'hienFormDoiMatKhau'])->name('giangvien.doi-mat-khau');
Route::post('/doi-mat-khau', [AuthController::class, 'doiMatKhau'])->name('doi-mat-khau.submit');
Route::get('/giang-vien/thay-doi-thong-tin-ca-nhan', [GiangVienHomeController::class, 'hienFormThayDoiThongTin'])->name('giangvien.doi-thong-tin');
Route::post('/doi-thong-tin', [AuthController::class, 'doiThongTin'])->name('doi-thong-tin.submit');
Route::get('/thay-doi-thong-tin-ca-nhan', [SinhVienHomeController::class, 'hienFormThayDoiThongTin'])->name('sinhvien.doi-thong-tin');
Route::get('/admin/doi-mat-khau', [DashboardController::class, 'hienFormDoiMatKhau'])->name('admin.doi-mat-khau');
Route::get('/admin/thay-doi-thong-tin-ca-nhan', [DashboardController::class, 'hienFormThayDoiThongTin'])->name('admin.doi-thong-tin');

Route::prefix('admin')->name('admin.')->middleware(['auth', RoleMiddleware::class . ':1'])->group(function () {
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

    // Routes cho quản lý giảng viên
    Route::get('/giang-vien', [GiangVienController::class, 'danhSachGiangVien'])->name('giang-vien.danh-sach');
    Route::get('/giang-vien/them', [GiangVienController::class, 'hienFormThemGiangVien'])->name('giang-vien.form-them');
    Route::post('/giang-vien/them', [GiangVienController::class, 'themGiangVien'])->name('giang-vien.them');
    Route::delete('/giang-vien/xoa/{id}', [GiangVienController::class, 'xoaGiangVien'])->name('giang-vien.xoa');
    Route::post('/giang-vien/khoi-phuc/{id}', [GiangVienController::class, 'khoiPhucGiangVien'])->name('giang-vien.khoi-phuc');
    Route::get('/giang-vien/sua/{id}', [GiangVienController::class, 'hienFormSuaGiangVien'])->name('giang-vien.form-sua');
    Route::put('/giang-vien/sua/{id}', [GiangVienController::class, 'capNhatGiangVien'])->name('giang-vien.sua');
});

Route::get('/giang-vien/su-kien-zoom', [SuKienZoomController::class, 'danhSachSuKien'])->name('giangvien.su-kien-zoom.danhsach');
Route::get('/giang-vien/su-kien-zoom/chi-tiet/{id}', [SuKienZoomController::class, 'chiTietSuKien'])->name('giangvien.su-kien-zoom.chi-tiet');
Route::get('/giang-vien/su-kien-zoom/them', [SuKienZoomController::class, 'hienFormThemZoom'])->name('giangvien.su-kien-zoom.form-them');
Route::post('/giang-vien/su-kien-zoom/them', [SuKienZoomController::class, 'themSuKienZoom'])->name('giangvien.su-kien-zoom.them');
Route::delete('/giangvien/su-kien-zoom/xoa/{id}', [SuKienZoomController::class, 'xoaSuKienZoom'])->name('giangvien.su-kien-zoom.xoa');
Route::get('/giang-vien/su-kien-zoom/sua/{id}', [SuKienZoomController::class, 'hienFormCapNhatZoom'])->name('giangvien.su-kien-zoom.form-sua');
Route::put('/giang-vien/su-kien-zoom/sua/{id}', [SuKienZoomController::class, 'capNhatSuKienZoom'])->name('giangvien.su-kien-zoom.sua');

Route::get('/admin/giang-vien', [GiangVienController::class, 'danhSachGiangVien'])->name('admin.giang-vien.danh-sach');
Route::get('/admin/giang-vien/them', [GiangVienController::class, 'hienFormThemGiangVien'])->name('admin.giang-vien.form-them');
Route::post('/admin/giang-vien/them', [GiangVienController::class, 'themGiangVien'])->name('admin.giang-vien.them');
Route::delete('/admin/giang-vien/xoa/{id}', [GiangVienController::class, 'xoaGiangVien'])->name('admin.giang-vien.xoa');
Route::post('/admin/giang-vien/khoi-phuc/{id}', [GiangVienController::class, 'khoiPhucGiangVien'])->name('admin.giang-vien.khoi-phuc');
Route::get('/admin/giang-vien/sua/{id}', [GiangVienController::class, 'hienFormSuaGiangVien'])->name('admin.giang-vien.form-sua');
Route::put('/admin/giang-vien/sua/{id}', [GiangVienController::class, 'capNhatGiangVien'])->name('admin.giang-vien.sua');


Route::get('/giang-vien/hoc-phan/{id}/bai-giang', [BaiGiangController::class, 'danhSachBaiGiang'])->name('giang-vien.bai-giang');
Route::post('/giang-vien/hoc-phan/{maHocPhan}/bai-giang/{maBaiGiang}/thay-doi-trang-thai', [BaiGiangController::class, 'thayDoiTrangThai'])->name('baiGiang.thayDoiTrangThai');
Route::get('/giang-vien/hoc-phan/{id}/bai-giang/them', [BaiGiangController::class, 'hienFormThem'])->name('giang-vien.bai-giang.form-them');
Route::get('/giang-vien/hoc-phan/{maHocPhan}/bai-giang/sua/{maBaiGiang}', [BaiGiangController::class, 'hienFormSua'])->name('giang-vien.bai-giang.form-sua');
Route::post('/giang-vien/hoc-phan/{id}/bai-giang/them', [BaiGiangController::class, 'themBaiGiang'])
    ->name('giang-vien.bai-giang.them');
Route::put('/giang-vien/hoc-phan/{maHocPhan}/bai-giang/cap-nhat/{maBaiGiang}', [BaiGiangController::class, 'capNhatBaiGiang'])
    ->name('giang-vien.bai-giang.cap-nhat');
Route::get('/giang-vien/hoc-phan/{maHocPhan}/bai-giang/chi-tiet/{maBaiGiang}', [BaiGiangController::class, 'chiTietBaiGiang'])->name('giang-vien.bai-giang.chi-tiet');
Route::post('/giang-vien/hoc-phan/{maHocPhan}/bai-giang/huy', [BaiGiangController::class, 'huyBoBaiGiang'])->name('bai-giang.huy');
Route::get('/giang-vien/hoc-phan/{id}/bai-giang/thong-ke', [BaiGiangController::class, 'thongKeBaiGiang'])->name('giang-vien.bai-giang.thong-ke');
Route::get('/giang-vien/hoc-phan/{id}/bai-giang/bieu-do-thong-ke', [BaiGiangController::class, 'layDuLieuBieuDoThongKe'])->name('giang-vien.bai-giang.bieu-do-thong-ke');


Route::any('/elfinder/connector', [ElfinderController::class, 'connector'])->name('elfinder.connector');



Route::get('/giang-vien/hoc-phan', [HocPhanController::class, 'danhSach'])->name('giangvien.hocphan.danh-sach');
Route::post('/giang-vien/hoc-phan', [HocPhanController::class, 'themMoi'])->name('giangvien.hocphan.them-moi');
Route::get('/giang-vien/hoc-phan/{id}', [HocPhanController::class, 'chiTiet'])->name('giangvien.hocphan.chi-tiet');
Route::get('/giang-vien/hoc-phan/{id}/chinh-sua', [HocPhanController::class, 'chinhSua'])->name('giangvien.hocphan.chinh-sua');
Route::put('/giang-vien/hoc-phan/{id}', [HocPhanController::class, 'capNhat'])->name('giangvien.hocphan.cap-nhat');
Route::delete('/giang-vien/hoc-phan/{id}', [HocPhanController::class, 'xoa'])->name('giangvien.hocphan.xoa');
Route::get('/giang-vien/hoc-phan/mon-hoc/danh-sach', [HocPhanController::class, 'layDanhSachMonHoc'])->name('giangvien.hocphan.mon-hoc.danh-sach');




// Sinh viên
Route::get('//hoc-phan/{id}/{tab?}', [SinhVienBaiGiangController::class, 'renderTab'])
    ->name('hoc-phan.bai-giang.tab');
Route::get('/hoc-phan/{id}/bai-giang/chi-tiet/{maBaiGiang}',[SinhVienBaiGiangController::class, 'chiTietBaiGiang'])->name('bai-giang.chi-tiet');