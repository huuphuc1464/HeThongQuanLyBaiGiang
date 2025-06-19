<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ElfinderController;
use App\Models\BaiGiang;
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

        try {
            $maNguoiDung = Auth::id();

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
            $baiGiang->updated_at =  now('Asia/Ho_Chi_Minh');
            $baiGiang->save();
            Log::info('Đã lưu bài giảng', ['MaBaiGiang' => $baiGiang->MaBaiGiang]);

            $oldFolder = public_path("BaiGiang/HocPhan_{$maHocPhan}/temp_{$maNguoiDung}");
            $newFolder = public_path("BaiGiang/HocPhan_{$maHocPhan}/{$baiGiang->MaBaiGiang}");

            if (file_exists($oldFolder)) {
                rename($oldFolder, $newFolder);
            }

            $oldPath = "BaiGiang/HocPhan_{$maHocPhan}/temp_{$maNguoiDung}";
            $newPath = "BaiGiang/HocPhan_{$maHocPhan}/{$baiGiang->MaBaiGiang}";

            // Cập nhật đường dẫn ảnh trong nội dung bài giảng
            $baiGiang->NoiDung = str_replace($oldPath, $newPath, $baiGiang->NoiDung);
            $baiGiang->save();

            // Đọc file JSON tạm lưu thông tin file đã upload
            $jsonPath = storage_path("app/file_bai_giang/{$maNguoiDung}.json");

            if (file_exists($jsonPath)) {
                $data = json_decode(file_get_contents($jsonPath), true);
                Log::info('File JSON đọc được:', $data);

                foreach ($data['tenFile'] as $duongDanCu) {
                    $duongDan = str_replace("temp_{$maNguoiDung}", $baiGiang->MaBaiGiang, $duongDanCu);
                    FileBaiGiang::create([
                        'MaBaiGiang' => $baiGiang->MaBaiGiang,
                        'DuongDan' => $duongDan,
                        'LoaiFile' => pathinfo($duongDan, PATHINFO_EXTENSION),
                        'TrangThai' => 1,
                        'created_at' => now('Asia/Ho_Chi_Minh'),
                        'updated_at' => now('Asia/Ho_Chi_Minh')
                    ]);
                }

                // Xóa file tạm
                unlink($jsonPath);
            }
            return redirect()->route('giang-vien.bai-giang', ['id' => $maHocPhan])->with('success', 'Thêm bài giảng thành công!');
        } catch (\Exception $e) {
            // Xóa bài giảng nếu đã được lưu mà lỗi xảy ra sau đó
            if (isset($baiGiang) && $baiGiang->exists) {
                $baiGiang->delete();
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
            $baiGiang->updated_at = now('Asia/Ho_Chi_Minh');
            $baiGiang->save();

            Log::info('Đã cập nhật bài giảng', ['MaBaiGiang' => $baiGiang->MaBaiGiang]);

            // Lưu file mới nếu người dùng đã upload trong quá trình cập nhật
            $jsonPath = storage_path("app/file_bai_giang/{$maNguoiDung}.json");
            if (file_exists($jsonPath)) {
                $data = json_decode(file_get_contents($jsonPath), true);
                if (!empty($data['tenFile']) && is_array($data['tenFile'])) {
                    foreach ($data['tenFile'] as $duongDan) {
                        $exists = FileBaiGiang::where('MaBaiGiang', $baiGiang->MaBaiGiang)
                            ->where('DuongDan', $duongDan)
                            ->exists();
                        if (!$exists) {
                            FileBaiGiang::create([
                                'MaBaiGiang' => $baiGiang->MaBaiGiang,
                                'DuongDan' => $duongDan,
                                'LoaiFile' => pathinfo($duongDan, PATHINFO_EXTENSION),
                                'TrangThai' => 1,
                                'created_at' => now('Asia/Ho_Chi_Minh'),
                                'updated_at' => now('Asia/Ho_Chi_Minh'),
                            ]);
                        }
                    }
                }
                Log::info("Đã xóa file JSON sau cập nhật bài giảng", ['path' => $jsonPath]);
            }

            $jsonDeletePath = storage_path("app/file_bai_giang/deletedFiles_{$maNguoiDung}_{$maBaiGiang}.json");
            if (file_exists($jsonDeletePath)) {
                $deletedFiles = json_decode(file_get_contents($jsonDeletePath), true);
                foreach ($deletedFiles as $duongDan) {
                    FileBaiGiang::where('MaBaiGiang', $maBaiGiang)
                        ->where('DuongDan', $duongDan)
                        ->delete();

                    $fullPath = public_path($duongDan);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                        Log::info("Đã xóa file khỏi ổ đĩa: $fullPath");
                    }

                    Log::info("Đã xóa file trong DB: $duongDan");
                }
            }
            $paths = [
                storage_path("app/file_bai_giang/{$maNguoiDung}.json"),
                storage_path("app/file_bai_giang/deletedFiles_{$maNguoiDung}_{$maBaiGiang}.json"),
            ];

            foreach ($paths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                    Log::info("Đã dọn sạch file tạm: $path");
                }
            }

            return redirect()->route('giang-vien.bai-giang', ['id' => $maHocPhan])
                ->with('success', 'Cập nhật bài giảng thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật bài giảng', ['message' => $e->getMessage()]);
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


    public function xoaFileTam(Request $request)
    {
        $maNguoiDung = Auth::id();
        $maHocPhan = $request->input('MaHocPhan');
        $jsonPath = storage_path("app/file_bai_giang/{$maNguoiDung}.json");
        $thuMucTam = public_path("BaiGiang/HocPhan_{$maHocPhan}/temp_{$maNguoiDung}");

        // Xóa file từ JSON
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);

            if (!empty($data['tenFile']) && is_array($data['tenFile'])) {
                foreach ($data['tenFile'] as $duongDan) {
                    $duongDan = urldecode($duongDan);
                    $fullPath = public_path($duongDan);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                        Log::info("Đã xóa file: $fullPath");
                    } else {
                        Log::warning("Không tìm thấy file: $fullPath");
                    }
                }
            }

            // Xóa file JSON
            unlink($jsonPath);
            Log::info("Đã xóa file JSON: $jsonPath");
        } else {
            Log::warning("Không tìm thấy file JSON: $jsonPath");
        }

        // Xóa thư mục tạm nếu tồn tại
        if (file_exists($thuMucTam)) {
            File::deleteDirectory($thuMucTam);
            Log::info("Đã xóa thư mục tạm: $thuMucTam");
        }

        return response()->json(['message' => 'Đã xóa tệp và thư mục tạm']);
    }

    public function xoaFileElfinder(Request $request)
    {
        $maNguoiDung = Auth::id();
        $filePath = urldecode($request->input('path'));
        $maBaiGiang = $request->input('maBaiGiang'); // null nếu là form thêm

        Log::info("Đang xử lý xóa file elfinder: $filePath | baiGiang: $maBaiGiang");

        if ($maBaiGiang) {
            // FORM SỬA - thêm vào JSON tạm deletedFiles
            $jsonPath = storage_path("app/file_bai_giang/deletedFiles_{$maNguoiDung}_{$maBaiGiang}.json");
            $data = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
            if (!in_array($filePath, $data)) {
                $data[] = $filePath;
                file_put_contents($jsonPath, json_encode($data));
                Log::info("Đã thêm file cần xóa vào JSON: $filePath");
            }
        } else {
            // FORM THÊM - xóa khỏi file JSON tạm
            $jsonPath = storage_path("app/file_bai_giang/{$maNguoiDung}.json");
            if (file_exists($jsonPath)) {
                $data = json_decode(file_get_contents($jsonPath), true);
                $data['tenFile'] = array_values(array_filter($data['tenFile'], fn($f) => $f !== $filePath));
                file_put_contents($jsonPath, json_encode($data));
                Log::info("Đã cập nhật JSON sau khi xóa elfinder: $filePath");
            }
        }

        return response()->json(['success' => true]);
    }

    public function themFileXoa(Request $request)
    {
        $maNguoiDung = Auth::id();
        $maBaiGiang = $request->input('maBaiGiang');
        $filePath = $request->input('path');
        $filePath = urldecode($filePath);

        $jsonPath = storage_path("app/file_bai_giang/deletedFiles_{$maNguoiDung}_{$maBaiGiang}.json");
        $data = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];

        if (!in_array($filePath, $data)) {
            $data[] = $filePath;
            file_put_contents($jsonPath, json_encode($data));
            Log::info("Đã thêm file cần xóa: $filePath");
        }

        return response()->json(['success' => true]);
    }
}
