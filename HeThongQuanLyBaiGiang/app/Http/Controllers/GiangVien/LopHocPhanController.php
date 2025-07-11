<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\BaiGiang;
use App\Models\LopHocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Services\EmailService;
use App\Jobs\GuiEmailBccJob;
use Illuminate\Support\Facades\Bus;

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
        $giangVien = Auth::user()->HoTen ?? 'Giảng viên';
        $bccList = [];
        foreach ($danhSachSinhVien as $sinhVien) {
            if ($sinhVien && $sinhVien->nguoiDung && !empty($sinhVien->nguoiDung->email)) {
                $bccList[] = $sinhVien->nguoiDung->email;
            }
        }
        $bccList = array_unique($bccList);
        if (!empty($bccList)) {
            $subject = "Giảng viên đã [Lưu trữ lớp học phần] $tenLop";
            $body = "<div style='font-family: Arial, sans-serif; color: #222;'>"
                . "<h2 style='color: #0d6efd;'>Thông báo lưu trữ lớp học phần</h2>"
                . "<p>Xin chào sinh viên,</p>"
                . "<p><b>Giảng viên:</b> <span style='color:#198754;'>$giangVien</span> đã <b style='color:#dc3545;'>lưu trữ</b> lớp học phần <b style='color:#0d6efd;'>$tenLop</b> (Bài giảng: <b>$tenBaiGiang</b>).</p>"
                . "<p style='margin: 18px 0; background: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 4px solid #0d6efd;'>"
                . "Lớp học phần này sẽ được chuyển vào mục <b style='color:#0d6efd;'>Lớp học phần đã lưu trữ</b> trên hệ thống. Bạn có thể <b>truy cập</b> vào lớp học phần này bằng cách mở mục lớp học phần đã lưu trữ.</p>"
                . "<p>Nếu có thắc mắc, vui lòng liên hệ giảng viên.</p>"
                . "<hr style='margin:24px 0 12px 0;'/><small style='color:#888;'>Đây là email tự động, vui lòng không trả lời email này.</small>"
                . "</div>";
            $emailService->sendEmailBcc($bccList, $subject, $body);
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
        $giangVien = Auth::user()->HoTen ?? 'Giảng viên';
        $bccList = [];
        foreach ($danhSachSinhVien as $sinhVien) {
            if ($sinhVien && $sinhVien->nguoiDung && !empty($sinhVien->nguoiDung->email)) {
                $bccList[] = $sinhVien->nguoiDung->email;
            }
        }
        $bccList = array_unique($bccList);
        if (!empty($bccList)) {
            $subject = "Giảng viên đã [Bỏ lưu trữ lớp học phần] $tenLop";
            $body = "<div style='font-family: Arial, sans-serif; color: #222;'>"
                . "<h2 style='color: #198754;'>Thông báo bỏ lưu trữ lớp học phần</h2>"
                . "<p>Xin chào sinh viên,</p>"
                . "<p><b>Giảng viên:</b> <span style='color:#0d6efd;'>$giangVien</span> đã <b style='color:#198754;'>bỏ lưu trữ</b> lớp học phần <b style='color:#0d6efd;'>$tenLop</b> (Bài giảng: <b>$tenBaiGiang</b>).</p>"
                . "<p style='margin: 18px 0; background: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 4px solid #198754;'>"
                . "Bạn đã có thể <b>truy cập lại</b> vào lớp học phần này trên hệ thống như bình thường.</p>"
                . "<p>Nếu có thắc mắc, vui lòng liên hệ giảng viên.</p>"
                . "<hr style='margin:24px 0 12px 0;'/><small style='color:#888;'>Đây là email tự động, vui lòng không trả lời email này.</small>"
                . "</div>";
            $emailService->sendEmailBcc($bccList, $subject, $body);
        }
        return redirect()->back()->with('success', 'Đã bỏ lưu trữ lớp học phần thành công!');
    }
}
