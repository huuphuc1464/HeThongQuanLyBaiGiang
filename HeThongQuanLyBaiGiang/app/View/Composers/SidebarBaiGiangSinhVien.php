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
                ->select('lhp.TenLopHocPhan', 'lhp.MoTa', 'bg.AnhBaiGiang', 'lhp.MaBaiGiang')
                ->join('bai_giang as bg', 'lhp.MaBaiGiang', '=', 'bg.MaBaiGiang')
                ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
                ->where('bg.TrangThai', 1)
                ->where('dsl.MaSinhVien', $sinhVien->MaNguoiDung)
                ->where('lhp.MaLopHocPhan', $maLopHocPhan)
                ->first();

            if ($lopHocPhan) {
                $danhSachBaiGiangSidebar = DB::table('bai as b')
                    ->join('chuong as c', 'b.MaChuong', '=', 'c.MaChuong')
                    ->join('bai_giang as bg', 'c.MaBaiGiang', '=', 'bg.MaBaiGiang')
                    ->join('lop_hoc_phan as lhp', 'lhp.MaBaiGiang', '=', 'bg.MaBaiGiang')
                    ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
                    ->where('lhp.MaLopHocPhan', $maLopHocPhan)
                    ->where('dsl.MaSinhVien', '=', Auth::id())
                    ->where('dsl.TrangThai', '=', 1)
                    ->where('b.TrangThai', 1)
                    ->where('c.TrangThai', 1)
                    ->where('bg.TrangThai', 1)
                    ->select(
                        'c.TenChuong',
                        'b.TenBai',
                        'b.MaBai as MaBaiGiang',
                        'b.TenBai as TenBaiGiang'
                    )
                    ->orderBy('c.TenChuong')
                    ->orderBy('b.created_at')
                    ->orderBy('b.TenBai')
                    ->get()
                    ->groupBy('TenChuong')
                    ->map(function ($dsBai) {
                        return $dsBai->map(function ($item) {
                            return (object)[
                                'MaBaiGiang' => $item->MaBaiGiang,
                                'TenBaiGiang' => $item->TenBaiGiang,
                            ];
                        });
                    });
            }
        }

        $view->with([
            'danhSachBaiGiangSidebar' => $danhSachBaiGiangSidebar,
            'lopHocPhan' => $lopHocPhan,
        ]);
    }
}
