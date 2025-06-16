<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuKienZoomController extends Controller
{
    public function danhSachSuKien()
    {
        $danhSachSuKien = DB::table('su_kien_zoom')
            ->join('lop_hoc_phan', 'su_kien_zoom.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->select('su_kien_zoom.*', 'lop_hoc_phan.TenLopHocPhan')
            ->paginate(10);
        return view('giangvien.danhSachSuKienZoom', compact('danhSachSuKien'));
    }
}