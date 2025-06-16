<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function hienFormDoiMatKhau()
    {
        return view('giangvien.doiMatKhau');
    }
    public function hienFormThayDoiThongTin()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $userId = Auth::id();
        $user = NguoiDung::select('MaNguoiDung', 'MaVaiTro', 'TenTaiKhoan', 'Email', 'HoTen', 'SoDienThoai', 'AnhDaiDien', 'DiaChi', 'NgaySinh', 'GioiTinh')
            ->where('MaNguoiDung', $userId)
            ->where('TrangThai', 1)
            ->first();
        return view('giangvien.thayDoiThongTinCaNhan', compact('user'));
    }
}
