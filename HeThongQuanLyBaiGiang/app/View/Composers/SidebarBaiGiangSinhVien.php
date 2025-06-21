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
            $lopHocPhan = DB::table('lop_hoc_phan as lhp')
                ->select('lhp.TenLopHocPhan', 'lhp.MoTa', 'hp.AnhHocPhan', 'lhp.MaHocPhan')
                ->join('hoc_phan as hp', 'lhp.MaHocPhan', '=', 'hp.MaHocPhan')
                ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
                ->where('lhp.TrangThai', 1)
                ->where('hp.TrangThai', 1)
                ->where('dsl.MaSinhVien', $sinhVien->MaNguoiDung)
                ->where('lhp.MaLopHocPhan', $maLopHocPhan)
                ->first();

            if ($lopHocPhan) {
                $danhSachBaiGiangSidebar = DB::table('bai_giang as bg')
                    ->join('lop_hoc_phan as lhp', function ($join) {
                        $join->on('bg.MaHocPhan', '=', 'lhp.MaHocPhan')
                            ->on('lhp.MaNguoiTao', '=', 'bg.MaGiangVien');
                    })
                    ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
                    ->where('lhp.MaLopHocPhan', $maLopHocPhan)
                    ->where('dsl.MaSinhVien', '=', Auth::id())
                    ->where('dsl.TrangThai', '=', 1)
                    ->where('bg.TrangThai', 1)
                    ->select(
                        'bg.TenChuong',
                        'bg.TenBai',
                        'bg.MaBaiGiang',
                        'bg.TenBaiGiang'
                    )
                    ->orderBy('bg.TenChuong')
                    ->orderBy('bg.TenBai')
                    ->orderBy('bg.TenBaiGiang')
                    ->orderBy('bg.created_at')
                    ->get()
                    ->groupBy('TenChuong')
                    ->map(fn($chuong) => $chuong->groupBy('TenBai'));
            }
        }

        $view->with([
            'danhSachBaiGiangSidebar' => $danhSachBaiGiangSidebar,
            'lopHocPhan' => $lopHocPhan,
        ]);
    }
}