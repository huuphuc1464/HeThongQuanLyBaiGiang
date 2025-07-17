<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Models\Khoa;
use App\Models\LopHocPhan;
use App\Models\DanhSachLop;
use App\Models\BaiGiang;
use App\Models\BaiKiemTra;
use App\Models\ThongBao;
use App\Models\Chuong;
use App\Models\Bai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan hệ thống
        $thongKeTongQuan = [
            'tongKhoa' => Khoa::where('TrangThai', 1)->count(),
            'tongBaiGiang' => BaiGiang::where('TrangThai', 1)->count(),
            'tongChuong' => Chuong::where('TrangThai', 1)->count(),
            'tongBai' => Bai::where('TrangThai', 1)->count(),
            'tongGiangVien' => NguoiDung::where('MaVaiTro', 2)->where('TrangThai', 1)->count(),
            'tongSinhVien' => NguoiDung::where('MaVaiTro', 3)->where('TrangThai', 1)->count(),
        ];

        // Thống kê hoạt động đào tạo
        $thongKeDaoTao = [
            'tongBaiGiang' => BaiGiang::where('TrangThai', 1)->count(),
            'tongChuong' => Chuong::where('TrangThai', 1)->count(),
            'tongBai' => Bai::where('TrangThai', 1)->count(),
            'tongLopHocPhan' => LopHocPhan::where('TrangThai', 1)->count(),
            'topKhoaNhieuBaiGiang' => $this->getTopKhoaNhieuBaiGiang(5),
            'topGiangVienNhieuBaiGiang' => $this->getTopGiangVienNhieuBaiGiang(5),
        ];

        // Thống kê hoạt động sinh viên
        $thongKeSinhVien = [
            'tongSinhVienThamGia' => DanhSachLop::where('TrangThai', 1)->distinct()->count('MaSinhVien'),
            'trungBinhSinhVienLop' => $this->getTrungBinhSinhVienLop(),
            'lopNhieuSinhVienNhat' => $this->getLopNhieuSinhVienNhat(),
        ];

        // Thống kê hoạt động hệ thống
        $thongKeHeThong = [
            'tongBaiGiang' => BaiGiang::where('TrangThai', 1)->count(),
            'tongBaiKiemTra' => BaiKiemTra::where('TrangThai', 1)->count(),
            'tongThongBao' => ThongBao::where('TrangThai', 1)->count(),
        ];

        return view('admin.dashboard', compact('thongKeTongQuan', 'thongKeDaoTao', 'thongKeSinhVien', 'thongKeHeThong'));
    }

    private function getTopKhoaNhieuBaiGiang($limit = 5)
    {
        return Khoa::select('khoa.TenKhoa', DB::raw('COUNT(bai_giang.MaBaiGiang) as soBaiGiang'))
            ->leftJoin('bai_giang', function ($join) {
                $join->on('khoa.MaKhoa', '=', 'bai_giang.MaKhoa')->where('bai_giang.TrangThai', 1);
            })
            ->where('khoa.TrangThai', 1)
            ->groupBy('khoa.MaKhoa', 'khoa.TenKhoa')
            ->having('soBaiGiang', '>', 0)
            ->orderBy('soBaiGiang', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getTopGiangVienNhieuBaiGiang($limit = 5)
    {
        return NguoiDung::select('nguoi_dung.HoTen', DB::raw('COUNT(bai_giang.MaBaiGiang) as soBaiGiang'))
            ->leftJoin('bai_giang', function ($join) {
                $join->on('nguoi_dung.MaNguoiDung', '=', 'bai_giang.MaGiangVien')->where('bai_giang.TrangThai', 1);
            })
            ->where('nguoi_dung.MaVaiTro', 2) // Giảng viên
            ->where('nguoi_dung.TrangThai', 1)
            ->groupBy('nguoi_dung.MaNguoiDung', 'nguoi_dung.HoTen')
            ->having('soBaiGiang', '>', 0)
            ->orderBy('soBaiGiang', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getTrungBinhSinhVienLop()
    {
        // Lấy danh sách các lớp học phần có sinh viên
        $lopCoSinhVien = LopHocPhan::select('lop_hoc_phan.MaLopHocPhan')
            ->join('danh_sach_lop', 'lop_hoc_phan.MaLopHocPhan', '=', 'danh_sach_lop.MaLopHocPhan')
            ->where('lop_hoc_phan.TrangThai', 1)
            ->where('danh_sach_lop.TrangThai', 1)
            ->groupBy('lop_hoc_phan.MaLopHocPhan')
            ->get();

        if ($lopCoSinhVien->count() == 0) {
            return 0;
        }
        // Tính tổng số sinh viên trong tất cả các lớp
        $tongSinhVien = DanhSachLop::where('TrangThai', 1)->count();
        // Tính trung bình
        return round($tongSinhVien / $lopCoSinhVien->count(), 0);
    }

    private function getLopNhieuSinhVienNhat()
    {
        return LopHocPhan::select('lop_hoc_phan.TenLopHocPhan', DB::raw('COUNT(danh_sach_lop.MaSinhVien) as soSinhVien'))
            ->leftJoin('danh_sach_lop', 'lop_hoc_phan.MaLopHocPhan', '=', 'danh_sach_lop.MaLopHocPhan')
            ->where('lop_hoc_phan.TrangThai', 1)
            ->where('danh_sach_lop.TrangThai', 1)
            ->groupBy('lop_hoc_phan.MaLopHocPhan', 'lop_hoc_phan.TenLopHocPhan')
            ->orderBy('soSinhVien', 'desc')
            ->limit(5)
            ->get();
    }


    // Trả về mảng gồm 12 phần tử, mỗi phần tử là số lượng bản ghi của từng  tháng
    private function getThongKeTheoThangByModel($model, $year, $dateColumn = 'created_at')
    {
        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            try {
                $count = $model::whereYear($dateColumn, $year)
                    ->whereMonth($dateColumn, $month)
                    ->where('TrangThai', 1)
                    ->count();
                $data[] = $count;
            } catch (\Exception $e) {
                $data[] = 0; // Nếu có lỗi, trả về 0
            }
        }
        return $data;
    }

    public function hienFormDoiMatKhau()
    {
        return view('admin.doiMatKhau');
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

        return view('admin.thayDoiThongTinCaNhan', compact('user'));
    }

    // API: Lấy danh sách năm có dữ liệu
    public function getYears()
    {
        try {
            $yearsBG = BaiGiang::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
            $yearsBKT = BaiKiemTra::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
            $yearsTB = ThongBao::selectRaw('YEAR(ThoiGianTao) as year')->distinct()->pluck('year')->toArray();
            $years = array_unique(array_merge($yearsBG, $yearsBKT, $yearsTB));
            rsort($years);
            return response()->json(array_values($years));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // API: Lấy thống kê theo tháng cho 1 năm
    public function getStatsByYear($year)
    {
        $baiGiang = $this->getThongKeTheoThangByModel(BaiGiang::class, $year);
        $baiKiemTra = $this->getThongKeTheoThangByModel(BaiKiemTra::class, $year);
        $thongBao = $this->getThongKeTheoThangByModel(ThongBao::class, $year, 'ThoiGianTao');
        return response()->json([
            'baiGiang' => $baiGiang,
            'baiKiemTra' => $baiKiemTra,
            'thongBao' => $thongBao
        ]);
    }
}
