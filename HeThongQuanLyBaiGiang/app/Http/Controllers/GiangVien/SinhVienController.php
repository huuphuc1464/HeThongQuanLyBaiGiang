<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Models\SinhVien;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SinhVienController extends Controller
{
    public function danhSachSinhVien(Request $request, $maLopHocPhan)
    {
        $lopHocPhan = DB::table('lop_hoc_phan')
            ->where('MaLopHocPhan', $maLopHocPhan)
            ->where('MaNguoiTao', Auth::id())
            ->first();

        if (!$lopHocPhan) {
            abort(404, 'Lớp học phần không tồn tại hoặc bạn không có quyền truy cập');
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
                'nd.AnhDaiDien',
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
            ->where('MaNguoiTao', Auth::id())
            ->first();

        if (!$sinhVien) {
            abort(404, 'Sinh viên không tồn tại hoặc bạn không có quyền truy cập');
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

        $lopHocPhan = DB::table('lop_hoc_phan')
            ->where('MaLopHocPhan', $maLopHocPhan)
            ->where('MaNguoiTao', Auth::id())
            ->where('TrangThai', 1)
            ->first();

        if (!$lopHocPhan) {
            abort(404, 'Lớp học phần không tồn tại hoặc bạn không có quyền truy cập');
        }

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
                    $maXacNhanMoi = Str::uuid()->toString();

                    DB::table('danh_sach_lop')
                        ->where('MaLopHocPhan', $maLopHocPhan)
                        ->where('MaSinhVien', $maNguoiDung)
                        ->update([
                            'MaXacNhan' => $maXacNhanMoi,
                            'updated_at' => now('Asia/Ho_Chi_Minh'),
                        ]);

                    $this->guiEmailMoiThamGiaLop(
                        $emailService,
                        $email,
                        $nguoiDung,
                        $maLopHocPhan,
                        $maXacNhanMoi,
                        $lopHocPhan->TenLopHocPhan
                    );

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

            $this->guiEmailMoiThamGiaLop($emailService, $email, $nguoiDung, $maLopHocPhan, $maXacNhan, $lopHocPhan->TenLopHocPhan);
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

    public function themSinhVienBangFile(Request $request, EmailService $emailService, $maLopHocPhan)
    {
        $request->validate(
            [
                'excel_file' => 'required|file|mimes:xlsx',
            ],
            [
                'excel_file.required' => 'Vui lòng chọn file Excel.',
                'excel_file.file' => 'File không hợp lệ.',
                'excel_file.mimes' => 'File phải có định dạng .xlsx.'
            ]
        );

        try {
            $data = Excel::toArray([], $request->file('excel_file'));
            $rows = $data[0];

            $dataRows = array_slice($rows, 1);
            $dataRows = array_filter($dataRows, function ($row) {
                foreach ($row as $cell) {
                    if (trim($cell) !== '') return true;
                }
                return false;
            });

            if (count($dataRows) === 0) {
                return back()->with('warning', 'File Excel không chứa dữ liệu sinh viên.');
            }

            $header = array_map('trim', $rows[0] ?? []);
            $cotBatBuoc = ['Họ và tên', 'Mã số sinh viên', 'Email', 'Ngày sinh', 'Giới tính'];
            foreach ($cotBatBuoc as $col) {
                if (!in_array($col, $header)) {
                    return back()->with('warning', 'Thiếu cột bắt buộc: ' . $col);
                }
            }

            $cotEmail = array_search('Email', $header);
            $cotMSSV = array_search('Mã số sinh viên', $header);
            $cotHoTen = array_search('Họ và tên', $header);
            $cotNgaySinh = array_search('Ngày sinh', $header);
            $cotGioiTinh = array_search('Giới tính', $header);

            $success = 0;
            $errors = [];

            foreach (array_slice($rows, 1) as $index => $row) {
                $rowNumber = $index + 2;
                $email = trim($row[$cotEmail] ?? '');
                $mssv = trim($row[$cotMSSV] ?? '');
                $hoTen = trim($row[$cotHoTen] ?? '');
                $gioiTinh = trim($row[$cotGioiTinh] ?? '');
                $ngaySinhRaw = $row[$cotNgaySinh] ?? null;

                if (is_null($ngaySinhRaw) || $ngaySinhRaw === '') {
                    $errors[] = "Dòng $rowNumber: Thiếu ngày sinh.";
                    continue;
                }
                try {
                    if (is_numeric($ngaySinhRaw)) {
                        $ngaySinh = Date::excelToDateTimeObject($ngaySinhRaw)->format('Y-m-d');
                    } else {
                        $date = \DateTime::createFromFormat('d/m/Y', trim($ngaySinhRaw));
                        if (!$date || $date->format('d/m/Y') !== trim($ngaySinhRaw)) {
                            throw new \Exception();
                        }
                        $ngaySinh = $date->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $errors[] = "Dòng $rowNumber: Ngày sinh không đúng định dạng dd/MM/yyyy.";
                    continue;
                }
                if (!$hoTen || !$mssv || !$email || !$ngaySinh || !$gioiTinh) {
                    $errors[] = "Dòng $rowNumber: Thiếu thông tin bắt buộc.";
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Dòng $rowNumber: Email không hợp lệ.";
                    continue;
                }

                // Regex MSSV
                if (!preg_match('/^(04|03)(01|02|03|04|06|07|08|09|12|61|62|63|64|65|66|67|68|69)\d{2}(\d{1})\d{3}$/', $mssv)) {
                    $errors[] = "Dòng $rowNumber: MSSV không đúng định dạng.";
                    continue;
                }

                // Kiểm tra tồn tại trong DB
                $nguoiDung = NguoiDung::where('Email', $email)->orWhere('TenTaiKhoan', $email)->first();
                $emailExists = NguoiDung::where('Email', $email)->exists();
                $mssvExists = SinhVien::where('MSSV', $mssv)->exists();

                if (!$nguoiDung && $emailExists) {
                    $errors[] = "Dòng $rowNumber: Email đã tồn tại trong hệ thống.";
                    continue;
                }
                if (!$nguoiDung && $mssvExists) {
                    $errors[] = "Dòng $rowNumber: MSSV đã tồn tại trong hệ thống.";
                    continue;
                }

                $sinhVienMoi = false;

                if (!$nguoiDung) {
                    $nguoiDung = $this->taoTaiKhoanSinhVienMoi($email, $mssv, $hoTen, $ngaySinh, $gioiTinh);
                    $sinhVienMoi = true;
                }

                // Kiểm tra sinh viên đã có trong lớp học phần chưa
                $daTonTai = DB::table('danh_sach_lop')
                    ->where('MaLopHocPhan', $maLopHocPhan)
                    ->where('MaSinhVien', $nguoiDung->MaNguoiDung)
                    ->exists();

                if ($daTonTai) {
                    $errors[] = "Dòng $rowNumber: Sinh viên đã có trong lớp.";
                    continue;
                }

                // Thêm vào lớp
                $maXacNhan = Str::uuid()->toString();
                DB::table('danh_sach_lop')->insert([
                    'MaLopHocPhan' => $maLopHocPhan,
                    'MaSinhVien' => $nguoiDung->MaNguoiDung,
                    'MaXacNhan' => $maXacNhan,
                    'TrangThai' => 0,
                    'created_at' => now('Asia/Ho_Chi_Minh'),
                    'updated_at' => now('Asia/Ho_Chi_Minh')
                ]);
                $tenLopHocPhan = DB::table('lop_hoc_phan')->where('MaLopHocPhan', $maLopHocPhan)->value('TenLopHocPhan');
                try {
                    $this->guiEmailMoiThamGiaLop($emailService, $email, $nguoiDung, $maLopHocPhan, $maXacNhan, $tenLopHocPhan, $sinhVienMoi);
                    $success++;
                } catch (\Exception $e) {
                    $errors[] = "Dòng $rowNumber: Gửi email thất bại.";
                    Log::error($e);
                }
            }

            $message = `<strong>Đã thêm thành công {$success} sinh viên.</strong>`;
            if (count($errors)) {
                $message .= '<br>Lỗi:<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
                return back()->with('warning', $message);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('warning', 'Lỗi khi xử lý file Excel: ' . $e->getMessage());
        }
    }
    private function taoTaiKhoanSinhVienMoi($email, $mssv, $hoTen, $ngaySinh, $gioiTinh)
    {
        $nguoiDung = NguoiDung::create([
            'TenTaiKhoan' => $email,
            'Email' => $email,
            'HoTen' => $hoTen,
            'MatKhau' => Hash::make($mssv),
            'NgaySinh' => $ngaySinh,
            'GioiTinh' => $gioiTinh,
            'LanDauDangNhap' => 1,
            'MaVaiTro' => 3,
            'TrangThai' => 1,
            'created_at' => now('Asia/Ho_Chi_Minh'),
            'updated_at' => now('Asia/Ho_Chi_Minh')
        ]);

        SinhVien::create([
            'MaNguoiDung' => $nguoiDung->MaNguoiDung,
            'MSSV' => $mssv,
            'created_at' => now('Asia/Ho_Chi_Minh'),
            'updated_at' => now('Asia/Ho_Chi_Minh')
        ]);

        return $nguoiDung;
    }

    private function guiEmailMoiThamGiaLop($emailService, $email, $nguoiDung, $maLopHocPhan, $maXacNhan, $tenLopHocPhan = null, $sinhVienMoi = false)
    {
        // Gửi email thông báo tài khoản mới
        if ($sinhVienMoi) {
            $subject1 = 'Thông tin tài khoản sinh viên mới';
            $body1 = '
            <div style="font-family: Arial, sans-serif; font-size: 15px; color: #333;">
                <p>Xin chào <strong>' . $nguoiDung->HoTen . '</strong>,</p>
                <p>Hệ thống Quản lý bài giảng xin thông báo rằng tài khoản sinh viên của bạn đã được khởi tạo thành công. Dưới đây là thông tin đăng nhập:</p>
                <table cellpadding="6" cellspacing="0" border="0" style="background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                    <tr>
                        <td><strong>Tên đăng nhập (email):</strong></td>
                        <td>' . $nguoiDung->Email . '</td>
                    </tr>
                    <tr>
                        <td><strong>Mật khẩu tạm thời:</strong></td>
                        <td>' . $nguoiDung->SinhVien->MSSV . '</td>
                    </tr>
                </table>
                <p><strong>Lưu ý:</strong> Đây là mật khẩu tạm thời. Vui lòng đăng nhập và thay đổi mật khẩu ngay lần đầu tiên sử dụng để đảm bảo bảo mật thông tin.</p>
                <p>Nếu bạn có bất kỳ thắc mắc hoặc cần hỗ trợ, xin vui lòng liên hệ với quản trị viên hệ thống.</p>
                <p style="margin-top: 30px;">Trân trọng,<br>
                <strong>Hệ thống Quản lý bài giảng</strong></p>
            </div>';
            $emailService->sendEmail($email, $subject1, $body1);
        }

        // Gửi email mời tham gia lớp học phần
        $link = route('xac-nhan-tham-gia-lop', [
            'maLopHocPhan' => $maLopHocPhan,
            'maXacNhan' => $maXacNhan
        ]);

        $subject2 = 'Thư mời tham gia lớp học phần';
        $body2 = '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"><title>Thư mời tham gia lớp học phần</title></head>
        <body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
            <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 25px 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
                <h2 style="color: #007bff;"><i class="fa-solid fa-graduation-cap"></i> Thư mời tham gia lớp học phần</h2>
                <p>Chào bạn <strong>' . $nguoiDung->HoTen . '</strong>,</p>
                <p>Bạn đã được mời tham gia lớp học phần <strong>' . $tenLopHocPhan . '</strong>.</p>
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
        $emailService->sendEmail($email, $subject2, $body2);
    }
}