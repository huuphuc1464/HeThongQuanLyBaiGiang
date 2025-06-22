<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SinhVienController extends Controller
{
    public function danhSachSinhVien(Request $request, $maLopHocPhan)
    {
        $lopHocPhan = DB::table('lop_hoc_phan')
            ->where('MaLopHocPhan', $maLopHocPhan)
            ->where('MaNguoiTao', Auth::id())
            ->first();

        if (!$lopHocPhan) {
            abort(404, 'Lớp học phần không tồn tại');
        }

        $query = DB::table('danh_sach_lop as dsl')
            ->join('sinh_vien as sv', 'sv.MaNguoiDung', '=', 'dsl.MaSinhVien')
            ->join('nguoi_dung as nd', 'nd.MaNguoiDung', '=', 'dsl.MaSinhVien')
            ->join('lop_hoc_phan as lhp', 'lhp.MaLopHocPhan', '=', 'dsl.MaLopHocPhan')
            ->where('dsl.MaLopHocPhan', $maLopHocPhan)
            ->where('lhp.MaNguoiTao', Auth::id())
            ->select(
                'dsl.MaDanhSachLop',
                'nd.HoTen',
                'sv.MSSV',
                'nd.Email',
                'nd.NgaySinh',
                'nd.GioiTinh',
                'dsl.TrangThai'
            );

        if ($request->filled('search')) {
            $keywords = preg_split('/\s+/', trim($request->search));

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $q->orWhereRaw('LOWER(nd.HoTen) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(nd.Email) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(sv.MSSV) LIKE ?', ["%$kw%"]);
                }
            });
        }
        $sinhViens = $query->paginate(10)->withQueryString();

        return view('giangvien.quanLyLopHocPhan.danhSachSinhVien', compact('sinhViens', 'lopHocPhan'));
    }

    public function xoaSinhVien($maLopHocPhan, $maDanhSachLop)
    {
        $sinhVien = DB::table('danh_sach_lop')
            ->where('MaDanhSachLop', $maDanhSachLop)
            ->where('MaLopHocPhan', $maLopHocPhan)
            ->first();

        if (!$sinhVien) {
            return redirect()->back()->with('error', 'Không tìm thấy sinh viên trong lớp này.');
        }

        DB::table('danh_sach_lop')
            ->where('MaDanhSachLop', $maDanhSachLop)
            ->delete();

        return redirect()->back()->with('success', 'Đã xóa sinh viên khỏi lớp học phần thành công.');
    }

    public function themSinhVien(Request $request, EmailService $emailService, $maLopHocPhan)
    {
        $request->validate([
            'emails' => 'required|string',
        ]);

        $emails = array_filter(array_map('trim', explode(';', $request->emails)));
        $emailsHopLe = [];
        $emailsSaiDinhDang = [];
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailsHopLe[] = $email;
            } else {
                $emailsSaiDinhDang[] = $email;
            }
        }
        $emailsKhongCoTaiKhoan = [];
        $emailsDaMoiChuaXacNhan = [];
        $lopHocPhan = DB::table('lop_hoc_phan')
            ->where('MaLopHocPhan', $maLopHocPhan)
            ->where('MaNguoiTao', Auth::id())
            ->where('TrangThai', 1)
            ->first();

        if (!$lopHocPhan) {
            abort(404, 'Lớp học phần không tồn tại hoặc bạn không có quyền');
        }

        foreach ($emailsHopLe as $email) {
            $nguoiDung = DB::table('nguoi_dung as nd')
                ->join('sinh_vien', 'sinh_vien.MaNguoiDung', '=', 'nd.MaNguoiDung')
                ->where('nd.Email', $email)
                ->where('nd.MaVaiTro', 3)
                ->where('nd.TrangThai', 1)
                ->select('nd.MaNguoiDung', 'nd.HoTen', 'nd.Email')
                ->first();

            if (!$nguoiDung) {
                $emailsKhongCoTaiKhoan[] = $email;
                continue;
            }

            $maNguoiDung = $nguoiDung->MaNguoiDung;
            $daTonTai = DB::table('danh_sach_lop')
                ->where('MaLopHocPhan', $maLopHocPhan)
                ->where('MaSinhVien', $maNguoiDung)
                ->first();

            if ($daTonTai) {
                if ($daTonTai->TrangThai == 0) {
                    $emailsDaMoiChuaXacNhan[] = $email;
                }
                continue;
            }

            $maXacNhan = Str::uuid()->toString();
            DB::table('danh_sach_lop')->insert([
                'MaLopHocPhan' => $maLopHocPhan,
                'MaSinhVien' => $maNguoiDung,
                'MaXacNhan' => $maXacNhan,
                'TrangThai' => 0,
                'created_at' => now('Asia/Ho_Chi_Minh'),
                'updated_at' => now('Asia/Ho_Chi_Minh')
            ]);

            // Gửi email xác nhận tham gia đến sinh viên
            $link = route('xac-nhan-tham-gia-lop', ['maLopHocPhan' => $maLopHocPhan, 'maXacNhan' => $maXacNhan]);
            $subject = 'Xác nhận tham gia lớp học phần';
            $body = '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Xác nhận tham gia lớp học phần</title>
                    </head>
                    <body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
                        <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 25px 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
                            <h2 style="color: #007bff;"><i class="fa-solid fa-graduation-cap"></i> Thư mời tham gia lớp học phần</h2>
                            <p>Chào bạn <strong>' . $nguoiDung->HoTen . '</strong>,</p>
                            <p>Bạn đã được mời tham gia lớp học phần <strong>' . $lopHocPhan->TenLopHocPhan . '</strong>.</p>
                            <p>Vui lòng nhấn vào nút bên dưới để xác nhận tham gia:</p>
                            <div style="text-align: center; margin: 25px 0;">
                                <a href="' . $link . '" style="background-color: #28a745; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                                    Xác nhận tham gia lớp học
                                </a>
                            </div>
                            <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
                            <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">
                            <p style="font-size: 12px; color: #999;">Email này được gửi tự động từ hệ thống quản lý học phần. Vui lòng không trả lời thư này.</p>
                        </div>
                    </body>
                    </html>';
            $emailService->sendEmail($email, $subject, $body);
        }

        $messages = [];
        if (count($emailsKhongCoTaiKhoan)) {
            $messages[] = '&bull; Các email chưa có tài khoản, vui lòng thêm sinh viên bằng file excel để tạo tài khoản: <br>&emsp;' . implode('<br>&emsp;', $emailsKhongCoTaiKhoan);
        }
        if (count($emailsDaMoiChuaXacNhan)) {
            $messages[] = '&bull; Các email đã được mời nhưng chưa xác nhận: <br>&emsp;' . implode('<br>&emsp;', $emailsDaMoiChuaXacNhan);
        }
        if (count($emailsSaiDinhDang)) {
            $messages[] = '&bull; Các email không hợp lệ:<br>&emsp;' . implode('<br>&emsp;', $emailsSaiDinhDang);
        }
        if (count($messages)) {
            return redirect()->back()->with('warning', implode('<br>', $messages));
        }

        return redirect()->back()->with('success', 'Đã gửi lời mời xác nhận tham gia lớp học phần.');
    }

    public function xacNhanThamGiaLop($maLopHocPhan, $maXacNhan)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $danhSach = DB::table('danh_sach_lop')
            ->where('MaLopHocPhan', $maLopHocPhan)
            ->where('MaXacNhan', $maXacNhan)
            ->where('MaSinhVien', Auth::id())
            ->where('TrangThai', 0)
            ->first();

        if (!$danhSach) {
            return view('sinhvien.trangThaiXacNhan', [
                'success' => false,
                'message' => 'Liên kết không hợp lệ hoặc đã xác nhận trước đó.'
            ]);
        }

        DB::table('danh_sach_lop')
            ->where('MaDanhSachLop', $danhSach->MaDanhSachLop)
            ->update([
                'MaXacNhan' => null,
                'TrangThai' => 1,
                'updated_at' => now('Asia/Ho_Chi_Minh')
            ]);

        return view('sinhvien.trangThaiXacNhan', [
            'success' => true,
            'message' => 'Bạn đã xác nhận tham gia lớp học phần thành công.'
        ]);
    }
}
