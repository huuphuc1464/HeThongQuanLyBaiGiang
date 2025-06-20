<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ElfinderController;
use App\Models\BaiGiang;
use App\Models\DanhSachLop;
use App\Models\FileBaiGiang;
use App\Models\HocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BaiGiangController extends Controller
{

    public function danhSachBaiGiang(Request $request, $id)
    {
        $query = DB::table('bai_giang')
            ->where('MaGiangVien', Auth::id())
            ->where('MaHocPhan', $id);

        if ($request->filled('search')) {
            $keywords = preg_split('/\s+/', trim($request->search));

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $q->orWhereRaw('LOWER(TenChuong) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(TenBai) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(TenBaiGiang) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(MoTa) LIKE ?', ["%$kw%"]);
                }
            });
        }

        $baiGiangs = $query
            ->orderBy('TenChuong')
            ->orderBy('TenBai')
            ->orderBy('TenBaiGiang')
            ->orderBy('created_at')
            ->get()
            ->groupBy('TenChuong')
            ->map(fn($chuong) => $chuong->groupBy('TenBai'));

        $hocPhan = DB::table('hoc_phan')->where('MaHocPhan', $id)->select('MaHocPhan', 'TenHocPhan')->first();

        return view('giangvien.quanLyBaiGiang.danhSachBaiGiang', compact('baiGiangs', 'hocPhan'));
    }

    public function hienFormSua(Request $request, $maHocPhan, $maBaiGiang)
    {
        $baiGiang = DB::table('bai_giang')
            ->where('MaGiangVien', Auth::id())
            ->where('MaHocPhan', $maHocPhan)
            ->where('MaBaiGiang', $maBaiGiang)
            ->first();

        $hocPhan = DB::table('hoc_phan')
            ->where('MaHocPhan', $maHocPhan)
            ->where('MaNguoiTao', Auth::id())
            ->select('MaHocPhan', 'TenHocPhan')
            ->first();
        $baiGiangs = BaiGiang::where('MaHocPhan', $maHocPhan)
            ->select('TenChuong', 'TenBai')
            ->whereNotNull('TenChuong')
            ->whereNotNull('TenBai')
            ->get();

        // Gom nhóm theo chương
        $chuongBai = [];

        foreach ($baiGiangs as $bg) {
            $tenChuong = trim($bg->TenChuong);
            $tenBai = trim($bg->TenBai);

            if (!$tenChuong || !$tenBai) continue;

            if (!isset($chuongBai[$tenChuong])) {
                $chuongBai[$tenChuong] = [];
            }

            if (!in_array($tenBai, $chuongBai[$tenChuong])) {
                $chuongBai[$tenChuong][] = $tenBai;
            }
        }

        return view('giangvien.quanLyBaiGiang.suaBaiGiang', compact('baiGiang', 'hocPhan', 'chuongBai'));
    }

    public function thayDoiTrangThai($maHocPhan, $maBaiGiang)
    {
        $baiGiang = BaiGiang::where('MaHocPhan', $maHocPhan)
            ->where('MaBaiGiang', $maBaiGiang)
            ->firstOrFail();

        $baiGiang->TrangThai = $baiGiang->TrangThai == 1 ? 0 : 1;
        $baiGiang->updated_at =  now('Asia/Ho_Chi_Minh');
        $baiGiang->save();
        return redirect()->back()->with('success', 'Trạng thái bài giảng đã được cập nhật.');
    }

    public function hienFormThem($id)
    {
        $hocPhan = DB::table('hoc_phan')
            ->where('MaHocPhan', $id)
            ->select('MaHocPhan', 'TenHocPhan')
            ->first();

        // Lấy dữ liệu chương và bài từ các bài giảng
        $baiGiangs = BaiGiang::where('MaHocPhan', $id)
            ->select('TenChuong', 'TenBai')
            ->whereNotNull('TenChuong')
            ->whereNotNull('TenBai')
            ->get();

        // Gom nhóm theo chương
        $chuongBai = [];

        foreach ($baiGiangs as $bg) {
            $tenChuong = trim($bg->TenChuong);
            $tenBai = trim($bg->TenBai);

            if (!$tenChuong || !$tenBai) continue;

            if (!isset($chuongBai[$tenChuong])) {
                $chuongBai[$tenChuong] = [];
            }

            if (!in_array($tenBai, $chuongBai[$tenChuong])) {
                $chuongBai[$tenChuong][] = $tenBai;
            }
        }
        return view('giangvien.quanLyBaiGiang.themBaiGiang', compact('hocPhan', 'chuongBai'));
    }

    public function themBaiGiang(Request $request, $maHocPhan)
    {
        $request->validate([
            'TenChuong' => 'required|string|max:255',
            'TenBai' => 'required|string|max:255',
            'TenBaiGiang' => 'required|string|max:255',
            'NoiDung' => 'required|string',
            'MoTa' => 'nullable|string|max:255',
        ], [
            'TenChuong.required' => 'Tên chương không được để trống.',
            'TenChuong.string' => 'Tên chương phải là chuỗi.',
            'TenChuong.max' => 'Tên chương không được vượt quá 255 ký tự.',
            'TenBai.required' => 'Tên bài không được để trống.',
            'TenBai.string' => 'Tên bài phải là chuỗi.',
            'TenBai.max' => 'Tên bài không được vượt quá 255 ký tự.',
            'TenBaiGiang.required' => 'Tên bài giảng không được để trống.',
            'TenBaiGiang.string' => 'Tên bài giảng phải là chuỗi.',
            'TenBaiGiang.max' => 'Tên bài giảng không được vượt quá 255 ký tự.',
            'NoiDung.required' => 'Nội dung bài giảng không được để trống.',
            'NoiDung.string' => 'Nội dung phải là chuỗi.',
            'MoTa.string' => 'Mô tả phải là chuỗi.',
            'MoTa.max' => 'Mô tả không được vượt quá 255 ký tự.',
        ]);

        $maNguoiDung = Auth::id();
        $oldPath = "BaiGiang/HocPhan_{$maHocPhan}/temp_{$maNguoiDung}";
        $oldFolder = public_path($oldPath);

        DB::beginTransaction();

        try {
            $baiGiang = new BaiGiang();
            $baiGiang->MaGiangVien = $maNguoiDung;
            $baiGiang->MaHocPhan = $maHocPhan;
            $baiGiang->TenChuong = $request->TenChuong;
            $baiGiang->TenBai = $request->TenBai;
            $baiGiang->TenBaiGiang = $request->TenBaiGiang;
            $baiGiang->NoiDung = $request->NoiDung;
            $baiGiang->MoTa = $request->MoTa;
            $baiGiang->TrangThai = 1;
            $baiGiang->created_at = now('Asia/Ho_Chi_Minh');
            $baiGiang->updated_at = now('Asia/Ho_Chi_Minh');
            $baiGiang->save();

            // Đổi tên thư mục chứa file
            $newPath = "BaiGiang/HocPhan_{$maHocPhan}/{$baiGiang->MaBaiGiang}";
            $newFolder = public_path($newPath);

            if (file_exists($oldFolder)) {
                rename($oldFolder, $newFolder);
            }

            // Cập nhật đường dẫn ảnh trong nội dung bài giảng
            $baiGiang->NoiDung = str_replace($oldPath, $newPath, $baiGiang->NoiDung);
            $baiGiang->save();

            // Lưu danh sách file
            if (File::exists($newFolder)) {
                $files = File::files($newFolder);

                foreach ($files as $file) {
                    $relativePath = str_replace(public_path(), '', $file->getPathname());
                    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

                    FileBaiGiang::create([
                        'MaBaiGiang' => $baiGiang->MaBaiGiang,
                        'DuongDan'   => $relativePath,
                        'LoaiFile'   => $file->getExtension(),
                        'TrangThai'  => 1,
                        'created_at' => now('Asia/Ho_Chi_Minh'),
                        'updated_at' => now('Asia/Ho_Chi_Minh'),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('giang-vien.bai-giang', ['id' => $maHocPhan])->with('success', 'Thêm bài giảng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Xoá bản ghi bài giảng nếu đã tạo
            if (isset($baiGiang) && $baiGiang->exists) {
                $baiGiang->delete();
            }

            // Xoá thư mục đã di chuyển (nếu có)
            if (isset($newFolder) && File::exists($newFolder)) {
                File::deleteDirectory($newFolder);
            }

            Log::error('Lỗi khi lưu bài giảng', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Lỗi khi lưu bài giảng: ' . $e->getMessage());
        }
    }


    public function capNhatBaiGiang(Request $request, $maHocPhan, $maBaiGiang)
    {
        $request->validate([
            'TenChuong' => 'required|string|max:255',
            'TenBai' => 'required|string|max:255',
            'TenBaiGiang' => 'required|string|max:255',
            'NoiDung' => 'required|string',
            'MoTa' => 'nullable|string|max:255',
            'TrangThai' => 'required|in:0,1',
        ], [
            'TenChuong.required' => 'Tên chương không được để trống.',
            'TenChuong.string' => 'Tên chương phải là chuỗi.',
            'TenChuong.max' => 'Tên chương không được vượt quá 255 ký tự.',
            'TenBai.required' => 'Tên bài không được để trống.',
            'TenBai.string' => 'Tên bài phải là chuỗi.',
            'TenBai.max' => 'Tên bài không được vượt quá 255 ký tự.',
            'TenBaiGiang.required' => 'Tên bài giảng không được để trống.',
            'TenBaiGiang.string' => 'Tên bài giảng phải là chuỗi.',
            'TenBaiGiang.max' => 'Tên bài giảng không được vượt quá 255 ký tự.',
            'NoiDung.required' => 'Nội dung bài giảng không được để trống.',
            'NoiDung.string' => 'Nội dung phải là chuỗi.',
            'MoTa.string' => 'Mô tả phải là chuỗi.',
            'MoTa.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'TrangThai.required' => 'Trạng thái bài giảng không được để trống.',
            'TrangThai.in' => 'Trạng thái bài giảng chỉ chấp nhận giá trị 0 và 1.'
        ]);

        DB::beginTransaction();

        try {
            $maNguoiDung = Auth::id();

            $baiGiang = BaiGiang::where('MaBaiGiang', $maBaiGiang)
                ->where('MaGiangVien', $maNguoiDung)
                ->where('MaHocPhan', $maHocPhan)
                ->firstOrFail();

            $baiGiang->TenChuong = $request->TenChuong;
            $baiGiang->TenBai = $request->TenBai;
            $baiGiang->TenBaiGiang = $request->TenBaiGiang;
            $baiGiang->NoiDung = $request->NoiDung;
            $baiGiang->MoTa = $request->MoTa;
            $baiGiang->TrangThai = $request->TrangThai;
            $baiGiang->updated_at = now('Asia/Ho_Chi_Minh');
            $baiGiang->save();

            $folderPath = public_path("BaiGiang/HocPhan_{$maHocPhan}/{$maBaiGiang}");
            if (File::exists($folderPath)) {
                $filePathsOnDisk = collect(File::files($folderPath))->map(function ($file) {
                    return ltrim(str_replace(public_path(), '', $file->getPathname()), '/');
                })->toArray();

                $filePathsInDb = FileBaiGiang::where('MaBaiGiang', $maBaiGiang)->pluck('DuongDan')->toArray();

                // Xóa khỏi DB nếu file không còn trong thư mục
                foreach ($filePathsInDb as $dbPath) {
                    if (!in_array($dbPath, $filePathsOnDisk)) {
                        FileBaiGiang::where('MaBaiGiang', $maBaiGiang)
                            ->where('DuongDan', $dbPath)
                            ->delete();

                        Log::info("Đã xóa bản ghi file không tồn tại trên ổ đĩa: $dbPath");
                    }
                }

                // Thêm file vào DB nếu tồn tại trong thư mục mà chưa có trong DB
                foreach ($filePathsOnDisk as $diskPath) {
                    if (!in_array($diskPath, $filePathsInDb)) {
                        FileBaiGiang::create([
                            'MaBaiGiang' => $maBaiGiang,
                            'DuongDan'   => $diskPath,
                            'LoaiFile'   => pathinfo($diskPath, PATHINFO_EXTENSION),
                            'TrangThai'  => 1,
                            'created_at' => now('Asia/Ho_Chi_Minh'),
                            'updated_at' => now('Asia/Ho_Chi_Minh'),
                        ]);
                    }
                }
            }
            DB::commit();
            return redirect()->route('giang-vien.bai-giang', ['id' => $maHocPhan])->with('success', 'Cập nhật bài giảng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    public function ChiTietBaiGiang($maHocPhan, $maBaiGiang)
    {
        $baiGiang = BaiGiang::where('MaHocPhan', $maHocPhan)
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->firstOrFail();

        $hocPhan = HocPhan::select('MaHocPhan', 'TenHocPhan')
            ->where('MaHocPhan', $maHocPhan)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();

        $files = FileBaiGiang::where('MaBaiGiang', $maBaiGiang)->get();

        return view('giangvien.quanLyBaiGiang.chiTietBaiGiang', compact('baiGiang', 'hocPhan', 'files'));
    }

    public function huyBoBaiGiang(Request $request)
    {
        $maHocPhan = $request->MaHocPhan;
        $maBaiGiang = $request->MaBaiGiang ?? null;
        $maNguoiDung = Auth::id();

        if ($maBaiGiang) {
            // Form cập nhật
            $folderPath = public_path("BaiGiang/HocPhan_{$maHocPhan}/{$maBaiGiang}");
            $duongDanDB = FileBaiGiang::where('MaBaiGiang', $maBaiGiang)->pluck('DuongDan')->toArray();
        } else {
            // Form thêm
            $folderPath = public_path("BaiGiang/HocPhan_{$maHocPhan}/temp_{$maNguoiDung}");
            $duongDanDB = []; // chưa có gì trong DB
        }

        if (File::exists($folderPath)) {
            foreach (File::files($folderPath) as $file) {
                $relativePath = ltrim(str_replace(public_path(), '', $file->getPathname()), '/');

                if (!in_array($relativePath, $duongDanDB)) {
                    unlink($file->getPathname());
                    Log::info("Đã xóa file chưa lưu DB: $relativePath");
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function thongKeBaiGiang($id)
    {
        $tongBaiGiang = DB::table('bai_giang')
            ->where('MaHocPhan', $id)
            ->count();

        $tongChuong = DB::table('bai_giang')
            ->where('MaHocPhan', $id)
            ->distinct()
            ->count('TenChuong');

        $tongBai = DB::table('bai_giang')
            ->where('MaHocPhan', $id)
            ->distinct()
            ->count('TenBai');

        $tongFile = DB::table('file_bai_giang')
            ->join('bai_giang', 'file_bai_giang.MaBaiGiang', '=', 'bai_giang.MaBaiGiang')
            ->where('bai_giang.MaHocPhan', $id)
            ->count();

        $tongSinhVien = DB::table('danh_sach_lop')
            ->join('lop_hoc_phan', 'danh_sach_lop.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->where('lop_hoc_phan.MaHocPhan', $id)
            ->distinct()
            ->count('danh_sach_lop.MaSinhVien');

        $thongKeTheoThang = DB::table('bai_giang')
            ->selectRaw('MONTH(created_at) as thang, COUNT(*) as so_luong')
            ->where('MaHocPhan', $id)
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('so_luong', 'thang');

        $filePaths = DB::table('file_bai_giang')
            ->join('bai_giang', 'bai_giang.MaBaiGiang', '=', 'file_bai_giang.MaBaiGiang')
            ->where('bai_giang.MaHocPhan', $id)
            ->pluck('file_bai_giang.DuongDan');

        // Tính tổng dung lượng file trên đĩa
        $tongDungLuong = 0;
        foreach ($filePaths as $path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                $tongDungLuong += filesize($fullPath);
            }
        }
        $tongDungLuong = round($tongDungLuong / 1024 / 1024, 2);

        $hocPhan = DB::table('hoc_phan')
            ->where('MaHocPhan', $id)
            ->select('MaHocPhan', 'TenHocPhan')
            ->first();

        $namThongKe = DB::table('bai_giang')
            ->where('MaHocPhan', $id)
            ->selectRaw('YEAR(created_at) as nam')
            ->union(
                DB::table('bai_giang')
                    ->where('MaHocPhan', $id)
                    ->selectRaw('YEAR(updated_at) as nam')
            )
            ->distinct()
            ->orderByDesc('nam')
            ->pluck('nam');

        return view('giangvien.quanLyBaiGiang.thongKeBaiGiang', [
            'hocPhanId' => $id,
            'tongBaiGiang' => $tongBaiGiang,
            'tongChuong' => $tongChuong,
            'tongBai' => $tongBai,
            'tongFile' => $tongFile,
            'tongSinhVien' => $tongSinhVien,
            'tongDungLuong' => $tongDungLuong,
            'thongKeTheoThang' => $thongKeTheoThang,
            'namThongKe' => $namThongKe,
            'hocPhan' => $hocPhan
        ]);
    }

    public function layDuLieuBieuDoThongKe(Request $request, $id)
    {
        $nam = $request->query('nam');
        $data = DB::table('bai_giang')
            ->select(DB::raw('MONTH(created_at) as thang'), DB::raw('count(*) as tong'))
            ->whereYear('created_at', $nam)
            ->where('MaHocPhan', $id)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('tong', 'thang');

        return response()->json($data);
    }
}
