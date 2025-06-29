<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DanhSachKhoaSinhVien
{
    public function compose(View $view)
    {
        $maSinhVien = Auth::id();
        $rows = DB::table('khoa as k')
            ->join('bai_giang as bg', 'bg.MaKhoa', '=', 'k.MaKhoa')
            ->join('lop_hoc_phan as lhp', 'lhp.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->join('danh_sach_lop as dsl', function ($join) use ($maSinhVien) {
                $join->on('dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
                    ->where('dsl.MaSinhVien', '=', $maSinhVien)
                    ->where('dsl.TrangThai', '=', 1);
            })
            ->join('nguoi_dung as nd', 'nd.MaNguoiDung', '=', 'lhp.MaNguoiTao')
            ->select(
                'k.MaKhoa',
                'k.TenKhoa',
                'bg.MaBaiGiang',
                'bg.TenBaiGiang',
                'nd.MaNguoiDung as MaGiangVien',
                'nd.HoTen as TenGiangVien'
            )
            ->distinct()
            ->orderBy('k.TenKhoa')
            ->orderBy('bg.TenBaiGiang')
            ->orderBy('nd.HoTen')
            ->get();

        $menuData = [];

        foreach ($rows as $row) {
            $khoaKey = $row->MaKhoa;
            $baiGiangKey = $row->MaBaiGiang;

            if (!isset($menuData[$khoaKey])) {
                $menuData[$khoaKey] = [
                    'TenKhoa' => $row->TenKhoa,
                    'BaiGiang' => []
                ];
            }

            if (!isset($menuData[$khoaKey]['BaiGiang'][$baiGiangKey])) {
                $menuData[$khoaKey]['BaiGiang'][$baiGiangKey] = [
                    'TenBaiGiang' => $row->TenBaiGiang,
                    'MaBaiGiang' => $row->MaBaiGiang,
                    'GiangVien' => []
                ];
            }

            $menuData[$khoaKey]['BaiGiang'][$baiGiangKey]['GiangVien'][] = [
                'MaGiangVien' => $row->MaGiangVien,
                'TenGiangVien' => $row->TenGiangVien
            ];
        }
        $view->with('danhSachKhoa', $menuData);
    }
}
