<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\BaiGiang;
use App\Models\LopHocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Services\EmailService;

class LopHocPhanController extends Controller
{
    public function danhSach(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $trangThai = $request->input('trang_thai', 1); // Mặc định là 1 (đang hoạt động)
        $query = LopHocPhan::with(['baiGiang'])
            ->where('MaNguoiTao', Auth::id());
        // Lọc theo trạng thái
        if ($trangThai == 1 || $trangThai == 3) {
            $query->where('TrangThai', $trangThai);
        }
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

    public function luuTru($id, EmailService $emailService)
    {
        $lopHocPhan = LopHocPhan::with(['danhSachLop.sinhVien.nguoiDung', 'baiGiang'])
            ->where('MaLopHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();

        if ($lopHocPhan->TrangThai == 3) {
            return redirect()->back()->with('errorSystem', 'Lớp học phần đã ở trạng thái lưu trữ!');
        }

        $lopHocPhan->TrangThai = 3;
        $lopHocPhan->save();

        $danhSachSinhVien = $lopHocPhan->danhSachLop->pluck('sinhVien')->filter();
        $tenLop = $lopHocPhan->TenLopHocPhan;
        $tenBaiGiang = $lopHocPhan->baiGiang->TenBaiGiang ?? '';
        $giangVien = Auth::user()->name ?? 'Giảng viên';
        foreach ($danhSachSinhVien as $sinhVien) {
            if (empty($sinhVien->nguoiDung->email)) continue;
            try {
                $emailService->sendEmail(
                    $sinhVien->nguoiDung->email,
                    'Lớp học phần đã được lưu trữ',
                    "Giảng viên $giangVien đã lưu trữ lớp học phần: $tenLop (Bài giảng: $tenBaiGiang)."
                );
            } catch (\Exception $e) {
                // Bỏ qua lỗi gửi mail từng sinh viên
                dd($e);
            }
        }
        return redirect()->back()->with('success', 'Lưu trữ lớp học phần thành công!');
    }

    public function boLuuTru($id, EmailService $emailService)
    {
        $lopHocPhan = LopHocPhan::with(['danhSachLop.sinhVien.nguoiDung', 'baiGiang'])
            ->where('MaLopHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();

        if ($lopHocPhan->TrangThai != 3) {
            return redirect()->back()->with('errorSystem', 'Lớp học phần này không ở trạng thái lưu trữ!');
        }

        $lopHocPhan->TrangThai = 1;
        $lopHocPhan->save();

        $danhSachSinhVien = $lopHocPhan->danhSachLop->pluck('sinhVien')->filter();
        $tenLop = $lopHocPhan->TenLopHocPhan;
        $tenBaiGiang = $lopHocPhan->baiGiang->TenBaiGiang ?? '';
        $giangVien = Auth::user()->name ?? 'Giảng viên';
        foreach ($danhSachSinhVien as $sinhVien) {
            if ($sinhVien->nguoiDung->email)  continue;
            try {
                $emailService->sendEmail(
                    $sinhVien->nguoiDung->email,
                    'Lớp học phần đã được bỏ lưu trữ',
                    "Giảng viên $giangVien đã bỏ lưu trữ lớp học phần: $tenLop (Bài giảng: $tenBaiGiang). Bạn có thể truy cập lại lớp học phần này."
                );
            } catch (\Exception $e) {
                // Bỏ qua lỗi gửi mail từng sinh viên
            }
        }
        return redirect()->back()->with('success', 'Đã bỏ lưu trữ lớp học phần thành công!');
    }
}
