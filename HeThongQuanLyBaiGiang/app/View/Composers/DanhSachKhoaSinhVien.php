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
            ->orderBy('nd.HoTen')
            ->orderBy('bg.TenBaiGiang')
            ->get();

        $menuData = [];

        foreach ($rows as $row) {
            $maKhoa = $row->MaKhoa;
            $maGV = $row->MaGiangVien;
            $maBaiGiang = $row->MaBaiGiang;

            if (!isset($menuData[$maKhoa])) {
                $menuData[$maKhoa] = [
                    'TenKhoa' => $row->TenKhoa,
                    'GiangVien' => []
                ];
            }

            if (!isset($menuData[$maKhoa]['GiangVien'][$maGV])) {
                $menuData[$maKhoa]['GiangVien'][$maGV] = [
                    'TenGiangVien' => $row->TenGiangVien,
                    'MaGiangVien' => $row->MaGiangVien,
                    'BaiGiang' => []
                ];
            }

            // Tránh trùng bài giảng
            $menuData[$maKhoa]['GiangVien'][$maGV]['BaiGiang'][$maBaiGiang] = [
                'MaBaiGiang' => $row->MaBaiGiang,
                'TenBaiGiang' => $row->TenBaiGiang
            ];
        }

        $view->with('danhSachKhoa', $menuData);
    }
}