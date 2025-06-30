<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\BaiGiang;
use App\Models\Khoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BaiGiangController extends Controller
{
    public function danhSachBaiGiang(Request $request)
    {
        $query = DB::table('bai_giang')
            ->join('khoa', 'bai_giang.MaKhoa', '=', 'khoa.MaKhoa')
            ->select('bai_giang.*', 'khoa.TenKhoa')
            ->where('bai_giang.MaGiangVien', Auth::id())
            ->orderBy('bai_giang.created_at', 'desc');

        if ($request->filled('search')) {
            $keywords = preg_split('/\s+/', trim($request->search));

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $q->orWhereRaw('LOWER(bai_giang.TenBaiGiang) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(bai_giang.MoTa) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(khoa.TenKhoa) LIKE ?', ["%$kw%"]);
                }
            });
        }
        $danhSachBaiGiang = $query->orderBy('bai_giang.created_at', 'desc')->paginate(10)->withQueryString();
        $danhSachKhoa = Khoa::where('TrangThai', 1)->get();
        return view('giangvien.quanLyBaiGiang.danhSachBaiGiang', compact('danhSachBaiGiang', 'danhSachKhoa'));
    }

    public function themBaiGiang(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TenBaiGiang' => 'required|string|max:255',
            'MaKhoa' => 'required|exists:khoa,MaKhoa',
            'MoTa' => 'nullable|string|max:255',
            'AnhBaiGiang' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'TrangThai' => 'required|in:0,1'
        ], [
            'TenBaiGiang.required' => 'Vui lòng nhập tên bài giảng.',
            'TenBaiGiang.max' => 'Tên bài giảng không được vượt quá 255 ký tự.',
            'MaKhoa.required' => 'Vui lòng chọn khoa.',
            'MaKhoa.exists' => 'Khoa được chọn không hợp lệ.',
            'MoTa.string' => 'Mô tả phải là chuỗi.',
            'AnhBaiGiang.image' => 'Tệp tải lên phải là hình ảnh.',
            'AnhBaiGiang.mimes' => 'Ảnh chỉ chấp nhận định dạng JPG, JPEG hoặc PNG.',
            'AnhBaiGiang.max' => 'Kích thước ảnh tối đa là 2MB.',
            'TrangThai.required' => 'Vui lòng chọn trạng thái.',
            'TrangThai.in' => 'Trạng thái không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('form_action', 'them');
        }

        $validated = $validator->validated();

        $isTrungTen = BaiGiang::where('TenBaiGiang', $validated['TenBaiGiang'])
            ->where('MaGiangVien', Auth::id())
            ->where('MaKhoa', $validated['MaKhoa'])
            ->exists();

        if ($isTrungTen) {
            return back()
                ->withErrors(['TenBaiGiang' => 'Tên bài giảng đã tồn tại cho giảng viên và khoa này.'])
                ->withInput()
                ->with('form_action', 'them');
        }

        $baiGiang = BaiGiang::create([
            'MaGiangVien' => Auth::id(),
            'TenBaiGiang' => $validated['TenBaiGiang'],
            'MaKhoa' => $validated['MaKhoa'],
            'MoTa' => $validated['MoTa'] ?? null,
            'AnhBaiGiang' => null,
            'TrangThai' => $validated['TrangThai'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->hasFile('AnhBaiGiang')) {
            $file = $request->file('AnhBaiGiang');
            $extension = $file->getClientOriginalExtension();
            $fileName = $baiGiang->MaBaiGiang . '_' . time() . '.' . $extension;
            $filePath = 'img/baigiang/' . $fileName;
            if (!file_exists(public_path('img/baigiang'))) {
                mkdir(public_path('img/baigiang'), 0755, true);
            }
            $file->move(public_path('img/baigiang'), $fileName);
            $baiGiang->AnhBaiGiang = $filePath;
            $baiGiang->save();
        }

        return back()->with('success', 'Thêm bài giảng thành công');
    }

    public function capNhatBaiGiang(Request $request, $id)
    {
        $baiGiang = BaiGiang::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'TenBaiGiang' => 'required|string|max:255',
            'MaKhoa' => 'required|exists:khoa,MaKhoa',
            'MoTa' => 'nullable|string|max:255',
            'TrangThai' => 'required|in:0,1',
            'AnhBaiGiang' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'TenBaiGiang.required' => 'Vui lòng nhập tên bài giảng.',
            'TenBaiGiang.max' => 'Tên bài giảng không được vượt quá 255 ký tự.',
            'MaKhoa.required' => 'Vui lòng chọn khoa.',
            'MaKhoa.exists' => 'Khoa được chọn không hợp lệ.',
            'MoTa.string' => 'Mô tả phải là chuỗi.',
            'AnhBaiGiang.image' => 'Tệp tải lên phải là hình ảnh.',
            'AnhBaiGiang.mimes' => 'Ảnh chỉ chấp nhận định dạng JPG, JPEG hoặc PNG.',
            'AnhBaiGiang.max' => 'Kích thước ảnh tối đa là 2MB.',
            'TrangThai.required' => 'Vui lòng chọn trạng thái.',
            'TrangThai.in' => 'Trạng thái không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('form_action', 'sua')
                ->with('MaBaiGiang', $id);
        }

        $validated = $validator->validated();

        $isTrung = BaiGiang::where('TenBaiGiang', $validated['TenBaiGiang'])
            ->where('MaGiangVien', Auth::id())
            ->where('MaBaiGiang', '!=', $baiGiang->MaBaiGiang)
            ->where('MaKhoa', $validated['MaKhoa'])
            ->exists();

        if ($isTrung) {
            return back()
                ->withErrors(['TenBaiGiang' => 'Tên bài giảng đã tồn tại cho giảng viên và khoa này.'])
                ->withInput()
                ->with('form_action', 'sua')
                ->with('MaBaiGiang', $id);
        }

        if ($request->hasFile('AnhBaiGiang')) {
            $file = $request->file('AnhBaiGiang');
            $extension = $file->getClientOriginalExtension();
            $fileName = $baiGiang->MaBaiGiang . '_' . time() . '.' . $extension;
            $filePath = 'img/baigiang/' . $fileName;

            if ($baiGiang->AnhBaiGiang && file_exists(public_path($baiGiang->AnhBaiGiang))) {
                unlink(public_path($baiGiang->AnhBaiGiang));
            }
            $file->move(public_path('img/baigiang'), $fileName);
            $baiGiang->AnhBaiGiang = $filePath;
        }

        $baiGiang->TenBaiGiang = $validated['TenBaiGiang'];
        $baiGiang->MaKhoa = $validated['MaKhoa'];
        $baiGiang->MoTa = $validated['MoTa'] ?? null;
        $baiGiang->TrangThai = $validated['TrangThai'];
        $baiGiang->updated_at = now();
        $baiGiang->save();

        DB::table('chuong')
            ->where('MaBaiGiang', $baiGiang->MaBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->update(['TrangThai' => $baiGiang->TrangThai, 'updated_at' => now('Asia/Ho_Chi_Minh')]);

        DB::table('bai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->where('chuong.MaBaiGiang', $baiGiang->MaBaiGiang)
            ->where('chuong.MaGiangVien', Auth::id())
            ->update(['bai.TrangThai' => $baiGiang->TrangThai, 'bai.updated_at' => now('Asia/Ho_Chi_Minh')]);

        DB::table('file_bai_giang')
            ->join('bai', 'file_bai_giang.MaBai', '=', 'bai.MaBai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->where('chuong.MaGiangVien', Auth::id())
            ->where('chuong.MaBaiGiang', $baiGiang->MaBaiGiang)
            ->update(['file_bai_giang.TrangThai' => $baiGiang->TrangThai, 'file_bai_giang.updated_at' => now('Asia/Ho_Chi_Minh')]);

        return back()->with('success', 'Cập nhật bài giảng thành công');
    }

    public function xoaBaiGiang($id)
    {
        $baiGiang = BaiGiang::findOrFail($id);
        $baiGiang->TrangThai = 0;
        $baiGiang->save();

        DB::table('chuong')
            ->where('MaBaiGiang', $baiGiang->MaBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->update(['TrangThai' => 0, 'updated_at' => now('Asia/Ho_Chi_Minh')]);

        DB::table('bai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->where('chuong.MaBaiGiang', $baiGiang->MaBaiGiang)
            ->where('chuong.MaGiangVien', Auth::id())
            ->update(['bai.TrangThai' => 0, 'bai.updated_at' => now('Asia/Ho_Chi_Minh')]);

        DB::table('file_bai_giang')
            ->join('bai', 'file_bai_giang.MaBai', '=', 'bai.MaBai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->where('chuong.MaBaiGiang', $baiGiang->MaBaiGiang)
            ->where('chuong.MaGiangVien', Auth::id())
            ->update(['file_bai_giang.TrangThai' => 0, 'file_bai_giang.updated_at' => now('Asia/Ho_Chi_Minh')]);
        return back()->with('success', 'Đã xóa bài giảng thành công');
    }

    public function khoiPhucBaiGiang($id)
    {
        $baiGiang = BaiGiang::findOrFail($id);
        $baiGiang->TrangThai = 1;
        $baiGiang->save();
        DB::table('chuong')
            ->where('MaBaiGiang', $baiGiang->MaBaiGiang)
            ->where('MaGiangVien', Auth::id())
            ->update(['TrangThai' => 1, 'updated_at' => now('Asia/Ho_Chi_Minh')]);

        DB::table('bai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->where('chuong.MaBaiGiang', $baiGiang->MaBaiGiang)
            ->where('chuong.MaGiangVien', Auth::id())
            ->update(['bai.TrangThai' => 1, 'bai.updated_at' => now('Asia/Ho_Chi_Minh')]);

        DB::table('file_bai_giang')
            ->join('bai', 'file_bai_giang.MaBai', '=', 'bai.MaBai')
            ->join('chuong', 'bai.MaChuong', '=', 'chuong.MaChuong')
            ->where('chuong.MaBaiGiang', $baiGiang->MaBaiGiang)
            ->where('chuong.MaGiangVien', Auth::id())
            ->update(['file_bai_giang.TrangThai' => 1, 'file_bai_giang.updated_at' => now('Asia/Ho_Chi_Minh')]);

        return back()->with('success', 'Đã khôi phục bài giảng thành công');
    }
}
