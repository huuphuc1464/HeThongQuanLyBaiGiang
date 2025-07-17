<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        if (!$user) {
            return redirect()->route('login')->withErrors(['errorSystem' => 'Người dùng không tồn tại hoặc đã bị khóa.']);
        }
        return view('giangvien.thayDoiThongTinCaNhan', compact('user'));
    }

    public function dashboard()
    {
        $maGiangVien = Auth::id();
        $maBaiGiang = request('MaBaiGiang');

        $tongBaiGiang = DB::table('bai_giang')
            ->where('MaGiangVien', $maGiangVien)
            ->when($maBaiGiang, fn($q) => $q->where('MaBaiGiang', $maBaiGiang))
            ->count();

        $tongChuong = DB::table('chuong')
            ->where('MaGiangVien', $maGiangVien)
            ->when($maBaiGiang, fn($q) => $q->where('MaBaiGiang', $maBaiGiang))
            ->count();

        $tongBai = DB::table('bai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->join('bai_giang', 'chuong.MaBaiGiang', '=', 'bai_giang.MaBaiGiang')
            ->where('bai_giang.MaGiangVien', $maGiangVien)
            ->when($maBaiGiang, fn($q) => $q->where('bai_giang.MaBaiGiang', $maBaiGiang))
            ->distinct()
            ->count('bai.MaBai');

        $tongSinhVien = DB::table('danh_sach_lop')
            ->join('lop_hoc_phan', 'danh_sach_lop.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->where('lop_hoc_phan.MaNguoiTao', $maGiangVien)
            ->when($maBaiGiang, function ($query) use ($maBaiGiang) {
                $query->whereIn('lop_hoc_phan.MaLopHocPhan', function ($sub) use ($maBaiGiang) {
                    $sub->select('MaLopHocPhan')
                        ->from('lop_hoc_phan')
                        ->where('MaBaiGiang', $maBaiGiang);
                });
            })
            ->count('danh_sach_lop.MaSinhVien');


        $filePaths = DB::table('file_bai_giang')
            ->join('bai', 'file_bai_giang.MaBai', '=', 'bai.MaBai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->join('bai_giang', 'chuong.MaBaiGiang', '=', 'bai_giang.MaBaiGiang')
            ->where('bai_giang.MaGiangVien', $maGiangVien)
            ->when($maBaiGiang, fn($q) => $q->where('bai_giang.MaBaiGiang', $maBaiGiang))
            ->pluck('file_bai_giang.DuongDan');

        $tongDungLuong = 0;
        foreach ($filePaths as $path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                $tongDungLuong += filesize($fullPath);
            }
        }
        $tongDungLuong = round($tongDungLuong / 1024 / 1024, 2);

        $danhSachBaiGiang = DB::table('bai_giang')
            ->where('MaGiangVien', $maGiangVien)
            ->select('MaBaiGiang', 'TenBaiGiang', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        $namThongKe = DB::table('bai_giang')
            ->where('MaGiangVien', $maGiangVien)
            ->selectRaw('YEAR(created_at) as nam')
            ->union(
                DB::table('bai_giang')
                    ->where('MaGiangVien', $maGiangVien)
                    ->selectRaw('YEAR(updated_at) as nam')
            )
            ->distinct()
            ->orderByDesc('nam')
            ->pluck('nam');

        $tongLopHocPhan = DB::table('lop_hoc_phan')
            ->where('MaNguoiTao', $maGiangVien)
            ->when($maBaiGiang, function ($query) use ($maBaiGiang) {
                $query->where('MaBaiGiang', $maBaiGiang);
            })
            ->count();

        $tongSuKienZoom = DB::table('su_kien_zoom')
            ->where('MaGiangVien', $maGiangVien)
            ->when($maBaiGiang, function ($query) use ($maBaiGiang) {
                $query->join('lop_hoc_phan', 'su_kien_zoom.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
                    ->where('lop_hoc_phan.MaBaiGiang', $maBaiGiang);
            })
            ->count();

        $tongBaiKiemTra = DB::table('bai_kiem_tra')
            ->where('MaGiangVien', $maGiangVien)
            ->when($maBaiGiang, function ($query) use ($maBaiGiang) {
                $query->join('lop_hoc_phan', 'bai_kiem_tra.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
                    ->where('lop_hoc_phan.MaBaiGiang', $maBaiGiang);
            })
            ->count();

        $baiTheoThang = DB::table('bai_giang')
            ->join('chuong', 'bai_giang.MaBaiGiang', '=', 'chuong.MaBaiGiang')
            ->join('bai', 'chuong.MaChuong', '=', 'bai.MaChuong')
            ->where('bai_giang.MaGiangVien', $maGiangVien)
            ->when($maBaiGiang, fn($q) => $q->where('bai_giang.MaBaiGiang', $maBaiGiang))
            ->whereYear('bai_giang.created_at', now()->year)
            ->selectRaw('MONTH(bai_giang.created_at) as thang, COUNT(DISTINCT bai.MaBai) as so_luong')
            ->groupByRaw('MONTH(bai_giang.created_at)')
            ->pluck('so_luong', 'thang');

        $quizTheoThang = DB::table('bai_kiem_tra')
            ->join('lop_hoc_phan', 'bai_kiem_tra.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->where('lop_hoc_phan.MaNguoiTao', $maGiangVien)
            ->when($maBaiGiang, function ($query) use ($maBaiGiang) {
                $query->where('lop_hoc_phan.MaBaiGiang', $maBaiGiang);
            })
            ->whereYear('bai_kiem_tra.created_at', now()->year)
            ->selectRaw('MONTH(bai_kiem_tra.created_at) as thang, COUNT(*) as so_luong')
            ->groupByRaw('MONTH(bai_kiem_tra.created_at)')
            ->pluck('so_luong', 'thang');

        $zoomTheoThang = DB::table('su_kien_zoom')
            ->join('lop_hoc_phan', 'su_kien_zoom.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->where('lop_hoc_phan.MaNguoiTao', $maGiangVien)
            ->when($maBaiGiang, function ($query) use ($maBaiGiang) {
                $query->where('lop_hoc_phan.MaBaiGiang', $maBaiGiang);
            })
            ->whereYear('su_kien_zoom.created_at', now()->year)
            ->selectRaw('MONTH(su_kien_zoom.created_at) as thang, COUNT(*) as so_luong')
            ->groupByRaw('MONTH(su_kien_zoom.created_at)')
            ->pluck('so_luong', 'thang');

        return view('giangvien.dashboard.thongKeBaiGiang', [
            'maBaiGiang' => $maBaiGiang,
            'tongBaiGiang' => $tongBaiGiang,
            'tongChuong' => $tongChuong,
            'tongBai' => $tongBai,
            'tongSinhVien' => $tongSinhVien,
            'tongDungLuong' => $tongDungLuong,
            'namThongKe' => $namThongKe,
            'danhSachBaiGiang' => $danhSachBaiGiang,
            'baiTheoThang' => $baiTheoThang,
            'tongLopHocPhan' => $tongLopHocPhan,
            'tongSuKienZoom' => $tongSuKienZoom,
            'tongBaiKiemTra' => $tongBaiKiemTra,
            'quizTheoThang' => $quizTheoThang,
            'zoomTheoThang' => $zoomTheoThang
        ]);
    }

    public function layDuLieuBieuDoThongKe(Request $request)
    {
        $nam = $request->query('nam');
        $maBaiGiang = $request->query('maBaiGiang');
        $maGiangVien = Auth::id();

        if ($nam && !is_numeric($nam)) {
            return response()->json(['error' => 'Năm không hợp lệ'], 400);
        }

        try {
            $baiGiangTheoThang = DB::table('bai_giang')
                ->join('chuong', 'bai_giang.MaBaiGiang', '=', 'chuong.MaBaiGiang')
                ->join('bai', 'chuong.MaChuong', '=', 'bai.MaChuong')
                ->where('bai_giang.MaGiangVien', $maGiangVien)
                ->when($maBaiGiang, fn($q) => $q->where('bai_giang.MaBaiGiang', $maBaiGiang))
                ->when($nam, fn($q) => $q->whereYear('bai_giang.created_at', $nam))
                ->selectRaw('MONTH(bai_giang.created_at) as thang, COUNT(DISTINCT bai.MaBai) as so_luong')
                ->groupByRaw('MONTH(bai_giang.created_at)')
                ->pluck('so_luong', 'thang');


            $quizTheoThang = DB::table('bai_kiem_tra')
                ->join('lop_hoc_phan', 'bai_kiem_tra.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
                ->where('lop_hoc_phan.MaNguoiTao', $maGiangVien)
                ->when($maBaiGiang, fn($query) => $query->where('lop_hoc_phan.MaBaiGiang', $maBaiGiang))
                ->when($nam, fn($query) => $query->whereYear('bai_kiem_tra.created_at', $nam))
                ->selectRaw('MONTH(bai_kiem_tra.created_at) as thang, COUNT(*) as so_luong')
                ->groupByRaw('MONTH(bai_kiem_tra.created_at)')
                ->pluck('so_luong', 'thang');

            $zoomTheoThang = DB::table('su_kien_zoom')
                ->join('lop_hoc_phan', 'su_kien_zoom.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
                ->where('lop_hoc_phan.MaNguoiTao', $maGiangVien)
                ->when($maBaiGiang, fn($query) => $query->where('lop_hoc_phan.MaBaiGiang', $maBaiGiang))
                ->when($nam, fn($query) => $query->whereYear('su_kien_zoom.created_at', $nam))
                ->selectRaw('MONTH(su_kien_zoom.created_at) as thang, COUNT(*) as so_luong')
                ->groupByRaw('MONTH(su_kien_zoom.created_at)')
                ->pluck('so_luong', 'thang');

            $result = [
                'baiGiang' => [],
                'baiKiemTra' => [],
                'suKienZoom' => [],
            ];

            for ($i = 1; $i <= 12; $i++) {
                $result['baiGiang'][$i] = $baiGiangTheoThang[$i] ?? 0;
                $result['baiKiemTra'][$i] = $quizTheoThang[$i] ?? 0;
                $result['suKienZoom'][$i] = $zoomTheoThang[$i] ?? 0;
            }
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi server'], 500);
        }
    }
}