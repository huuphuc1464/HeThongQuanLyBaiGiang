<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class ChuongController extends Controller
{
    public function danhSach(Request $request, $maBaiGiang)
    {
        // Kiểm tra xem bài giảng có tồn tại và thuộc về giảng viên hiện tại
        $existsBaiGiang = DB::table('bai_giang')
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->exists();

        if (!$existsBaiGiang) {
            return redirect()->back()->withErrors(['errorSystem' => 'Bài giảng không tồn tại hoặc bạn không có quyền truy cập']);
        }
        $query = DB::table('chuong')
            ->leftJoin('bai', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->where('chuong.MaGiangVien', Auth::id())
            ->where('chuong.MaBaiGiang', $maBaiGiang)
            ->select(
                'chuong.MaChuong',
                'chuong.TenChuong',
                'chuong.TrangThai as TrangThaiChuong',
                'bai.MaBai',
                'bai.TenBai',
                'bai.MoTa',
                'bai.TrangThai',
                'bai.created_at',
                'bai.updated_at'
            );

        // Tìm kiếm
        if ($request->filled('search')) {
            $keywords = preg_split('/\s+/', trim($request->search));
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $q->orWhereRaw('LOWER(bai.TenBai) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(bai.MoTa) LIKE ?', ["%$kw%"]);
                    // ->orWhereRaw('LOWER(REGEXP_REPLACE(bai.NoiDung, "<[^>]*>", "")) LIKE ?', ["%$kw%"]);
                }
            });
        }

        $data = $query
            ->orderBy('chuong.TenChuong')
            ->orderBy('bai.TenBai')
            ->orderBy('bai.created_at')
            ->get();

        $chuongList = $data->groupBy('MaChuong')->map(function ($dsBai) {
            $chuong = $dsBai->first();
            return [
                'TenChuong' => $chuong->TenChuong,
                'TrangThai' => $chuong->TrangThaiChuong,
                'Bai' => $dsBai->filter(fn($b) => $b->MaBai !== null)->groupBy('TenBai'),
            ];
        });

        $baiGiang = DB::table('bai_giang')
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->select('TenBaiGiang', 'MaBaiGiang', 'TrangThai')
            ->first();

        return view('giangvien.quanLyChuong.danhSachChuong', compact('chuongList', 'baiGiang'));
    }

    public function layThongTinChuong($maBaiGiang, $maChuong)
    {
        $chuong = DB::table('chuong')
            ->where('MaChuong', $maChuong)
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->select('MaChuong', 'TenChuong', 'MoTa', 'TrangThai')
            ->first();

        if (!$chuong) {
            return response()->json(['error' => 'Chương không tồn tại'], 404);
        }

        return response()->json($chuong);
    }

    public function themChuong(Request $request, $maBaiGiang)
    {
        $request->validate([
            'TenChuong' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}\p{P}][\p{L}\p{N}\p{P}\p{Zs}\t]*$/u'
            ],
            'MoTa' => 'nullable|string|max:255',
            'TrangThai' => 'required|in:0,1',
        ], [
            'TenChuong.required' => 'Tên chương là bắt buộc',
            'TenChuong.string' => 'Tên chương phải là chuỗi ký tự',
            'TenChuong.max' => 'Tên chương không được vượt quá 255 ký tự',
            'TenChuong.regex' => 'Tên chương chỉ được chứa chữ cái, số, khoảng trắng và các ký tự đặc biệt hợp lệ',
            'MoTa.string' => 'Mô tả phải là chuỗi ký tự',
            'MoTa.max' => 'Mô tả không được vượt quá 255 ký tự',
            'TrangThai.required' => 'Trạng thái là bắt buộc',
            'TrangThai.in' => 'Trạng thái không hợp lệ',
        ]);

        $existsBaiGiang = DB::table('bai_giang')
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->exists();

        if (!$existsBaiGiang) {
            return redirect()->back()->withErrors(['errorSystem' => 'Bài giảng không tồn tại hoặc bạn không có quyền truy cập']);
        }

        $existsChuong = DB::table('chuong')
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('TenChuong', $request->TenChuong)
            ->where('MaGiangVien', Auth::id())
            ->exists();

        if ($existsChuong) {
            return redirect()->back()
                ->withErrors(['TenChuong' => 'Chương này đã tồn tại trong bài giảng'])
                ->withInput()->with('isEditing', false)
                ->with('actionUrl', route('giangvien.bai-giang.chuong.them', ['maBaiGiang' => $maBaiGiang]));
        }

        DB::table('chuong')->insert([
            'MaBaiGiang' => $maBaiGiang,
            'MaGiangVien' => Auth::id(),
            'TenChuong' => $request->TenChuong,
            'MoTa' => $request->MoTa,
            'TrangThai' => $request->TrangThai,
            'created_at' => now('Asia/Ho_Chi_Minh'),
            'updated_at' => now('Asia/Ho_Chi_Minh'),
        ]);

        return redirect()->route('giangvien.bai-giang.chuong.danh-sach', ['maBaiGiang' => $maBaiGiang])
            ->with('success', 'Thêm chương thành công');
    }

    public function capNhatChuong(Request $request, $maBaiGiang, $maChuong)
    {
        $request->validate(
            [
                'TenChuong' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[\p{L}\p{N}\p{P}][\p{L}\p{N}\p{P}\p{Zs}\t]*$/u'
                ],
                'MoTa' => 'nullable|string|max:255',
                'TrangThai' => 'required|in:0,1',
            ],
            [
                'TenChuong.required' => 'Tên chương là bắt buộc',
                'TenChuong.string' => 'Tên chương phải là chuỗi ký tự',
                'TenChuong.max' => 'Tên chương không được vượt quá 255 ký tự',
                'TenChuong.regex' => 'Tên chương chỉ được chứa chữ cái, số, khoảng trắng và các ký tự đặc biệt hợp lệ',
                'MoTa.string' => 'Mô tả phải là chuỗi ký tự',
                'MoTa.max' => 'Mô tả không được vượt quá 255 ký tự',
                'TrangThai.required' => 'Trạng thái là bắt buộc',
                'TrangThai.in' => 'Trạng thái không hợp lệ',
            ]
        );

        $baiGiang = DB::table('bai_giang')
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->select('TrangThai')
            ->first();

        if (!$baiGiang) {
            return redirect()->back()->withErrors(['errorSystem' => 'Bài giảng không tồn tại hoặc bạn không có quyền truy cập']);
        }

        $chuong = DB::table('chuong')
            ->where('MaChuong', $maChuong)
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->first();

        if (!$chuong) {
            return redirect()->back()->withErrors(['errorSystem' => 'Chương không tồn tại hoặc bạn không có quyền truy cập']);
        }

        $existsChuong = DB::table('chuong')
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('TenChuong', $request->TenChuong)
            ->where('MaGiangVien', Auth::id())
            ->where('MaChuong', '!=', $maChuong)
            ->exists();

        if ($existsChuong) {
            return redirect()->back()
                ->withErrors(['TenChuong' => 'Chương này đã tồn tại trong bài giảng'])
                ->withInput()
                ->with('isEditing', true)
                ->with('actionUrl', route('giangvien.bai-giang.chuong.cap-nhat', ['maBaiGiang' => $maBaiGiang, 'maChuong' => $maChuong]));
        }

        $updateData = [
            'TenChuong' => $request->TenChuong,
            'MoTa' => $request->MoTa,
            'updated_at' => now('Asia/Ho_Chi_Minh'),
        ];

        if ($baiGiang->TrangThai == 1) {
            $updateData['TrangThai'] = $request->TrangThai;
        }

        DB::table('chuong')
            ->where('MaChuong', $maChuong)
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->update($updateData);

        if ($baiGiang->TrangThai == 1) {
            DB::table('bai')
                ->where('MaChuong', $maChuong)
                ->where('MaGiangVien', Auth::id())
                ->update([
                    'TrangThai' => $request->TrangThai,
                    'updated_at' => now('Asia/Ho_Chi_Minh'),
                ]);

            DB::table('file_bai_giang')
                ->join('bai', 'file_bai_giang.MaBai', '=', 'bai.MaBai')
                ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
                ->where('chuong.MaBaiGiang', $maBaiGiang)
                ->where('chuong.MaChuong', $maChuong)
                ->where('chuong.MaGiangVien', Auth::id())
                ->where('bai.MaGiangVien', Auth::id())
                ->update([
                    'file_bai_giang.TrangThai' => $request->TrangThai,
                    'file_bai_giang.updated_at' => now('Asia/Ho_Chi_Minh'),
                ]);
        }

        return redirect()->route('giangvien.bai-giang.chuong.danh-sach', ['maBaiGiang' => $maBaiGiang])
            ->with('success', 'Cập nhật chương thành công');
    }

    public function doiTrangThaiChuong(Request $request, $maBaiGiang, $maChuong)
    {
        $chuong = DB::table('chuong')
            ->join('bai_giang', 'bai_giang.MaBaiGiang', '=', 'chuong.MaBaiGiang')
            ->where('chuong.MaChuong', $maChuong)
            ->where('chuong.MaBaiGiang', $maBaiGiang)
            ->where('bai_giang.MaGiangVien', Auth::id())
            ->where('chuong.MaGiangVien', Auth::id())
            ->select('chuong.TrangThai as TrangThaiChuong', 'bai_giang.TrangThai as TrangThaiBaiGiang')
            ->first();

        if (!$chuong) {
            return back()->with('errorSystem', 'Chương không tồn tại hoặc bạn không có quyền.');
        }

        if ($chuong->TrangThaiBaiGiang == 0) {
            return back()->with('errorSystem', 'Không thể cập nhật trạng thái chương vì bài giảng đang bị ẩn.');
        }

        $trangThai = $request->input('trangThai') == 1 ? 1 : 0;

        DB::table('chuong')
            ->where('MaChuong', $maChuong)
            ->where('MaBaiGiang', $maBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->where('TrangThai', '!=', $trangThai)
            ->update([
                'TrangThai' => $trangThai,
                'updated_at' => now('Asia/Ho_Chi_Minh')
            ]);

        DB::table('bai')
            ->where('MaChuong', $maChuong)
            ->where('MaGiangVien', Auth::id())
            ->where('TrangThai', '!=', $trangThai)
            ->update([
                'TrangThai' => $trangThai,
                'updated_at' => now('Asia/Ho_Chi_Minh')
            ]);

        DB::table('file_bai_giang')
            ->join('bai', 'file_bai_giang.MaBai', '=', 'bai.MaBai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->where('chuong.MaBaiGiang', $maBaiGiang)
            ->where('chuong.MaChuong', $maChuong)
            ->where('chuong.MaGiangVien', Auth::id())
            ->where('bai.MaGiangVien', Auth::id())
            ->where('file_bai_giang.TrangThai', '!=', $trangThai)
            ->update([
                'file_bai_giang.TrangThai' => $trangThai,
                'file_bai_giang.updated_at' => now('Asia/Ho_Chi_Minh')
            ]);

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}