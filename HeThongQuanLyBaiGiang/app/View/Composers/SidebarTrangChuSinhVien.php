<?php

namespace App\View\Composers;

use App\Models\LopHocPhan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SidebarTrangChuSinhVien
{
    public function compose(View $view)
    {
        $sinhVien = Auth::user();

        $danhSachLopHocPhanSidebar = [];

        if ($sinhVien) {
            $danhSachLopHocPhanSidebar = LopHocPhan::select(
                'lop_hoc_phan.TenLopHocPhan',
                'lop_hoc_phan.MoTa',
                'lop_hoc_phan.MaLopHocPhan',
                'hoc_phan.AnhHocPhan'
            )
                ->join('hoc_phan', 'lop_hoc_phan.MaHocPhan', '=', 'hoc_phan.MaHocPhan')
                ->join('danh_sach_lop', 'danh_sach_lop.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
                ->where('lop_hoc_phan.TrangThai', 1)
                ->where('hoc_phan.TrangThai', 1)
                ->where('danh_sach_lop.MaSinhVien', $sinhVien->MaNguoiDung)
                ->where('danh_sach_lop.TrangThai', 1)
                ->get();
        }
        $view->with('danhSachLopHocPhanSidebar', $danhSachLopHocPhanSidebar);
    }
}