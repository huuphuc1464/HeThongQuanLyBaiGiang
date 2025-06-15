<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function hienFormDoiMatKhau()
    {
        return view('giangvien.doiMatKhau');
    }
}
