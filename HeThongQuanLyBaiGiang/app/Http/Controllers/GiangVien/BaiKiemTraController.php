<?php

namespace App\Http\Controllers\GiangVien;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaiKiemTraController extends Controller
{
    public function danhSachBaiKiemTra()
    {
        return view('giangvien.quanLyBaiKiemTra.danhSachBaiKiemTra');
    }
}