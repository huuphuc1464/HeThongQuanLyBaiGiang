<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Models\Khoa;
use App\Models\MonHoc;
use App\Models\HocPhan;
use App\Models\LopHocPhan;
use App\Models\DanhSachLop;
use App\Models\BaiGiang;
use App\Models\BaiKiemTra;
use App\Models\ThongBao;
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
            'tongMonHoc' => MonHoc::where('TrangThai', 1)->count(),
            'tongGiangVien' => NguoiDung::where('MaVaiTro', 2)->where('TrangThai', 1)->count(), // MaVaiTro = 2: Giảng viên
            'tongSinhVien' => NguoiDung::where('MaVaiTro', 3)->where('TrangThai', 1)->count(), // MaVaiTro = 3: Sinh viên
        ];

        // Thống kê hoạt động đào tạo
        $thongKeDaoTao = [
            'tongHocPhan' => HocPhan::where('TrangThai', 1)->count(),
            'tongLopHocPhan' => LopHocPhan::where('TrangThai', 1)->count(),
            'topKhoaNhieuMonHoc' => $this->getTopKhoaNhieuMonHoc(),
            'topGiangVienNhieuHocPhan' => $this->getTopGiangVienNhieuHocPhan(),
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
            'thongKeTheoThang' => $this->getThongKeTheoThang(),
        ];

        return view('admin.dashboard', compact('thongKeTongQuan', 'thongKeDaoTao', 'thongKeSinhVien', 'thongKeHeThong'));
    }

    private function getTopKhoaNhieuMonHoc()
    {
        return Khoa::select('khoa.TenKhoa', DB::raw('COUNT(mon_hoc.MaMonHoc) as soMonHoc'))
            ->leftJoin('mon_hoc', 'khoa.MaKhoa', '=', 'mon_hoc.MaKhoa')
            ->where('khoa.TrangThai', 1)
            ->where('mon_hoc.TrangThai', 1)
            ->groupBy('khoa.MaKhoa', 'khoa.TenKhoa')
            ->orderBy('soMonHoc', 'desc')
            ->limit(5)
            ->get();
    }

    private function getTopGiangVienNhieuHocPhan()
    {
        return NguoiDung::select('nguoi_dung.HoTen', DB::raw('COUNT(hoc_phan.MaHocPhan) as soHocPhan'))
            ->leftJoin('hoc_phan', 'nguoi_dung.MaNguoiDung', '=', 'hoc_phan.MaNguoiTao')
            ->where('nguoi_dung.MaVaiTro', 2) // Giảng viên
            ->where('nguoi_dung.TrangThai', 1)
            ->where('hoc_phan.TrangThai', 1)
            ->groupBy('nguoi_dung.MaNguoiDung', 'nguoi_dung.HoTen')
            ->orderBy('soHocPhan', 'desc')
            ->limit(5)
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

    private function getThongKeTheoThang()
    {
        $currentYear = date('Y');
        
        return [
            'baiGiangTheoThang' => $this->getThongKeTheoThangByModel(BaiGiang::class, $currentYear),
            'baiKiemTraTheoThang' => $this->getThongKeTheoThangByModel(BaiKiemTra::class, $currentYear),
        ];
    }

    private function getThongKeTheoThangByModel($model, $year)
    {
        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            try {
                $count = $model::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('TrangThai', 1)
                    ->count();
                $data[] = $count;
            } catch (\Exception $e) {
                $data[] = 0; // Nếu có lỗi, trả về 0
            }
        }
        return $data;
    }

    //Kiểm tra xem có dữ liệu thống kê không
    private function hasData()
    {
        return Khoa::where('TrangThai', 1)->count() > 0 ||
               MonHoc::where('TrangThai', 1)->count() > 0 ||
               NguoiDung::where('TrangThai', 1)->count() > 0;
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
}
