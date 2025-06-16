<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\BaiGiang;
use App\Models\LopHocPhan;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function hienThiDanhSachBaiGiang()
    {
        $maSinhVien = Auth::user()->MaNguoiDung;
        $danhSachBaiGiang = DB::table('lop_hoc_phan as lhp')
            ->join('hoc_phan as hp', 'lhp.MaHocPhan', '=', 'hp.MaHocPhan')
            ->join('nguoi_dung as nd', 'lhp.MaNguoiTao', '=', 'nd.MaNguoiDung')
            ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
            ->where('dsl.MaSinhVien', $maSinhVien)
            ->select(
                'lhp.MaLopHocPhan',
                'lhp.TenLopHocPhan',
                'hp.TenHocPhan',
                'lhp.MoTa',
                'nd.HoTen as TenGiangVien',
                'nd.AnhDaiDien as AnhGiangVien',
                'hp.AnhHocPhan',
                DB::raw('COUNT(dsl.MaDanhSachLop) as SoLuongSinhVien')
            )
            ->groupBy(
                'lhp.MaLopHocPhan',
                'lhp.TenLopHocPhan',
                'hp.TenHocPhan',
                'lhp.MoTa',
                'nd.HoTen',
                'nd.AnhDaiDien',
                'hp.AnhHocPhan'
            )
            ->get();
        return view('sinhvien.trangChu', compact('danhSachBaiGiang'));
    }

    public function hienFormDoiMatKhau()
    {
        return view('sinhvien.doiMatKhau');
    }

    public function hienFormThayDoiThongTin()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $userId = Auth::id();
        $user = NguoiDung::select(
            'nguoi_dung.MaNguoiDung',
            'nguoi_dung.MaVaiTro',
            'nguoi_dung.TenTaiKhoan',
            'nguoi_dung.Email',
            'nguoi_dung.HoTen',
            'nguoi_dung.SoDienThoai',
            'nguoi_dung.AnhDaiDien',
            'nguoi_dung.DiaChi',
            'nguoi_dung.NgaySinh',
            'nguoi_dung.GioiTinh',
            'sinh_vien.MSSV'
        )
            ->leftJoin('sinh_vien', 'sinh_vien.MaNguoiDung', '=', 'nguoi_dung.MaNguoiDung')
            ->where('nguoi_dung.MaNguoiDung', $userId)
            ->where('nguoi_dung.TrangThai', 1)
            ->first();

        return view('sinhvien.thayDoiThongTinCaNhan', compact('user'));
    }
}
