<?php

namespace App\View\Composers;

use App\Models\ThongBao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ThongBaoSinhVien
{
    public function compose(View $view)
    {
        $sinhVien = Auth::user();
        $thongBao = collect();
        if ($sinhVien && $sinhVien->MaNguoiDung) {
            $thongBao = DB::table('thong_bao as tb')
                ->join('danh_sach_lop as dsl', 'tb.MaLopHocPhan', '=', 'dsl.MaLopHocPhan')
                ->where('tb.TrangThai', 1)
                ->where('dsl.TrangThai', 1)
                ->where('dsl.MaSinhVien', $sinhVien->MaNguoiDung)
                ->orderByDesc('tb.ThoiGianTao')
                ->select('tb.NoiDung', 'tb.ThoiGianTao')
                ->get();
        }

        $view->with('thongBao', $thongBao);
    }
}
