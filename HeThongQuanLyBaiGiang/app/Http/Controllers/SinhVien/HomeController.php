<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\BaiGiang;
use App\Models\LopHocPhan;
use App\Models\NguoiDung;
use App\Models\BaiKiemTra;
use App\Models\SinhVien;
use App\Models\KetQuaBaiKiemTra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function hienThiDanhSachBaiGiang(Request $request)
    {
        $maSinhVien = Auth::user()->MaNguoiDung;
        $query = DB::table('lop_hoc_phan as lhp')
            ->join('hoc_phan as hp', 'lhp.MaHocPhan', '=', 'hp.MaHocPhan')
            ->join('nguoi_dung as nd', 'lhp.MaNguoiTao', '=', 'nd.MaNguoiDung')
            ->join('danh_sach_lop as dsl', function ($join) {
                $join->on('dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
                    ->where('dsl.TrangThai', '=', 1);
            })
            ->whereExists(function ($query) use ($maSinhVien) {
                $query->select(DB::raw(1))
                    ->from('danh_sach_lop')
                    ->whereColumn('MaLopHocPhan', 'lhp.MaLopHocPhan')
                    ->where('MaSinhVien', $maSinhVien)
                    ->where('TrangThai', 1);
            });

        if ($request->filled('search')) {
            $keywords = preg_split('/\s+/', trim($request->search));

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $q->orWhereRaw('LOWER(lhp.TenLopHocPhan) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(hp.TenHocPhan) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(nd.HoTen) LIKE ?', ["%$kw%"]);
                }
            });
        }

        if ($request->filled('giang_vien') && $request->filled('mon_hoc')) {
            $query->where('nd.MaNguoiDung', '=', $request->giang_vien)
                ->where('hp.MaMonHoc', '=', $request->mon_hoc);
        }

        $danhSachBaiGiang = $query->select(
            'lhp.MaLopHocPhan',
            'lhp.TenLopHocPhan',
            'hp.TenHocPhan',
            'lhp.MoTa',
            'nd.HoTen as TenGiangVien',
            'nd.AnhDaiDien as AnhGiangVien',
            'hp.AnhHocPhan',
            DB::raw('COUNT(DISTINCT dsl.MaSinhVien) as SoLuongSinhVien')
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

    private function danhSachBaiGiang($id)
    {
        $lopHocPhan = DB::table('lop_hoc_phan')->where('MaLopHocPhan', $id)->first();

        if (!$lopHocPhan) {
            return [];
        }

        $giangVien = DB::table('nguoi_dung')
            ->where('MaNguoiDung', $lopHocPhan->MaNguoiTao)
            ->first();

        $baiGiangs = DB::table('bai_giang as bg')
            ->join('lop_hoc_phan as lhp', function ($join) {
                $join->on('bg.MaHocPhan', '=', 'lhp.MaHocPhan')
                    ->on('lhp.MaNguoiTao', '=', 'bg.MaGiangVien');
            })
            ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
            ->where('lhp.MaLopHocPhan', $id)
            ->where('dsl.MaSinhVien', '=', Auth::id())
            ->where('dsl.TrangThai', '=', 1)
            ->where('bg.TrangThai', 1)
            ->select(
                'bg.TenChuong',
                'bg.TenBai',
                'bg.TenBaiGiang',
                'bg.MaBaiGiang',
                'bg.updated_at',
                'bg.created_at'
            )
            ->orderBy('bg.TenChuong')
            ->orderBy('bg.TenBai')
            ->orderBy('bg.TenBaiGiang')
            ->orderBy('bg.created_at')
            ->get()
            ->groupBy('TenChuong')
            ->map(fn($chuong) => $chuong->groupBy('TenBai'));

        return compact('lopHocPhan', 'giangVien', 'baiGiangs');
    }

    private function danhSachSuKienZoom($id)
    {
        $suKiens = DB::table('su_kien_zoom as sk')
            ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'sk.MaLopHocPhan')
            ->where('dsl.MaSinhVien', '=', Auth::id())
            ->where('dsl.TrangThai', '=', 1)
            ->where('sk.MaLopHocPhan', $id)
            ->select(
                'sk.MaSuKienZoom',
                'sk.TenSuKien',
                'sk.MoTa',
                'sk.ThoiGianBatDau',
                'sk.ThoiGianKetThuc',
                'sk.LinkSuKien',
                'sk.MatKhauSuKien',
                'sk.created_at',
                'sk.updated_at',
            )
            ->orderBy('sk.ThoiGianBatDau', 'asc')
            ->get();

        return ['suKiens' => $suKiens];
    }

    private function danhSachLop($id)
    {
        $giangVien = DB::table('nguoi_dung as nd')
            ->join('lop_hoc_phan as lhp', 'lhp.MaNguoiTao', '=', 'nd.MaNguoiDung')
            ->where('lhp.MaLopHocPhan', $id)
            ->select('nd.HoTen', 'nd.AnhDaiDien')
            ->first();

        $daThamGia = DB::table('danh_sach_lop')
            ->where('MaLopHocPhan', $id)
            ->where('MaSinhVien', Auth::id())
            ->where('TrangThai', 1)
            ->exists();

        if (!$daThamGia) {
            abort(404, 'Bạn không có quyền truy cập vào lớp học phần này');
        }

        $sinhViens = DB::table('nguoi_dung as nd')
            ->join('danh_sach_lop as dsl', 'nd.MaNguoiDung', '=', 'dsl.MaSinhVien')
            ->where('dsl.MaLopHocPhan', $id)
            ->where('dsl.TrangThai', 1)
            ->select('nd.HoTen', 'nd.AnhDaiDien')
            ->get();

        return ['sinhViens' => $sinhViens, 'giangVien' => $giangVien];
    }


    public function renderTab(Request $request, $id, $tab = 'bai-giang')
    {
        $hocPhan = DB::table('hoc_phan')
            ->where('MaHocPhan', $id)
            ->select('MaHocPhan', 'TenHocPhan')
            ->first();
        switch ($tab) {
            case 'bai-kiem-tra':
                return redirect()->route('danh-sach-bai-kiem-tra');

            case 'su-kien-zoom':
                return view('sinhvien.danhSachSuKienZoom', [
                    'id' => $id,
                    'tab' => $tab,
                    'hocPhan' => $hocPhan,
                    ...$this->danhSachSuKienZoom($id),
                ]);
            case 'moi-nguoi':
                return view('sinhvien.danhSachLop', [
                    'id' => $id,
                    'tab' => $tab,
                    'hocPhan' => $hocPhan,
                    ...$this->danhSachLop($id),
                ]);
            default:
                return view('sinhvien.danhSachBaiGiang', [
                    'id' => $id,
                    'tab' => $tab,
                    'hocPhan' => $hocPhan,
                    ...$this->danhSachBaiGiang($id),
                ]);
        }
    }

    public function chiTietBaiGiang($id, $maBaiGiang)
    {
        $baiGiang = DB::table('bai_giang as bg')
            ->join('lop_hoc_phan as lhp', 'lhp.MaHocPhan', '=', 'bg.MaHocPhan')
            ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
            ->where('lhp.MaLopHocPhan', $id)
            ->where('bg.MaBaiGiang', $maBaiGiang)
            ->where('dsl.MaSinhVien', Auth::id())
            ->where('dsl.TrangThai', '=', 1)
            ->select('bg.*')
            ->first();

        if (!$baiGiang) {
            abort(404, 'Không tìm thấy bài giảng');
        }

        $files = DB::table('file_bai_giang')->where('MaBaiGiang', $maBaiGiang)
            ->where('TrangThai', 1)
            ->get();
        $tab = 'bai-giang';
        return view('sinhvien.chiTietBaiGiang', compact('baiGiang', 'files', 'tab', 'id'));
    }

    public function chiTietSuKienZoom($id, $maSuKien)
    {
        $suKien = DB::table('su_kien_zoom as sk')
            ->join('nguoi_dung', 'nguoi_dung.MaNguoiDung', '=', 'sk.MaGiangVien')
            ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'sk.MaLopHocPhan')
            ->where('dsl.MaSinhVien', '=', Auth::id())
            ->where('dsl.TrangThai', '=', 1)
            ->where('sk.MaLopHocPhan', $id)
            ->where('sk.MaSuKienZoom', $maSuKien)
            ->select(
                'sk.TenSuKien',
                'sk.MoTa',
                'sk.ThoiGianBatDau',
                'sk.ThoiGianKetThuc',
                'sk.LinkSuKien',
                'sk.MatKhauSuKien',
                'sk.created_at',
                'sk.updated_at',
                'nguoi_dung.HoTen as TenGiangVien'
            )
            ->first();

        if (!$suKien) {
            abort(404, 'Không tìm thấy sự kiện Zoom');
        }

        return view('sinhvien.chiTietSuKienZoom', [
            'suKien' => $suKien,
            'tenGiangVien' => $suKien->TenGiangVien ?? null,
            'tab' => 'su-kien-zoom',
            'id' => $id
        ]);
    }
}
