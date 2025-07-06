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


        $tongFile = DB::table('file_bai_giang')
            ->join('bai', 'file_bai_giang.MaBai', '=', 'bai.MaBai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->join('bai_giang', 'chuong.MaBaiGiang', '=', 'bai_giang.MaBaiGiang')
            ->where('bai_giang.MaGiangVien', $maGiangVien)
            ->when($maBaiGiang, fn($q) => $q->where('bai_giang.MaBaiGiang', $maBaiGiang))
            ->count();


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
            ->distinct()
            ->count('danh_sach_lop.MaSinhVien');


        // $thongKeTheoThang = DB::table('bai_giang')
        //     ->where('MaGiangVien', $maGiangVien)
        //     ->when($maBaiGiang, fn($q) => $q->where('MaBaiGiang', $maBaiGiang))
        //     ->whereYear('created_at', now()->year)
        //     ->selectRaw('MONTH(created_at) as thang, COUNT(*) as so_luong')
        //     ->groupByRaw('MONTH(created_at)')
        //     ->pluck('so_luong', 'thang');


        $baiTheoThang = DB::table('bai_giang')
            ->join('chuong', 'bai_giang.MaBaiGiang', '=', 'chuong.MaBaiGiang')
            ->join('bai', 'chuong.MaChuong', '=', 'bai.MaChuong')
            ->where('bai_giang.MaGiangVien', $maGiangVien)
            ->when($maBaiGiang, fn($q) => $q->where('bai_giang.MaBaiGiang', $maBaiGiang))
            ->whereYear('bai_giang.created_at', now()->year)
            ->selectRaw('MONTH(bai_giang.created_at) as thang, COUNT(DISTINCT bai.MaBai) as so_luong')
            ->groupByRaw('MONTH(bai_giang.created_at)')
            ->pluck('so_luong', 'thang');

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
            
        return view('giangvien.dashboard.thongKeBaiGiang', [
            'maBaiGiang' => $maBaiGiang,
            'tongBaiGiang' => $tongBaiGiang,
            'tongChuong' => $tongChuong,
            'tongBai' => $tongBai,
            'tongFile' => $tongFile,
            'tongSinhVien' => $tongSinhVien,
            'tongDungLuong' => $tongDungLuong,
            // 'thongKeTheoThang' => $thongKeTheoThang,
            'namThongKe' => $namThongKe,
            'danhSachBaiGiang' => $danhSachBaiGiang,
            'baiTheoThang' => $baiTheoThang,
        ]);
    }
    public function layDuLieuBieuDoThongKe(Request $request)
    {
        $nam = $request->query('nam');
        $maBaiGiang = $request->query('maBaiGiang');

        if ($nam && !is_numeric($nam)) {
            return response()->json(['error' => 'Năm không hợp lệ'], 400);
        }

        try {
            $query = DB::table('bai_giang')
                ->join('chuong', 'bai_giang.MaBaiGiang', '=', 'chuong.MaBaiGiang')
                ->join('bai', 'chuong.MaChuong', '=', 'bai.MaChuong')
                ->where('bai_giang.MaGiangVien', Auth::id());

            if ($nam) {
                $query->whereYear('bai_giang.created_at', $nam);
            }

            if ($maBaiGiang) {
                $query->where('bai_giang.MaBaiGiang', $maBaiGiang);
            }

            $data = $query
                ->selectRaw('MONTH(bai_giang.created_at) as thang, COUNT(DISTINCT bai.MaBai) as tong')
                ->groupByRaw('MONTH(bai_giang.created_at)')
                ->pluck('tong', 'thang');

            $ketQua = [];
            for ($i = 1; $i <= 12; $i++) {
                $ketQua[$i] = $data[$i] ?? 0;
            }

            return response()->json($ketQua);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy dữ liệu biểu đồ:', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Lỗi server'], 500);
        }
    }
}