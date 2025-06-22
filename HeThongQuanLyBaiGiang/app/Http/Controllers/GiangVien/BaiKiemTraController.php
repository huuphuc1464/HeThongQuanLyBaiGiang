<?php

namespace App\Http\Controllers\GiangVien;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BaiKiemTra;
use App\Models\CauHoiBaiKiemTra;
use App\Models\ThongBao;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BaiKiemTraController extends Controller
{
    public function danhSachBaiKiemTra()
    {
        $lopHocPhan = DB::table('lop_hoc_phan')->where('MaNguoiTao', Auth::id())->select('MaLopHocPhan', 'TenLopHocPhan')->get();
        return view('giangvien.quanLyBaiKiemTra.danhSachBaiKiemTra', compact('lopHocPhan'));
    }
    public function importBaiKiemTra(Request $request, EmailService $emailService)
    {
        $request->validate([
            'MaLopHocPhan' => 'required|integer',
            'file' => 'required|file|mimes:xlsx',
        ], [
            'MaLopHocPhan.required' => 'Vui lòng chọn lớp học phần.',
            'MaLopHocPhan.integer' => 'Lớp học phần không hợp lệ.',
            'file.required' => 'Vui lòng chọn file Excel.',
            'file.file' => 'Tệp tải lên phải là một file.',
            'file.mimes' => 'File phải có định dạng .xlsx.',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $tenBaiKiemTra = trim($sheet->getCell('B1')->getValue());
            $moTa = trim($sheet->getCell('B4')->getValue());
            $trangThaiRaw = strtolower(trim($sheet->getCell('B5')->getValue()));
            $thoiGianBatDauRaw = $sheet->getCell('B2')->getValue();
            $thoiGianKetThucRaw = $sheet->getCell('B3')->getValue();

            try {
                $thoiGianBatDau = is_numeric($thoiGianBatDauRaw)
                    ? Carbon::instance(Date::excelToDateTimeObject($thoiGianBatDauRaw))
                    : Carbon::parse($thoiGianBatDauRaw);

                $thoiGianKetThuc = is_numeric($thoiGianKetThucRaw)
                    ? Carbon::instance(Date::excelToDateTimeObject($thoiGianKetThucRaw))
                    : Carbon::parse($thoiGianBatDauRaw);
            } catch (\Exception $e) {
                return redirect()->back()->with('warning', 'Định dạng ngày giờ trong ô B2 hoặc B3 không hợp lệ.');
            }

            if (!in_array($trangThaiRaw, ['hiện', 'ẩn'])) {
                return redirect()->back()->with('warning', 'Trạng thái phải là "hiện" hoặc "ẩn".');
            }

            $trangThai = $trangThaiRaw === 'hiện' ? 1 : 0;
            if (
                empty($tenBaiKiemTra) ||
                empty($thoiGianBatDau) ||
                empty($thoiGianKetThuc) ||
                empty($moTa) ||
                empty($trangThai)
            ) {
                return redirect()->back()->with('warning', 'Thông tin bài kiểm tra không được bỏ trống.');
            }

            if ($thoiGianKetThuc->lessThanOrEqualTo($thoiGianBatDau)) {
                return redirect()->back()->with('warning', 'Thời gian kết thúc phải sau thời gian bắt đầu.');
            }

            if ($thoiGianBatDau->lessThan(Carbon::now())) {
                return redirect()->back()->with('warning', 'Thời gian bắt đầu phải lớn hơn thời điểm hiện tại.');
            }

            DB::beginTransaction();

            $maLopHocPhan = $request->input('MaLopHocPhan');
            $baiKiemTra = BaiKiemTra::create([
                'MaLopHocPhan' => $maLopHocPhan,
                'MaGiangVien' => Auth::id(),
                'TenBaiKiemTra' => $tenBaiKiemTra,
                'ThoiGianBatDau' => $thoiGianBatDau,
                'ThoiGianKetThuc' => $thoiGianKetThuc,
                'MoTa' => $moTa,
                'TrangThai' => $trangThai,
                'created_at' => now('Asia/Ho_Chi_Minh'),
                'updated_at' => now('Asia/Ho_Chi_Minh')
            ]);

            $highestRow = $sheet->getHighestRow();
            $errors = [];

            for ($row = 7; $row <= $highestRow; $row++) {
                $rowValues = [
                    $sheet->getCell("A$row")->getValue(),
                    $sheet->getCell("B$row")->getValue(),
                    $sheet->getCell("C$row")->getValue(),
                    $sheet->getCell("D$row")->getValue(),
                    $sheet->getCell("E$row")->getValue(),
                    $sheet->getCell("F$row")->getValue(),
                ];

                if (collect($rowValues)->every(fn($v) => is_null($v) || trim($v) === '')) {
                    continue;
                }

                $cauHoi = trim($rowValues[0]);
                $dapAnA = trim($rowValues[1]);
                $dapAnB = trim($rowValues[2]);
                $dapAnC = trim($rowValues[3]);
                $dapAnD = trim($rowValues[4]);
                $dapAnDung = strtoupper(trim($rowValues[5]));

                if (empty($cauHoi)) {
                    $errors[] = "Dòng $row: Thiếu câu hỏi.";
                    continue;
                }

                if (empty($dapAnA) || empty($dapAnB) || empty($dapAnC) || empty($dapAnD)) {
                    $errors[] = "Dòng $row: Thiếu một hoặc nhiều đáp án A/B/C/D.";
                    continue;
                }

                if (!in_array($dapAnDung, ['A', 'B', 'C', 'D'])) {
                    $errors[] = "Dòng $row: Đáp án đúng phải là một trong A, B, C, D.";
                    continue;
                }

                CauHoiBaiKiemTra::create([
                    'MaBaiKiemTra' => $baiKiemTra->MaBaiKiemTra,
                    'CauHoi' => $cauHoi,
                    'DapAnA' => $dapAnA,
                    'DapAnB' => $dapAnB,
                    'DapAnC' => $dapAnC,
                    'DapAnD' => $dapAnD,
                    'DapAnDung' => $dapAnDung,
                    'created_at' => now('Asia/Ho_Chi_Minh'),
                    'updated_at' => now('Asia/Ho_Chi_Minh')
                ]);
            }

            if (!empty($errors)) {
                DB::rollBack();
                $message = 'Import thất bại. Có lỗi trong file Excel.';
                $message .= '<br>Lỗi:<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
                return redirect()->back()->with('warning', $message);
            }
            if ($baiKiemTra->TrangThai == 1) {
                $noiDungThongBao = 'Giảng viên đã tạo bài kiểm tra mới: "' . $tenBaiKiemTra . '" vào lớp học phần. Vui lòng kiểm tra và chuẩn bị.';
                ThongBao::create([
                    'MaLopHocPhan' => $maLopHocPhan,
                    'MaNguoiTao' => Auth::id(),
                    'NoiDung' => $noiDungThongBao,
                    'ThoiGianTao' => now('Asia/Ho_Chi_Minh'),
                    'TrangThai' => 1,
                    'created_at' => now('Asia/Ho_Chi_Minh'),
                    'updated_at' => now('Asia/Ho_Chi_Minh')
                ]);

                $emails = DB::table('danh_sach_lop')
                    ->join('nguoi_dung', 'danh_sach_lop.MaSinhVien', '=', 'nguoi_dung.MaNguoiDung')
                    ->where('danh_sach_lop.MaLopHocPhan', $maLopHocPhan)
                    ->select('nguoi_dung.MaNguoiDung', 'nguoi_dung.HoTen', 'nguoi_dung.Email')
                    ->get();

                $start = \Carbon\Carbon::parse($baiKiemTra->ThoiGianBatDau)->format('H:i:s d/m/Y');
                $end = \Carbon\Carbon::parse($baiKiemTra->ThoiGianKetThuc)->format('H:i:s d/m/Y');
                foreach ($emails as $email) {
                    $studentName = $email->HoTen;
                    $email = $email->Email;
                    $body = "Chào {$studentName},<br><br>";
                    $body .= "{$noiDungThongBao}<br><br>";
                    $body .= "📄 Tên bài kiểm tra: {$baiKiemTra->TenBaiKiemTra}<br>";
                    $body .= "📄 Mô tả bài kiểm tra: {$baiKiemTra->MoTa}<br>";
                    $body .= "⌚ Thời gian bắt đầu: {$start}<br>";
                    $body .= "⏳ Thời gian kết thúc: {$end}<br>";
                    $body .= "<br>Trân trọng,<br>Hệ thống quản lý bài giảng trực tuyến.";

                    try {
                        $emailService->sendEmail($email, 'Thêm bài kiểm tra mới', $body);
                    } catch (\Throwable $e) {
                        Log::error("Không thể gửi email đến {$email}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Import bài kiểm tra và các câu hỏi thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Đã xảy ra lỗi trong quá trình xử lý.';
            $message .= '<br><strong>Chi tiết:</strong><ul><li>' . e($e->getMessage()) . '</li></ul>';
            return redirect()->back()->with('warning', $message);
        }
    }
}
