<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaiGiangController extends Controller
{
    private function danhSachBaiGiang($id)
    {
        $lopHocPhan = DB::table('lop_hoc_phan')->where('MaLopHocPhan', $id)->first();

        if (!$lopHocPhan) {
            return [];
        }

        $giangVien = DB::table('nguoi_dung')
            ->where('MaNguoiDung', $lopHocPhan->MaNguoiTao)
            ->first();

        $baiGiangs = DB::table('bai_giang')
            ->join('lop_hoc_phan', function ($join) {
                $join->on('bai_giang.MaHocPhan', '=', 'lop_hoc_phan.MaHocPhan')
                    ->on('lop_hoc_phan.MaNguoiTao', '=', 'bai_giang.MaGiangVien');
            })
            ->where('lop_hoc_phan.MaLopHocPhan', $id)
            ->where('bai_giang.TrangThai', 1)
            ->select(
                'bai_giang.TenChuong',
                'bai_giang.TenBai',
                'bai_giang.TenBaiGiang',
                'bai_giang.MaBaiGiang',
                'bai_giang.updated_at',
                'bai_giang.created_at'
            )
            ->orderBy('bai_giang.TenChuong')
            ->orderBy('bai_giang.TenBai')
            ->orderBy('bai_giang.TenBaiGiang')
            ->orderBy('bai_giang.created_at')
            ->get()
            ->groupBy('TenChuong')
            ->map(fn($chuong) => $chuong->groupBy('TenBai'));

        return compact('lopHocPhan', 'giangVien', 'baiGiangs');
    }



    public function renderTab(Request $request, $id, $tab = 'bai-giang')
    {
        $hocPhan = DB::table('hoc_phan')
            ->where('MaHocPhan', $id)
            ->select('MaHocPhan', 'TenHocPhan')
            ->first();
        Log::info($hocPhan->MaHocPhan);
        switch ($tab) {
            case 'bai-kiem-tra':
                return view('giangvien.lopHocPhan.kiemTra', compact('id', 'tab'));
            case 'su-kien-zoom':
                return view('giangvien.lopHocPhan.zoom', compact('id', 'tab'));
            case 'moi-nguoi':
                return view('giangvien.lopHocPhan.nguoi', compact('id', 'tab'));
            default:
                return view('sinhvien.danhSachBaiGiang', [
                    'id' => $id,
                    'tab' => $tab,
                    'hocPhan' => $hocPhan,
                    ...$this->danhSachBaiGiang($id),
                ]);
        }
    }

    public function chiTietBaiGiang($id, $maBaiGiang)
    {
        $baiGiang = DB::table('bai_giang as bg')
            ->join('lop_hoc_phan as lhp', 'lhp.MaHocPhan', '=', 'bg.MaHocPhan')
            ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
            ->where('lhp.MaLopHocPhan', $id)
            ->where('bg.MaBaiGiang', $maBaiGiang)
            ->where('dsl.MaSinhVien', Auth::id())
            ->select('bg.*')
            ->first();

        $files = DB::table('file_bai_giang')->where('MaBaiGiang', $maBaiGiang)
            ->where('TrangThai', 1)
            ->get();
        $tab = 'bai-giang';
        return view('sinhvien.chiTietBaiGiang', compact('baiGiang', 'files', 'tab', 'id'));
    }
}