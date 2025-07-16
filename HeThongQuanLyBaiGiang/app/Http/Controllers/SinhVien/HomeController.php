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
    public function hienThiDanhSachBaiGiang(Request $request)
    {
        $maSinhVien = Auth::user()->MaNguoiDung;
        $query = DB::table('lop_hoc_phan as lhp')
            ->join('bai_giang as bg', 'lhp.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->join('nguoi_dung as nd', 'lhp.MaNguoiTao', '=', 'nd.MaNguoiDung')
            ->join('danh_sach_lop as dsl', function ($join) {
                $join->on('dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
                    ->where('dsl.TrangThai', '=', 1);
            })
            ->where('lhp.TrangThai', 1)
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
                        ->orWhereRaw('LOWER(bg.TenBaiGiang) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(nd.HoTen) LIKE ?', ["%$kw%"]);
                }
            });
        }

        if ($request->filled('giang_vien') && $request->filled('bai_giang')) {
            $query->where('nd.MaNguoiDung', '=', $request->giang_vien)
                ->where('bg.MaBaiGiang', '=', $request->bai_giang);
        }

        $danhSachBaiGiang = $query->select(
            'lhp.MaLopHocPhan',
            'lhp.TenLopHocPhan',
            'bg.TenBaiGiang',
            'lhp.MoTa',
            'nd.HoTen as TenGiangVien',
            'nd.AnhDaiDien as AnhGiangVien',
            'bg.AnhBaiGiang',
            DB::raw('COUNT(DISTINCT dsl.MaSinhVien) as SoLuongSinhVien')
        )
            ->groupBy(
                'lhp.MaLopHocPhan',
                'lhp.TenLopHocPhan',
                'bg.TenBaiGiang',
                'lhp.MoTa',
                'nd.HoTen',
                'nd.AnhDaiDien',
                'bg.AnhBaiGiang'
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

        $search = request('search');

        $baiGiangsQuery = DB::table('bai as b')
            ->join('chuong as c', 'b.MaChuong', '=', 'c.MaChuong')
            ->join('bai_giang as bg', 'c.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->join('lop_hoc_phan as lhp', 'lhp.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
            ->where('lhp.MaLopHocPhan', $id)
            ->where('dsl.MaSinhVien', '=', Auth::id())
            ->where('dsl.TrangThai', '=', 1)
            ->where('b.TrangThai', 1)
            ->where('c.TrangThai', 1)
            ->where('bg.TrangThai', 1);

        if (!empty($search)) {
            $keywords = preg_split('/\s+/', trim($search));
            $baiGiangsQuery->where(function ($query) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $query->orWhereRaw('LOWER(b.TenBai) LIKE ?', ["%$kw%"])
                        // ->orWhereRaw('LOWER(c.TenChuong) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(b.NoiDung) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(b.MoTa) LIKE ?', ["%$kw%"]);
                }
            });
        }

        $baiGiangs = $baiGiangsQuery
            ->orderBy('c.TenChuong')
            ->orderBy('b.TenBai')
            ->orderBy('b.created_at')
            ->select(
                'c.TenChuong',
                'b.TenBai',
                'b.TenBai as TenBaiGiang',
                'b.MaBai as MaBaiGiang',
                'b.updated_at',
                'b.created_at'
            )
            ->get()
            ->groupBy('TenChuong');
        // ->map(fn($chuong) => $chuong->groupBy('TenBai'));

        return compact('lopHocPhan', 'giangVien', 'baiGiangs');
    }

    private function danhSachSuKienZoom($id)
    {
        $search = request('search');

        $query = DB::table('su_kien_zoom as sk')
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
            );

        if (!empty($search)) {
            $keywords = preg_split('/\s+/', trim($search));
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $q->orWhereRaw('LOWER(sk.TenSuKien) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(sk.MoTa) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(sk.LinkSuKien) LIKE ?', ["%$kw%"]);
                }
            });
        }

        $suKiens = $query
            ->orderBy('sk.ThoiGianBatDau', 'asc')
            ->get();

        return ['suKiens' => $suKiens];
    }

    private function danhSachLop($id)
    {
        $search = request('search');

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

        $sinhVienQuery = DB::table('nguoi_dung as nd')
            ->join('danh_sach_lop as dsl', 'nd.MaNguoiDung', '=', 'dsl.MaSinhVien')
            ->where('dsl.MaLopHocPhan', $id)
            ->where('dsl.TrangThai', 1)
            ->select('nd.HoTen', 'nd.AnhDaiDien');

        if (!empty($search)) {
            $keywords = preg_split('/\s+/', trim($search));

            $sinhVienQuery->where(function ($query) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $query->orWhereRaw('LOWER(nd.HoTen) LIKE ?', ["%$kw%"]);
                }
            });
        }

        $sinhViens = $sinhVienQuery->get();

        return ['sinhViens' => $sinhViens, 'giangVien' => $giangVien];
    }

    /**
     * Hiển thị danh sách lớp học phần lưu trữ cho sinh viên
     */
    public function lopHocPhanLuuTru()
    {
        $maSinhVien = Auth::user()->MaNguoiDung;
        $danhSachLopLuuTru = DB::table('lop_hoc_phan as lhp')
            ->join('bai_giang as bg', 'lhp.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->join('nguoi_dung as nd', 'lhp.MaNguoiTao', '=', 'nd.MaNguoiDung')
            ->join('danh_sach_lop as dsl', function ($join) {
                $join->on('dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
                    ->where('dsl.TrangThai', '=', 1);
            })
            ->where('lhp.TrangThai', 3) // 3: lưu trữ
            ->whereExists(function ($query) use ($maSinhVien) {
                $query->select(DB::raw(1))
                    ->from('danh_sach_lop')
                    ->whereColumn('MaLopHocPhan', 'lhp.MaLopHocPhan')
                    ->where('MaSinhVien', $maSinhVien)
                    ->where('TrangThai', 1);
            })
            ->select(
                'lhp.MaLopHocPhan',
                'lhp.TenLopHocPhan',
                'bg.TenBaiGiang',
                'lhp.MoTa',
                'nd.HoTen as TenGiangVien',
                'nd.AnhDaiDien as AnhGiangVien',
                'bg.AnhBaiGiang',
                DB::raw('COUNT(DISTINCT dsl.MaSinhVien) as SoLuongSinhVien')
            )
            ->groupBy(
                'lhp.MaLopHocPhan',
                'lhp.TenLopHocPhan',
                'bg.TenBaiGiang',
                'lhp.MoTa',
                'nd.HoTen',
                'nd.AnhDaiDien',
                'bg.AnhBaiGiang'
            )
            ->get();
        return view('sinhvien.lopHocPhanLuuTru', compact('danhSachLopLuuTru'));
    }


    public function renderTab(Request $request, $id, $tab = 'bai-giang')
    {
        $baiGiang = DB::table('bai_giang as bg')
            ->join('lop_hoc_phan as lhp', 'lhp.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->where('lhp.MaLopHocPhan', $id)
            ->select('bg.MaBaiGiang', 'bg.TenBaiGiang')
            ->first();
        switch ($tab) {
            case 'bai-kiem-tra':
                return redirect()->route('danh-sach-bai-kiem-tra', $id);

            case 'su-kien-zoom':
                return view('sinhvien.danhSachSuKienZoom', [
                    'id' => $id,
                    'tab' => $tab,
                    'baiGiang' => $baiGiang,
                    ...$this->danhSachSuKienZoom($id),
                ]);
            case 'moi-nguoi':
                return view('sinhvien.danhSachLop', [
                    'id' => $id,
                    'tab' => $tab,
                    'baiGiang' => $baiGiang,
                    ...$this->danhSachLop($id),
                ]);
            default:
                return view('sinhvien.danhSachBaiGiang', [
                    'id' => $id,
                    'tab' => $tab,
                    'baiGiang' => $baiGiang,
                    ...$this->danhSachBaiGiang($id),
                ]);
        }
    }

    public function chiTietBaiGiang($id, $maBaiGiang)
    {
        $sinhVienId = Auth::id();

        $bai = DB::table('bai as b')
            ->join('chuong as c', 'b.MaChuong', '=', 'c.MaChuong')
            ->join('bai_giang as bg', 'c.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->join('lop_hoc_phan as lhp', 'lhp.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
            ->where('lhp.MaLopHocPhan', $id)
            ->where('b.MaBai', $maBaiGiang)
            ->where('dsl.MaSinhVien', $sinhVienId)
            ->where('dsl.TrangThai', 1)
            ->select('b.*', 'c.TenChuong', 'bg.TenBaiGiang as TenBaiGiangCha', 'bg.MaBaiGiang')
            ->first();

        if (!$bai) {
            abort(404, 'Không tìm thấy bài giảng');
        }

        $dsBai = DB::table('bai as b')
            ->join('chuong as c', 'b.MaChuong', '=', 'c.MaChuong')
            ->join('bai_giang as bg', 'c.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->join('lop_hoc_phan as lhp', 'lhp.MaBaiGiang', '=', 'bg.MaBaiGiang')
            ->join('danh_sach_lop as dsl', 'dsl.MaLopHocPhan', '=', 'lhp.MaLopHocPhan')
            ->where('lhp.MaLopHocPhan', $id)
            ->where('dsl.MaSinhVien', Auth::id())
            ->where('dsl.TrangThai', 1)
            ->select('b.MaBai', 'b.TenBai')
            ->orderBy('b.created_at')
            ->get()
            ->values();

        $currentIndex = $dsBai->search(fn($item) => $item->MaBai == $maBaiGiang);

        $baiTruoc = $currentIndex > 0 ? $dsBai[$currentIndex - 1] : null;
        $baiSau = $currentIndex < $dsBai->count() - 1 ? $dsBai[$currentIndex + 1] : null;

        $files = DB::table('file_bai_giang')
            ->where('MaBai', $maBaiGiang)
            ->where('TrangThai', 1)
            ->get();

        $tab = 'bai-giang';
        return view('sinhvien.chiTietBaiGiang', compact('bai', 'files', 'tab', 'id', 'baiTruoc', 'baiSau'));
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
