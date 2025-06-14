<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DanhSachKhoaSinhVien
{
    public function compose(View $view)
    {
        $rows = DB::table('khoa as k')
            ->join('mon_hoc as mh', 'mh.MaKhoa', '=', 'k.MaKhoa')
            ->join('hoc_phan as hp', 'hp.MaMonHoc', '=', 'mh.MaMonHoc')
            ->join('nguoi_dung as nd', 'nd.MaNguoiDung', '=', 'hp.MaNguoiTao')
            ->select(
                'k.MaKhoa',
                'k.TenKhoa',
                'mh.MaMonHoc',
                'mh.TenMonHoc',
                'nd.MaNguoiDung as MaGiangVien',
                'nd.HoTen as TenGiangVien'
            )
            ->distinct()
            ->orderBy('k.TenKhoa')
            ->orderBy('mh.TenMonHoc')
            ->orderBy('nd.HoTen')
            ->get();

        $menuData = [];

        foreach ($rows as $row) {
            $khoaKey = $row->MaKhoa;
            $monKey = $row->MaMonHoc;

            if (!isset($menuData[$khoaKey])) {
                $menuData[$khoaKey] = [
                    'TenKhoa' => $row->TenKhoa,
                    'MonHoc' => []
                ];
            }

            if (!isset($menuData[$khoaKey]['MonHoc'][$monKey])) {
                $menuData[$khoaKey]['MonHoc'][$monKey] = [
                    'TenMonHoc' => $row->TenMonHoc,
                    'GiangVien' => []
                ];
            }

            $menuData[$khoaKey]['MonHoc'][$monKey]['GiangVien'][] = [
                'MaGiangVien' => $row->MaGiangVien,
                'TenGiangVien' => $row->TenGiangVien
            ];
        }
        $view->with('danhSachKhoa', $menuData);
    }
}