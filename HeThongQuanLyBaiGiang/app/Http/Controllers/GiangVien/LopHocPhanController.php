<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\Bai;
use App\Models\BaiGiang;
use App\Models\LopHocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LopHocPhanController extends Controller
{
    public function danhSach(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = LopHocPhan::with(['baiGiang'])
            ->where('MaNguoiTao', Auth::id());
        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('TenLopHocPhan', 'like', '%' . $request->search . '%')
                    ->orWhere('MoTa', 'like', '%' . $request->search . '%');
            });
        }
        $lopHocPhans = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $baiGiang = BaiGiang::where('MaGiangVien', Auth::id())->get();
        return view('giangvien.quanLyLopHocPhan.quanLyLopHocPhan', compact('lopHocPhans', 'baiGiang'));
    }

    public function themMoi(Request $request)
    {
        $request->validate([
            'TenLopHocPhan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lop_hoc_phan')->where(function ($query) {
                    return $query->where('MaNguoiTao', Auth::id());
                })
            ],
            'MoTa' => 'nullable|string|max:255',
            'MaBaiGiang' => 'required|exists:bai_giang,MaBaiGiang',
        ], [
            'MaBaiGiang.exists' => 'Bài giảng không tồn tại.',
            'MaBaiGiang.required' => 'Mã bài giảng là bắt buộc.',
            'TenLopHocPhan.required' => 'Tên lớp học phần là bắt buộc.',
            'TenLopHocPhan.unique' => 'Tên lớp học phần đã tồn tại.',
            'TenLopHocPhan.string' => 'Tên lớp học phần phải là chuỗi.',
            'TenLopHocPhan.max' => 'Tên lớp học phần không được vượt quá 255 ký tự.',
            'MoTa.string' => 'Mô tả phải là chuỗi.',
            'MoTa.max' => 'Mô tả không được vượt quá 255 ký tự.',
        ]);

        LopHocPhan::create([
            'TenLopHocPhan' => $request->TenLopHocPhan,
            'MoTa' => $request->MoTa,
            'MaBaiGiang' => $request->MaBaiGiang,
            'MaNguoiTao' => Auth::id(),
            'TrangThai' => 1,
        ]);

        return redirect()->back()->with('success', 'Thêm lớp học phần thành công!');
    }

    public function chiTiet($id)
    {
        $lopHocPhan = LopHocPhan::with(['baiGiang.chuong.bai'])
            ->where('MaLopHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();
        return response()->json($lopHocPhan);
    }

    public function chinhSua($id)
    {
        $lopHocPhan = LopHocPhan::with(['baiGiang.chuong.bai'])
            ->where('MaLopHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();
        return response()->json($lopHocPhan);
    }

    public function capNhat(Request $request, $id)
    {
        $lopHocPhan = LopHocPhan::where('MaLopHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();
        $request->validate([
            'TenLopHocPhan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lop_hoc_phan')->where(function ($query) {
                    return $query->where('MaNguoiTao', Auth::id());
                })->ignore($lopHocPhan->MaLopHocPhan, 'MaLopHocPhan')
            ],
            'MoTa' => 'nullable|string|max:255',
            // 'MaBaiGiang' => 'required|exists:bai_giang,MaBaiGiang',
        ], [
            'TenLopHocPhan.unique' => 'Tên lớp học phần đã tồn tại.'
        ]);
        $lopHocPhan->update([
            'TenLopHocPhan' => $request->TenLopHocPhan,
            'MoTa' => $request->MoTa,
            'MaBaiGiang' => $request->MaBaiGiang,
        ]);
        return redirect()->back()->with('success', 'Cập nhật lớp học phần thành công!');
    }

    public function xoa($id)
    {
        $lopHocPhan = LopHocPhan::withCount(['danhSachLop', 'baiKiemTra', 'suKienZoom'])
            ->where('MaLopHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();

        if ($lopHocPhan->danh_sach_lop_count > 0 || $lopHocPhan->bai_kiem_tra_count > 0 || $lopHocPhan->su_kien_zoom_count > 0) {
            return redirect()->back()->with('errorSystem', 'Không thể xóa lớp học phần này vì đã có dữ liệu liên quan (sinh viên, bài kiểm tra, zoom...).');
        }

        $lopHocPhan->delete();
        return redirect()->back()->with('success', 'Xóa lớp học phần thành công!');
    }
}