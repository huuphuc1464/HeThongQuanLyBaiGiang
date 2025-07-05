<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\BaiGiang;
use App\Models\FileBaiGiang;
use App\Models\HocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BaiController extends Controller
{
    public function doiTrangThaiBai(Request $request, $maBaiGiang, $maChuong, $maBai)
    {
        $bai = DB::table('bai')
            ->join('chuong', 'chuong.MaChuong', '=', 'bai.MaChuong')
            ->join('bai_giang', 'bai_giang.MaBaiGiang', '=', 'chuong.MaBaiGiang')
            ->where('bai.MaBai', $maBai)
            ->where('chuong.MaChuong', $maChuong)
            ->where('bai_giang.MaBaiGiang', $maBaiGiang)
            ->where('chuong.MaGiangVien', Auth::id())
            ->select(
                'bai.TrangThai as TrangThaiBai',
                'chuong.TrangThai as TrangThaiChuong',
                'bai_giang.TrangThai as TrangThaiBaiGiang'
            )
            ->first();

        if (!$bai) {
            return redirect()->back()->with('errorSystem', 'Bài không tồn tại hoặc bạn không có quyền.');
        }

        if ($bai->TrangThaiChuong == 0) {
            return redirect()->back()->with('errorSystem', 'Không thể cập nhật vì chương đang bị ẩn.');
        }

        if ($bai->TrangThaiBaiGiang == 0) {
            return redirect()->back()->with('errorSystem', 'Không thể cập nhật vì bài giảng đang bị ẩn.');
        }

        $newStatus = $request->input('trangThai') == 1 ? 1 : 0;

        DB::table('bai')
            ->where('MaBai', $maBai)
            ->update([
                'TrangThai' => $newStatus,
                'updated_at' => now()
            ]);

        DB::table('file_bai_giang')
            ->join('bai', 'file_bai_giang.MaBai', '=', 'bai.MaBai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->where('chuong.MaGiangVien', Auth::id())
            ->where('chuong.MaBaiGiang', $maBaiGiang)
            ->where('chuong.MaChuong', $maChuong)
            ->where('bai.MaBai', $maBai)
            ->update([
                'file_bai_giang.TrangThai' => $newStatus,
                'file_bai_giang.updated_at' => now('Asia/Ho_Chi_Minh')
            ]);
        return redirect()->back()->with('success', 'Cập nhật trạng thái bài thành công.');
    }

    public function chiTietBai($maBaiGiang, $maChuong, $maBai)
    {
        $baiHoc = DB::table('bai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->join('bai_giang', 'bai_giang.MaBaiGiang', '=', 'chuong.MaBaiGiang')
            ->where('bai.MaBai', $maBai)
            ->where('chuong.MaChuong', $maChuong)
            ->where('bai_giang.MaBaiGiang', $maBaiGiang)
            ->where('chuong.MaGiangVien', Auth::id())
            ->select(
                'bai.*',
                'chuong.TenChuong',
                'bai_giang.TenBaiGiang',
                'bai_giang.MaBaiGiang'
            )
            ->first();

        if (!$baiHoc) {
            return redirect()->back()->with('errorSystem', 'Bài học không tồn tại hoặc bạn không có quyền truy cập.');
        }

        $files = DB::table('file_bai_giang')
            ->where('MaBai', $maBai)
            ->where('TrangThai', 1)
            ->get();

        return view('giangvien.quanLyBai.chiTietBai', compact('baiHoc', 'files'));
    }

    public function hienFormThemBai($maBaiGiang, $maChuong)
    {
        $baiHoc = DB::table('bai_giang')
            ->join('chuong', 'bai_giang.MaBaiGiang', '=', 'chuong.MaBaiGiang')
            ->where('bai_giang.MaBaiGiang', $maBaiGiang)
            ->where('chuong.MaChuong', $maChuong)
            ->where('bai_giang.MaGiangVien', Auth::id())
            ->select(
                'bai_giang.MaBaiGiang',
                'bai_giang.TenBaiGiang',
                'chuong.MaChuong',
                'chuong.TenChuong'
            )
            ->first();
        if (!$baiHoc) {
            return redirect()->back()->with('errorSystem', 'Không tìm thấy bài giảng hoặc bạn không có quyền truy cập.');
        }
        return view('giangvien.quanLyBai.themBai', compact('baiHoc'));
    }

    public function huyBoBai(Request $request)
    {
        $maBaiGiang = $request->MaBaiGiang ?? null;
        $maBai = $request->MaBai ?? null;
        $maNguoiDung = Auth::id();

        if ($maBai) {
            // Form cập nhật
            $folderPath = public_path("BaiGiang/BaiGiang_{$maBaiGiang}/Bai_{$maBai}");
            $duongDanDB = FileBaiGiang::where('MaBai', $maBai)->pluck('DuongDan')->map(function ($path) {
                return preg_replace('#/+#', '/', str_replace('\\', '/', $path));
            })->toArray();
        } else {
            // Form thêm
            $folderPath = public_path("BaiGiang/BaiGiang_{$maBaiGiang}/temp_{$maNguoiDung}_{$maBaiGiang}");
            $duongDanDB = [];
        }

        Log::info($duongDanDB);

        if (File::exists($folderPath)) {
            foreach (File::files($folderPath) as $file) {
                $relativePath = str_replace(public_path(), '', $file->getPathname());
                $relativePath = preg_replace('#/+#', '/', str_replace('\\', '/', $relativePath));
                $relativePath = ltrim($relativePath, '/');

                if (!in_array($relativePath, $duongDanDB)) {
                    unlink($file->getPathname());
                    Log::info("Đã xóa file chưa lưu DB: $relativePath");
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function themBai(Request $request, $maBaiGiang, $maChuong)
    {
        $request->validate([
            'MaChuong' => 'required|exists:chuong,MaChuong',
            'TenBai' => 'required|string|max:255',
            'NoiDung' => 'required|string',
            'MoTa' => 'nullable|string|max:255',
            'TrangThai' => 'required|in:0,1',
        ], [
            'MaChuong.required' => 'Mã chương không được để trống.',
            'MaChuong.exists' => 'Chương không tồn tại.',
            'TenBai.required' => 'Tên bài không được để trống.',
            'TenBai.string' => 'Tên bài phải là chuỗi.',
            'TenBai.max' => 'Tên bài không được vượt quá 255 ký tự.',
            'NoiDung.required' => 'Nội dung bài không được để trống.',
            'NoiDung.string' => 'Nội dung phải là chuỗi.',
            'MoTa.string' => 'Mô tả phải là chuỗi.',
            'MoTa.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'TrangThai.required' => 'Trạng thái bài không được để trống.',
            'TrangThai.in' => 'Trạng thái bài chỉ chấp nhận giá trị 0 và 1.'
        ]);

        $existsBai = DB::table('bai')
            ->where('MaChuong', $maChuong)
            ->where('TenBai', $request->TenBai)
            ->where('MaGiangVien', Auth::id())
            ->exists();
        if ($existsBai) {
            return redirect()->back()
                ->withErrors(['TenBai' => 'Tên bài này đã tồn tại trong chương.'])
                ->withInput();
        }

        $maNguoiDung = Auth::id();
        $oldPath = "BaiGiang/BaiGiang_{$maBaiGiang}/temp_{$maNguoiDung}_{$maBaiGiang}";
        $oldFolder = public_path($oldPath);

        if (mb_strlen($request->NoiDung, '8bit') > 4294967295) {
            return back()->withErrors([
                'NoiDung' => 'Nội dung vượt quá dung lượng tối đa cho phép (4GB). Vui lòng rút gọn.'
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            $bai = DB::table('bai')->insertGetId([
                'MaChuong' => $maChuong,
                'MaGiangVien' => $maNguoiDung,
                'TenBai' => $request->TenBai,
                'NoiDung' => $request->NoiDung,
                'MoTa' => $request->MoTa,
                'TrangThai' => $request->TrangThai,
                'created_at' => now('Asia/Ho_Chi_Minh'),
                'updated_at' => now('Asia/Ho_Chi_Minh'),
            ]);

            // Đổi tên thư mục chứa file
            $newPath = "BaiGiang/BaiGiang_{$maBaiGiang}/Bai_{$bai}";
            $newFolder = public_path($newPath);

            if (file_exists($oldFolder)) {
                rename($oldFolder, $newFolder);
            }

            // Cập nhật đường dẫn ảnh trong nội dung bài giảng
            $noiDungCapNhat = str_replace($oldPath, $newPath, $request->NoiDung);
            DB::table('bai')
                ->where('MaBai', $bai)
                ->update(['NoiDung' => $noiDungCapNhat]);

            // Lưu file đính kèm (nếu có)
            if (File::exists($newFolder)) {
                $files = File::files($newFolder);
                foreach ($files as $file) {
                    $relativePath = str_replace(public_path(), '', $file->getPathname());
                    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

                    DB::table('file_bai_giang')->insert([
                        'MaBai' => $bai,
                        'DuongDan' => $relativePath,
                        'LoaiFile' => $file->getExtension(),
                        'TrangThai' => 1,
                        'created_at' => now('Asia/Ho_Chi_Minh'),
                        'updated_at' => now('Asia/Ho_Chi_Minh'),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('giangvien.bai-giang.chuong.danh-sach', [$maBaiGiang])
                ->with('success', 'Thêm bài thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($bai)) {
                DB::table('bai')->where('MaBai', $bai)->delete();
            }

            if (isset($newFolder) && File::exists($newFolder)) {
                File::deleteDirectory($newFolder);
            }

            Log::error('Lỗi khi lưu bài', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Lỗi khi lưu bài: ' . $e->getMessage());
        }
    }

    public function hienFormSuaBai(Request $request, $maBaiGiang, $maChuong, $maBai)
    {
        $baiHoc = DB::table('bai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->join('bai_giang', 'bai_giang.MaBaiGiang', '=', 'chuong.MaBaiGiang')
            ->where('bai.MaBai', $maBai)
            ->where('bai_giang.MaBaiGiang', $maBaiGiang)
            ->where('chuong.MaChuong', $maChuong)
            ->where('bai.MaGiangVien', Auth::id())
            ->select(
                'bai_giang.MaBaiGiang',
                'bai_giang.TenBaiGiang',
                'chuong.TenChuong',
                'bai.*'
            )
            ->first();
        if (!$baiHoc) {
            return redirect()->back()->with('errorSystem', 'Không tìm thấy bài học hoặc bạn không có quyền truy cập.');
        }
        return view('giangvien.quanLyBai.suaBai', compact('baiHoc'));
    }

    public function capNhatBai(Request $request, $maBaiGiang, $maChuong, $maBai)
    {
        $request->validate([
            'MaChuong' => 'required|exists:chuong,MaChuong',
            'TenBai' => 'required|string|max:255',
            'NoiDung' => 'required|string',
            'MoTa' => 'nullable|string|max:255',
            'TrangThai' => 'required|in:0,1',
        ], [
            'MaChuong.required' => 'Mã chương không được để trống.',
            'MaChuong.exists' => 'Chương không tồn tại.',
            'TenBai.required' => 'Tên bài không được để trống.',
            'TenBai.string' => 'Tên bài phải là chuỗi.',
            'TenBai.max' => 'Tên bài không được vượt quá 255 ký tự.',
            'NoiDung.required' => 'Nội dung bài giảng không được để trống.',
            'NoiDung.string' => 'Nội dung phải là chuỗi.',
            'MoTa.string' => 'Mô tả phải là chuỗi.',
            'MoTa.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'TrangThai.required' => 'Trạng thái bài giảng không được để trống.',
            'TrangThai.in' => 'Trạng thái bài giảng chỉ chấp nhận giá trị 0 và 1.',
        ]);

        $maNguoiDung = Auth::id();
        $newPath = "BaiGiang/BaiGiang_{$maBaiGiang}/Bai_{$maBai}";
        $folderPath = public_path($newPath);

        $existsBai = DB::table('bai')
            ->where('MaChuong', $maChuong)
            ->where('TenBai', $request->TenBai)
            ->where('MaGiangVien', $maNguoiDung)
            ->where('MaBai', '!=', $maBai)
            ->exists();
        if ($existsBai) {
            return redirect()->back()
                ->withErrors(['TenBai' => 'Tên bài này đã tồn tại trong chương.'])
                ->withInput();
        }

        if (mb_strlen($request->NoiDung, '8bit') > 4294967295) {
            return back()->withErrors([
                'NoiDung' => 'Nội dung vượt quá dung lượng tối đa cho phép (4GB). Vui lòng rút gọn.'
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            // Cập nhật thông tin bài
            DB::table('bai')
                ->where('MaBai', $maBai)
                ->where('MaGiangVien', $maNguoiDung)
                ->update([
                    'TenBai' => $request->TenBai,
                    'NoiDung' => $request->NoiDung,
                    'MoTa' => $request->MoTa,
                    'TrangThai' => $request->TrangThai,
                    'updated_at' => now('Asia/Ho_Chi_Minh'),
                ]);

            // Xử lý file
            if (File::exists($folderPath)) {
                $filePathsOnDisk = collect(File::files($folderPath))->map(function ($file) {
                    return ltrim(str_replace(public_path(), '', $file->getPathname()), '/');
                })->toArray();

                $filePathsInDb = DB::table('file_bai_giang')->where('MaBai', $maBai)->pluck('DuongDan')->toArray();

                // Xoá file DB không còn trong ổ đĩa
                foreach ($filePathsInDb as $dbPath) {
                    if (!in_array($dbPath, $filePathsOnDisk)) {
                        DB::table('file_bai_giang')->where('MaBai', $maBai)->where('DuongDan', $dbPath)->delete();
                        Log::info("Đã xóa bản ghi file không tồn tại trên ổ đĩa: $dbPath");
                    }
                }

                // Thêm file mới từ ổ đĩa vào DB nếu chưa có
                foreach ($filePathsOnDisk as $diskPath) {
                    if (!in_array($diskPath, $filePathsInDb)) {
                        DB::table('file_bai_giang')->insert([
                            'MaBai' => $maBai,
                            'DuongDan' => $diskPath,
                            'LoaiFile' => pathinfo($diskPath, PATHINFO_EXTENSION),
                            'TrangThai' => 1,
                            'created_at' => now('Asia/Ho_Chi_Minh'),
                            'updated_at' => now('Asia/Ho_Chi_Minh'),
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('giangvien.bai-giang.chuong.danh-sach', [$maBaiGiang])
                ->with('success', 'Cập nhật bài thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật bài', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Lỗi khi cập nhật bài: ' . $e->getMessage());
        }
    }

    public function uploadDocxImage(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No image provided'], 400);
        }

        $file = $request->file('image');
        $maBai = $request->input('maBai');
        $maBaiGiang = $request->input('maBaiGiang');
        $maNguoiDung = Auth::id();

        if ($maBai) {
            // Form cập nhật
            $folder = "BaiGiang/BaiGiang_{$maBaiGiang}/Bai_{$maBai}";
        } else {
            // Form thêm mới
            $folder = "BaiGiang/BaiGiang_{$maBaiGiang}/temp_{$maNguoiDung}_{$maBaiGiang}";
        }

        // Tạo tên file
        $fileName = uniqid('img_') . '.' . $file->getClientOriginalExtension();
        $filePath = public_path($folder);
        if (!file_exists($filePath)) {
            mkdir($filePath, 0755, true);
        }

        // Lưu file
        $file->move($filePath, $fileName);

        return response()->json([
            'url' => asset("{$folder}/{$fileName}")
        ]);
    }
}