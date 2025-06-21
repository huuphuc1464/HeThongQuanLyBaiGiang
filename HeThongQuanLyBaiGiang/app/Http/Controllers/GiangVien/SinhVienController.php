<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SinhVienController extends Controller
{
    public function danhSachSinhVien(Request $request, $maLopHocPhan)
    {
        $lopHocPhan = DB::table('lop_hoc_phan')
            ->where('MaLopHocPhan', $maLopHocPhan)
            ->where('MaNguoiTao', Auth::id())
            ->first();

        if (!$lopHocPhan) {
            abort(404, 'Lớp học phần không tồn tại');
        }

        $query = DB::table('danh_sach_lop as dsl')
            ->join('sinh_vien as sv', 'sv.MaNguoiDung', '=', 'dsl.MaSinhVien')
            ->join('nguoi_dung as nd', 'nd.MaNguoiDung', '=', 'dsl.MaSinhVien')
            ->join('lop_hoc_phan as lhp', 'lhp.MaLopHocPhan', '=', 'dsl.MaLopHocPhan')
            ->where('dsl.MaLopHocPhan', $maLopHocPhan)
            ->where('lhp.MaNguoiTao', Auth::id())
            ->select(
                'dsl.MaDanhSachLop',
                'nd.HoTen',
                'sv.MSSV',
                'nd.Email',
                'nd.NgaySinh',
                'nd.GioiTinh',
                'dsl.TrangThai'
            );

        if ($request->filled('search')) {
            $keywords = preg_split('/\s+/', trim($request->search));

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $q->orWhereRaw('LOWER(nd.HoTen) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(nd.Email) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(sv.MSSV) LIKE ?', ["%$kw%"]);
                }
            });
        }
        $sinhViens = $query->paginate(10)->withQueryString();

        return view('giangvien.quanLyLopHocPhan.danhSachSinhVien', compact('sinhViens', 'lopHocPhan'));
    }

    public function xoaSinhVien($maLopHocPhan, $maDanhSachLop)
    {
        $sinhVien = DB::table('danh_sach_lop')
            ->where('MaDanhSachLop', $maDanhSachLop)
            ->where('MaLopHocPhan', $maLopHocPhan)
            ->first();

        if (!$sinhVien) {
            return redirect()->back()->with('error', 'Không tìm thấy sinh viên trong lớp này.');
        }

        DB::table('danh_sach_lop')
            ->where('MaDanhSachLop', $maDanhSachLop)
            ->delete();

        return redirect()->back()->with('success', 'Đã xóa sinh viên khỏi lớp học phần thành công.');
    }
}
