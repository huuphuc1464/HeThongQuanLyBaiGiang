<?php

namespace App\Providers;

use App\View\Composers\DanhSachKhoaSinhVien;
use App\View\Composers\LayoutSinhVien;
use App\View\Composers\SidebarTrangChuSinhVien;
use App\View\Composers\ThongBaoSinhVien;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $user = Auth::user();

            if ($user) {
                $view->with('tenNguoiDung', $user->HoTen)
                    ->with('maNguoiDung', $user->MaNguoiDung);
            }
        });

        View::composer(['sinhvien.trangChu', 'layouts.sidebarTrangChu', 'sinhvien.doiMatKhau', 'sinhvien.thayDoiThongTinCaNhan'], SidebarTrangChuSinhVien::class);
        View::composer('layouts.studentLayout', ThongBaoSinhVien::class);
        View::composer('layouts.studentLayout', DanhSachKhoaSinhVien::class);
    }
}
