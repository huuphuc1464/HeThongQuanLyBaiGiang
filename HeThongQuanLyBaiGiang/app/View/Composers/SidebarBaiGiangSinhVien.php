<?php

namespace App\View\Composers;

use App\Models\LopHocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SidebarBaiGiangSinhVien
{
    public function compose(View $view)
    {
        $sinhVien = Auth::user();
        $maLopHocPhan = request()->route('id');
        $danhSachBaiGiangSidebar = collect();
        $lopHocPhan = null;

        if ($sinhVien && $maLopHocPhan) {
            $lopHocPhan = DB::table('lop_hoc_phan')
                ->select('lop_hoc_phan.TenLopHocPhan', 'lop_hoc_phan.MoTa', 'hoc_phan.AnhHocPhan', 'lop_hoc_phan.MaHocPhan')
                ->join('hoc_phan', 'lop_hoc_phan.MaHocPhan', '=', 'hoc_phan.MaHocPhan')
                ->join('danh_sach_lop', 'danh_sach_lop.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
                ->where('lop_hoc_phan.TrangThai', 1)
                ->where('hoc_phan.TrangThai', 1)
                ->where('danh_sach_lop.MaSinhVien', $sinhVien->MaNguoiDung)
                ->where('lop_hoc_phan.MaLopHocPhan', $maLopHocPhan)
                ->first();

            if ($lopHocPhan) {
                $danhSachBaiGiangSidebar = DB::table('bai_giang')
                    ->join('lop_hoc_phan', function ($join) {
                        $join->on('bai_giang.MaHocPhan', '=', 'lop_hoc_phan.MaHocPhan')
                            ->on('lop_hoc_phan.MaNguoiTao', '=', 'bai_giang.MaGiangVien');
                    })
                    ->select(
                        'bai_giang.TenChuong',
                        'bai_giang.TenBai',
                        'bai_giang.MaBaiGiang',
                        'bai_giang.TenBaiGiang'
                    )
                    ->where('lop_hoc_phan.MaLopHocPhan', $maLopHocPhan)
                    ->where('bai_giang.TrangThai', 1)
                    ->orderBy('bai_giang.TenChuong')
                    ->orderBy('bai_giang.TenBai')
                    ->orderBy('bai_giang.TenBaiGiang')
                    ->get()
                    ->groupBy('TenChuong')
                    ->map(function ($itemsByChuong) {
                        return $itemsByChuong->groupBy('TenBai');
                    });
            }
        }

        $view->with([
            'danhSachBaiGiangSidebar' => $danhSachBaiGiangSidebar,
            'lopHocPhan' => $lopHocPhan,
        ]);
    }
}
