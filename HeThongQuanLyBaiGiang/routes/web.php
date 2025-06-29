<?php

use App\Http\Controllers\GiangVien\BaiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GiangVien\HomeController as GiangVienHomeController;
use App\Http\Controllers\SinhVien\HomeController as SinhVienHomeController;
use App\Http\Controllers\GiangVien\SuKienZoomController;
use App\Http\Controllers\GiangVien\HocPhanController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\KhoaController;
use App\Http\Controllers\Admin\MonHocController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GiangVienController;
use App\Http\Controllers\Shared\BinhLuanBaiGiangController;
use App\Http\Controllers\GiangVien\BaiKiemTraController;
use App\Http\Controllers\ElfinderController;
use App\Http\Controllers\GiangVien\BaiGiangController;
use App\Http\Controllers\GiangVien\ChuongController;
use App\Http\Controllers\GiangVien\SinhVienController;
use App\Http\Controllers\GiangVien\LopHocPhanController;
use App\Http\Controllers\SinhVien\KetQuaBaiKiemTraController;
use App\Models\Bai;

Route::prefix('auth')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'hienThiFormLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'dangNhap'])->name('login.submit');
    Route::post('/dang-xuat', [AuthController::class, 'dangXuat'])->name('logout');
    Route::post('/xac-nhan-otp', [AuthController::class, 'xacNhanOTP'])->name('otp.xacNhan');
    Route::get('/xac-nhan-otp', [AuthController::class, 'hienThiFormXacNhanOTP'])->name('otp.form');
    Route::get('/khoi-phuc-mat-khau', [AuthController::class, 'hienThiFormKhoiPhuc'])->name('forgot');
    Route::post('/khoi-phuc-mat-khau/gui-otp', [AuthController::class, 'guiOtpKhoiPhuc'])->name('forgot.sendOtp');
    Route::post('/dat-lai-mat-khau', [AuthController::class, 'datLaiMatKhau'])->name('resetPass.submit');
    Route::get('/dat-lai-mat-khau', [AuthController::class, 'hienThiFormDatLaiMatKhau'])->name('resetPass.form');
    Route::post('/doi-mat-khau-lan-dau-dang-nhap', [AuthController::class, 'doiMatKhauLanDau'])->name('changePassFirst.submit');
    Route::get('/doi-mat-khau-lan-dau-dang-nhap', [AuthController::class, 'hienThiFormDoiMatKhauLanDau'])->name('changePassFirst.form');
});

Route::any('/elfinder/connector', [ElfinderController::class, 'connector'])->name('elfinder.connector');

Route::middleware(['auth', RoleMiddleware::class . ':1,2,3'])->group(function () {
    Route::post('/doi-mat-khau', [AuthController::class, 'doiMatKhau'])->name('doi-mat-khau.submit');
    Route::post('/doi-thong-tin', [AuthController::class, 'doiThongTin'])->name('doi-thong-tin.submit');
});

Route::prefix('binh-luan')->name('binhluan.')->middleware(['auth', RoleMiddleware::class . ':2,3'])->group(function () {
    // Bình luận
    Route::post('/gui-binh-luan', [BinhLuanBaiGiangController::class, 'guiBinhLuan'])->name('guibinhluan');
    Route::post('/tra-loi-binh-luan', [BinhLuanBaiGiangController::class, 'traLoiBinhLuan'])->name('traloi');
    Route::delete('/xoa/{id}', [BinhLuanBaiGiangController::class, 'xoa'])->name('xoa');
    Route::put('/cap-nhat', [BinhLuanBaiGiangController::class, 'capNhat'])->name('capnhat');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', RoleMiddleware::class . ':1'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/doi-mat-khau', [DashboardController::class, 'hienFormDoiMatKhau'])->name('doi-mat-khau');
    Route::get('/thay-doi-thong-tin-ca-nhan', [DashboardController::class, 'hienFormThayDoiThongTin'])->name('doi-thong-tin');

    // Routes cho quản lý khoa
    Route::prefix('khoa')->name('khoa.')->group(function () {
        Route::get('/', [KhoaController::class, 'danhSach'])->name('danh-sach');
        Route::post('/', [KhoaController::class, 'themMoi'])->name('them-moi');
        Route::put('/{khoa}', [KhoaController::class, 'capNhat'])->name('cap-nhat');
        Route::delete('/{khoa}', [KhoaController::class, 'xoa'])->name('xoa');
    });

    // Routes cho quản lý giảng viên
    Route::prefix('giang-vien')->name('giang-vien.')->group(function () {
        Route::get('/', [GiangVienController::class, 'danhSachGiangVien'])->name('danh-sach');
        Route::get('/them', [GiangVienController::class, 'hienFormThemGiangVien'])->name('form-them');
        Route::post('/them', [GiangVienController::class, 'themGiangVien'])->name('them');
        Route::delete('/xoa/{id}', [GiangVienController::class, 'xoaGiangVien'])->name('xoa');
        Route::post('/khoi-phuc/{id}', [GiangVienController::class, 'khoiPhucGiangVien'])->name('khoi-phuc');
        Route::get('/sua/{id}', [GiangVienController::class, 'hienFormSuaGiangVien'])->name('form-sua');
        Route::put('/sua/{id}', [GiangVienController::class, 'capNhatGiangVien'])->name('sua');
    });
});

Route::prefix('giang-vien')->name('giangvien.')->middleware(['auth', RoleMiddleware::class . ':2'])->group(function () {
    Route::get('/', [GiangVienHomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/bieu-do-thong-ke', [BaiGiangController::class, 'layDuLieuBieuDoThongKe'])->name('bieu-do-thong-ke');
    Route::get('/doi-mat-khau', [GiangVienHomeController::class, 'hienFormDoiMatKhau'])->name('doi-mat-khau');
    Route::get('/thay-doi-thong-tin-ca-nhan', [GiangVienHomeController::class, 'hienFormThayDoiThongTin'])->name('doi-thong-tin');

    // Quản lý sự kiện Zoom
    Route::prefix('su-kien-zoom')->name('su-kien-zoom.')->group(function () {
        Route::get('/', [SuKienZoomController::class, 'danhSachSuKien'])->name('danhsach');
        Route::get('/chi-tiet/{id}', [SuKienZoomController::class, 'chiTietSuKien'])->name('chi-tiet');
        Route::get('/them', [SuKienZoomController::class, 'hienFormThemZoom'])->name('form-them');
        Route::post('/them', [SuKienZoomController::class, 'themSuKienZoom'])->name('them');
        Route::delete('/xoa/{id}', [SuKienZoomController::class, 'xoaSuKienZoom'])->name('xoa');
        Route::get('/sua/{id}', [SuKienZoomController::class, 'hienFormCapNhatZoom'])->name('form-sua');
        Route::put('/sua/{id}', [SuKienZoomController::class, 'capNhatSuKienZoom'])->name('sua');
    });



    // Quản lý bài giảng
    Route::prefix('bai-giang')->name('bai-giang.')->group(function () {
        Route::get('/', [BaiGiangController::class, 'danhSachBaiGiang'])->name('danh-sach');
        Route::post('/them', [BaiGiangController::class, 'themBaiGiang'])->name('them');
        Route::put('/cap-nhat/{id}', [BaiGiangController::class, 'capNhatBaiGiang'])->name('cap-nhat');
        Route::delete('/xoa/{id}', [BaiGiangController::class, 'xoaBaiGiang'])->name('xoa');
        Route::post('/khoi-phuc/{id}', [BaiGiangController::class, 'khoiPhucBaiGiang'])->name('khoi-phuc');

        Route::prefix('{maBaiGiang}')->group(function () {
            // Quản lý chương
            Route::prefix('chuong')->name('chuong.')->group(function () {
                Route::get('/', [ChuongController::class, 'danhSach'])->name('danh-sach');
                Route::post('/them', [ChuongController::class, 'themChuong'])->name('them');
                Route::get('/thong-tin/{maChuong}', [ChuongController::class, 'layThongTinChuong'])->name('form-sua');
                Route::put('/cap-nhat/{maChuong}', [ChuongController::class, 'capNhatChuong'])->name('cap-nhat');
                Route::post('/doi-trang-thai/{maChuong}', [ChuongController::class, 'doiTrangThaiChuong'])->name('doi-trang-thai');

                // Quản lý bài
                Route::prefix('{maChuong}/bai')->name('bai.')->group(function () {
                    Route::get('/them', [BaiController::class, 'hienFormThemBai'])->name('form-them');
                    Route::post('/them', [BaiController::class, 'themBai'])->name('them');
                    Route::post('/huy', [BaiController::class, 'huyBoBai'])->name('huy');
                    Route::get('/cap-nhat/{maBai}', [BaiController::class, 'hienFormSuaBai'])->name('form-sua');
                    Route::put('/cap-nhat/{maBai}', [BaiController::class, 'capNhatBai'])->name('cap-nhat');
                    Route::post('/doi-trang-thai/{maBai}', [BaiController::class, 'doiTrangThaiBai'])->name('doi-trang-thai');
                    Route::get('/{maBai}', [BaiController::class, 'chiTietBai'])->name('chi-tiet');
                });
            });
        });
    });

    // Quản lý lớp học phần
    Route::prefix('lop-hoc-phan')->name('lophocphan.')->group(function () {
        Route::get('/', [LopHocPhanController::class, 'danhSach'])->name('danhsach');
        Route::post('/', [LopHocPhanController::class, 'themMoi'])->name('them-moi');
        Route::get('/{id}', [LopHocPhanController::class, 'chiTiet'])->name('chi-tiet');
        Route::get('/{id}/chinh-sua', [LopHocPhanController::class, 'chinhSua'])->name('chinh-sua');
        Route::put('/{id}', [LopHocPhanController::class, 'capNhat'])->name('cap-nhat');
        Route::delete('/{id}', [LopHocPhanController::class, 'xoa'])->name('xoa');

        // Quản lý sinh viên
        Route::prefix('{maLopHocPhan}/sinh-vien')->name('sinhvien.')->group(function () {
            Route::get('/', [SinhVienController::class, 'danhSachSinhVien'])->name('danhsach');
            Route::delete('/xoa/{maDanhSachLop}', [SinhVienController::class, 'xoaSinhVien'])->name('xoa');
            Route::post('/them', [SinhVienController::class, 'themSinhVien'])->name('them-bang-email');
            Route::post('/them-file', [SinhVienController::class, 'themSinhVienBangFile'])->name('them-bang-file');
        });
    });

    // Quản lý bài kiểm tra
    Route::prefix('bai-kiem-tra')->name('bai-kiem-tra.')->group(function () {
        Route::get('/', [BaiKiemTraController::class, 'danhSachBaiKiemTra'])->name('danh-sach');
        Route::get('them', [BaiKiemTraController::class, 'hienFormThemBaiKiemTra'])->name('form-them');
        Route::post('them', [BaiKiemTraController::class, 'themBaiKiemTra'])->name('them');
        Route::post('import', [BaiKiemTraController::class, 'importBaiKiemTra'])->name('import');
        Route::post('nhan-ban', [BaiKiemTraController::class, 'nhanBanBaiKiemTra'])->name('nhan-ban');
        Route::get('chi-tiet/{id}', [BaiKiemTraController::class, 'chiTietBaiKiemTra'])->name('chi-tiet');
        Route::get('sua/{id}', [BaiKiemTraController::class, 'hienFormSuaBaiKiemTra'])->name('form-sua');
        Route::put('sua/{id}', [BaiKiemTraController::class, 'capNhatBaiKiemTra'])->name('sua');
        Route::delete('xoa/{id}', [BaiKiemTraController::class, 'xoaBaiKiemTra'])->name('xoa');
        Route::get('xuat-bai-kiem-tra/{id}', [BaiKiemTraController::class, 'xuatBaiKiemTra'])->name('xuat-bai-kiem-tra');
        Route::get('xuat-ket-qua/{id}', [BaiKiemTraController::class, 'xuatKetQuaBaiLam'])->name('xuat-ket-qua');
    });
});

Route::middleware(['auth', RoleMiddleware::class . ':3'])->group(function () {
    Route::get('/', [SinhVienHomeController::class, 'hienThiDanhSachBaiGiang'])->name('trang-chu');
    Route::get('/doi-mat-khau', [SinhVienHomeController::class, 'hienFormDoiMatKhau'])->name('sinhvien.doi-mat-khau');
    Route::get('/thay-doi-thong-tin-ca-nhan', [SinhVienHomeController::class, 'hienFormThayDoiThongTin'])->name('sinhvien.doi-thong-tin');

    // Routes cho sinh viên làm bài kiểm tra
    Route::prefix('bai-kiem-tra')->group(function () {
        Route::get('/', [KetQuaBaiKiemTraController::class, 'danhSachBaiKiemTra'])->name('danh-sach-bai-kiem-tra');
        Route::get('/{maBaiKiemTra}/lam-bai', [KetQuaBaiKiemTraController::class, 'lamBaiKiemTra'])->name('lam-bai-kiem-tra');
        Route::post('/{maBaiKiemTra}/nop-bai', [KetQuaBaiKiemTraController::class, 'nopBaiKiemTra'])->name('nop-bai-kiem-tra');
        Route::get('/{maBaiKiemTra}/ket-qua', [KetQuaBaiKiemTraController::class, 'ketQuaBaiKiemTra'])->name('ket-qua-bai-kiem-tra');
    });

    Route::prefix('hoc-phan/{id}')->group(function () {
        Route::get('/{tab?}', [SinhVienHomeController::class, 'renderTab'])->name('hoc-phan.bai-giang.tab');
        Route::get('/bai-giang/chi-tiet/{maBaiGiang}', [SinhVienHomeController::class, 'chiTietBaiGiang'])->name('bai-giang.chi-tiet');
        Route::get('/su-kien-zoom/chi-tiet/{maSuKien}', [SinhVienHomeController::class, 'chiTietSuKienZoom'])->name('su-kien-zoom.chi-tiet');
    });

    Route::get('/xac-nhan-tham-gia-lop/{maLopHocPhan}/{maXacNhan}', [SinhVienController::class, 'xacNhanThamGiaLop'])->name('xac-nhan-tham-gia-lop');
});