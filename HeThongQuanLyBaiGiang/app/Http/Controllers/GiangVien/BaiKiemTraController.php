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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BaiKiemTraController extends Controller
{
    public function danhSachBaiKiemTra(Request $request)
    {
        $query = DB::table('bai_kiem_tra as bkt')
            ->join('lop_hoc_phan as lhp', function ($join) {
                $join->on('lhp.MaLopHocPhan', '=', 'bkt.MaLopHocPhan')
                    ->on('lhp.MaNguoiTao', '=', 'bkt.MaGiangVien');
            })
            ->where('bkt.MaGiangVien', Auth::id())
            ->select('bkt.*', 'lhp.TenLopHocPhan');

        // Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('bkt.TenBaiKiemTra', 'like', "%{$search}%")
                    ->orWhere('bkt.MoTa', 'like', "%{$search}%")
                    ->orWhere('lhp.TenLopHocPhan', 'like', "%{$search}%");
            });
        }

        // Lọc theo lớp học phần
        if ($maLopHocPhan = $request->input('filterClass')) {
            $query->where('bkt.MaLopHocPhan', $maLopHocPhan);
        }

        // Phân trang
        $perPage = $request->input('itemsPerPage', 10);
        $baiKiemTras = $query->paginate($perPage);

        $lopHocPhan = DB::table('lop_hoc_phan')
            ->where('MaNguoiTao', Auth::id())
            ->select('MaLopHocPhan', 'TenLopHocPhan')
            ->get();

        return view('giangvien.quanLyBaiKiemTra.danhSachBaiKiemTra', compact('lopHocPhan', 'baiKiemTras'));
    }

    public function hienFormThemBaiKiemTra()
    {
        $lopHocPhan = DB::table('lop_hoc_phan')
            ->where('MaNguoiTao', Auth::id())
            ->select('MaLopHocPhan', 'TenLopHocPhan')
            ->get();

        return view('giangvien.quanLyBaiKiemTra.themBaiKiemTra', compact('lopHocPhan'));
    }

    public function themBaiKiemTra(Request $request, EmailService $emailService)
    {
        $request->validate([
            'quizName' => 'required|string|max:255',
            'classId' => 'required|integer|exists:lop_hoc_phan,MaLopHocPhan',
            'startTime' => 'required|date|after:now',
            'endTime' => 'required|date|after:startTime',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'questions.*.cauHoi' => 'required|string',
            'questions.*.dapAnA' => 'required|string',
            'questions.*.dapAnB' => 'required|string',
            'questions.*.dapAnC' => 'required|string',
            'questions.*.dapAnD' => 'required|string',
            'questions.*.dapAnDung' => 'required|in:A,B,C,D',
        ], [
            'quizName.required' => 'Tên bài kiểm tra không được để trống.',
            'classId.required' => 'Vui lòng chọn lớp học phần.',
            'classId.exists' => 'Lớp học phần không tồn tại.',
            'startTime.required' => 'Thời gian bắt đầu không được để trống.',
            'startTime.after' => 'Thời gian bắt đầu phải sau thời điểm hiện tại.',
            'endTime.required' => 'Thời gian kết thúc không được để trống.',
            'endTime.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'questions.*.cauHoi.required' => 'Nội dung câu hỏi không được để trống.',
            'questions.*.dapAnA.required' => 'Đáp án A không được để trống.',
            'questions.*.dapAnB.required' => 'Đáp án B không được để trống.',
            'questions.*.dapAnC.required' => 'Đáp án C không được để trống.',
            'questions.*.dapAnD.required' => 'Đáp án D không được để trống.',
            'questions.*.dapAnDung.required' => 'Vui lòng chọn đáp án đúng.',
            'questions.*.dapAnDung.in' => 'Đáp án đúng phải là A, B, C hoặc D.',
        ]);

        try {
            DB::beginTransaction();

            $baiKiemTra = BaiKiemTra::create([
                'MaLopHocPhan' => $request->classId,
                'MaGiangVien' => Auth::id(),
                'TenBaiKiemTra' => $request->quizName,
                'ThoiGianBatDau' => Carbon::parse($request->startTime),
                'ThoiGianKetThuc' => Carbon::parse($request->endTime),
                'MoTa' => $request->description,
                'TrangThai' => $request->status,
                'created_at' => now('Asia/Ho_Chi_Minh'),
                'updated_at' => now('Asia/Ho_Chi_Minh'),
            ]);

            foreach ($request->questions as $question) {
                CauHoiBaiKiemTra::create([
                    'MaBaiKiemTra' => $baiKiemTra->MaBaiKiemTra,
                    'CauHoi' => $question['cauHoi'],
                    'DapAnA' => $question['dapAnA'],
                    'DapAnB' => $question['dapAnB'],
                    'DapAnC' => $question['dapAnC'],
                    'DapAnD' => $question['dapAnD'],
                    'DapAnDung' => $question['dapAnDung'],
                    'created_at' => now('Asia/Ho_Chi_Minh'),
                    'updated_at' => now('Asia/Ho_Chi_Minh'),
                ]);
            }

            $noiDungThongBao = 'Giảng viên đã tạo bài kiểm tra mới: "' . $request->quizName . '" vào lớp học phần. Vui lòng kiểm tra và chuẩn bị.';
            ThongBao::create([
                'MaLopHocPhan' => $request->classId,
                'MaNguoiTao' => Auth::id(),
                'NoiDung' => $noiDungThongBao,
                'ThoiGianTao' => now('Asia/Ho_Chi_Minh'),
                'TrangThai' => 1,
                'created_at' => now('Asia/Ho_Chi_Minh'),
                'updated_at' => now('Asia/Ho_Chi_Minh'),
            ]);

            $emails = DB::table('danh_sach_lop')
                ->join('nguoi_dung', 'danh_sach_lop.MaSinhVien', '=', 'nguoi_dung.MaNguoiDung')
                ->where('danh_sach_lop.MaLopHocPhan', $request->classId)
                ->select('nguoi_dung.MaNguoiDung', 'nguoi_dung.HoTen', 'nguoi_dung.Email')
                ->get();

            $start = Carbon::parse($baiKiemTra->ThoiGianBatDau)->format('H:i:s d/m/Y');
            $end = Carbon::parse($baiKiemTra->ThoiGianKetThuc)->format('H:i:s d/m/Y');
            foreach ($emails as $email) {
                $studentName = $email->HoTen;
                $emailAddress = $email->Email;
                $body = "Chào {$studentName},<br><br>";
                $body .= "{$noiDungThongBao}<br><br>";
                $body .= "📄 Tên bài kiểm tra: {$baiKiemTra->TenBaiKiemTra}<br>";
                $body .= "📄 Mô tả bài kiểm tra: {$baiKiemTra->MoTa}<br>";
                $body .= "⌚ Thời gian bắt đầu: {$start}<br>";
                $body .= "⏳ Thời gian kết thúc: {$end}<br>";
                $body .= "<br>Trân trọng,<br>Hệ thống managing bài giảng trực tuyến.";
                try {
                    $emailService->sendEmail($emailAddress, 'Thêm bài kiểm tra mới', $body);
                } catch (\Throwable $e) {
                    Log::error("Không thể gửi email đến {$emailAddress}: " . $e->getMessage());
                }
            }

            DB::commit();
            return redirect()->route('giangvien.bai-kiem-tra.danh-sach')->with('success', 'Thêm bài kiểm tra và các câu hỏi thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('errorSystem', 'Lỗi khi thêm bài kiểm tra: ' . $e->getMessage());
        }
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
                    : Carbon::parse($thoiGianKetThucRaw);
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
                    $emailAddress = $email->Email;
                    $body = "Chào {$studentName},<br><br>";
                    $body .= "{$noiDungThongBao}<br><br>";
                    $body .= "📄 Tên bài kiểm tra: {$baiKiemTra->TenBaiKiemTra}<br>";
                    $body .= "📄 Mô tả bài kiểm tra: {$baiKiemTra->MoTa}<br>";
                    $body .= "⌚ Thời gian bắt đầu: {$start}<br>";
                    $body .= "⏳ Thời gian kết thúc: {$end}<br>";
                    $body .= "<br>Trân trọng,<br>Hệ thống managing bài giảng trực tuyến.";
                    try {
                        $emailService->sendEmail($emailAddress, 'Thêm bài kiểm tra mới', $body);
                    } catch (\Throwable $e) {
                        Log::error("Không thể gửi email đến {$emailAddress}: " . $e->getMessage());
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

    public function nhanBanBaiKiemTra(Request $request)
    {
        $request->validate([
            'MaBaiKiemTra' => 'required|integer|exists:bai_kiem_tra,MaBaiKiemTra',
            'MaLopHocPhan' => 'required|integer|exists:lop_hoc_phan,MaLopHocPhan',
            'TrangThai' => 'required|in:0,1',
        ], [
            'MaBaiKiemTra.required' => 'Vui lòng chọn bài kiểm tra cần nhân bản.',
            'MaLopHocPhan.required' => 'Vui lòng chọn lớp học phần đích.',
            'MaBaiKiemTra.exists' => 'Bài kiểm tra không tồn tại',
            'MaLopHocPhan.exists' => 'Lớp học phần không tồn tại',
            'TrangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        DB::beginTransaction();

        try {
            $baiGoc = BaiKiemTra::findOrFail($request->MaBaiKiemTra);

            if ($baiGoc->MaGiangVien != Auth::id()) {
                return redirect()->back()->with('errorSystem', 'Bạn không có quyền nhân bản bài kiểm tra này.');
            }

            $baiMoi = BaiKiemTra::create([
                'MaLopHocPhan' => $request->MaLopHocPhan,
                'MaGiangVien' => Auth::id(),
                'TenBaiKiemTra' => 'Bản sao của ' .  $baiGoc->TenBaiKiemTra,
                'ThoiGianBatDau' => $baiGoc->ThoiGianBatDau,
                'ThoiGianKetThuc' => $baiGoc->ThoiGianKetThuc,
                'MoTa' => $baiGoc->MoTa,
                'TrangThai' => $request->TrangThai,
                'created_at' => now('Asia/Ho_Chi_Minh'),
                'updated_at' => now('Asia/Ho_Chi_Minh')
            ]);

            $cauHoiGoc = CauHoiBaiKiemTra::where('MaBaiKiemTra', $baiGoc->MaBaiKiemTra)->get();

            foreach ($cauHoiGoc as $cauHoi) {
                CauHoiBaiKiemTra::create([
                    'MaBaiKiemTra' => $baiMoi->MaBaiKiemTra,
                    'CauHoi' => $cauHoi->CauHoi,
                    'DapAnA' => $cauHoi->DapAnA,
                    'DapAnB' => $cauHoi->DapAnB,
                    'DapAnC' => $cauHoi->DapAnC,
                    'DapAnD' => $cauHoi->DapAnD,
                    'DapAnDung' => $cauHoi->DapAnDung,
                    'created_at' => now('Asia/Ho_Chi_Minh'),
                    'updated_at' => now('Asia/Ho_Chi_Minh')
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Nhân bản bài kiểm tra thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('errorSystem', 'Lỗi khi nhân bản: ' . $e->getMessage());
        }
    }

    public function chiTietBaiKiemTra($id)
    {
        $baiKiemTra = BaiKiemTra::where('MaBaiKiemTra', $id)
            ->where('MaGiangVien', Auth::id())
            ->firstOrFail();

        $cauHois = CauHoiBaiKiemTra::where('MaBaiKiemTra', $id)->get();

        $soLuongSinhVien = DB::table('ket_qua_bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->distinct('MaSinhVien')
            ->count('MaSinhVien');

        return view('giangvien.quanLyBaiKiemTra.chiTietBaiKiemTra', compact('baiKiemTra', 'cauHois', 'soLuongSinhVien'));
    }

    public function hienFormSuaBaiKiemTra($id)
    {
        $baiKiemTra = BaiKiemTra::where('MaBaiKiemTra', $id)
            ->where('MaGiangVien', Auth::id())
            ->firstOrFail();

        $lopHocPhan = DB::table('lop_hoc_phan')
            ->where('MaNguoiTao', Auth::id())
            ->select('MaLopHocPhan', 'TenLopHocPhan')
            ->get();

        $cauHois = CauHoiBaiKiemTra::where('MaBaiKiemTra', $id)->get();

        $soLuongSinhVien = DB::table('ket_qua_bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->distinct('MaSinhVien')
            ->count('MaSinhVien');

        return view('giangvien.quanLyBaiKiemTra.suaBaiKiemTra', compact('baiKiemTra', 'lopHocPhan', 'cauHois', 'soLuongSinhVien'));
    }

    public function capNhatBaiKiemTra(Request $request, $id)
    {
        $baiKiemTra = BaiKiemTra::where('MaBaiKiemTra', $id)
            ->where('MaGiangVien', Auth::id())
            ->firstOrFail();

        $soLuongSinhVien = DB::table('ket_qua_bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->distinct('MaSinhVien')
            ->count('MaSinhVien');

        if ($soLuongSinhVien > 0) {
            return redirect()->back()->with('errorSystem', 'Không thể sửa bài kiểm tra vì đã có sinh viên làm bài.');
        }

        $request->validate([
            'quizName' => 'required|string|max:255',
            'classId' => 'required|integer|exists:lop_hoc_phan,MaLopHocPhan',
            'startTime' => 'required|date|after:now',
            'endTime' => 'required|date|after:startTime',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'questions.*.cauHoi' => 'required|string',
            'questions.*.dapAnA' => 'required|string',
            'questions.*.dapAnB' => 'required|string',
            'questions.*.dapAnC' => 'required|string',
            'questions.*.dapAnD' => 'required|string',
            'questions.*.dapAnDung' => 'required|in:A,B,C,D',
        ], [
            'quizName.required' => 'Tên bài kiểm tra không được để trống.',
            'classId.required' => 'Vui lòng chọn lớp học phần.',
            'classId.exists' => 'Lớp học phần không tồn tại.',
            'startTime.required' => 'Thời gian bắt đầu không được để trống.',
            'startTime.after' => 'Thời gian bắt đầu phải sau thời điểm hiện tại.',
            'endTime.required' => 'Thời gian kết thúc không được để trống.',
            'endTime.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'questions.*.cauHoi.required' => 'Nội dung câu hỏi không được để trống.',
            'questions.*.dapAnA.required' => 'Đáp án A không được để trống.',
            'questions.*.dapAnB.required' => 'Đáp án B không được để trống.',
            'questions.*.dapAnC.required' => 'Đáp án C không được để trống.',
            'questions.*.dapAnD.required' => 'Đáp án D không được để trống.',
            'questions.*.dapAnDung.required' => 'Vui lòng chọn đáp án đúng.',
            'questions.*.dapAnDung.in' => 'Đáp án đúng phải là A, B, C hoặc D.',
        ]);

        try {
            DB::beginTransaction();

            $baiKiemTra->update([
                'MaLopHocPhan' => $request->classId,
                'TenBaiKiemTra' => $request->quizName,
                'ThoiGianBatDau' => Carbon::parse($request->startTime),
                'ThoiGianKetThuc' => Carbon::parse($request->endTime),
                'MoTa' => $request->description,
                'TrangThai' => $request->status,
                'updated_at' => now('Asia/Ho_Chi_Minh'),
            ]);

            CauHoiBaiKiemTra::where('MaBaiKiemTra', $id)->delete();

            foreach ($request->questions as $question) {
                CauHoiBaiKiemTra::create([
                    'MaBaiKiemTra' => $baiKiemTra->MaBaiKiemTra,
                    'CauHoi' => $question['cauHoi'],
                    'DapAnA' => $question['dapAnA'],
                    'DapAnB' => $question['dapAnB'],
                    'DapAnC' => $question['dapAnC'],
                    'DapAnD' => $question['dapAnD'],
                    'DapAnDung' => $question['dapAnDung'],
                    'created_at' => now('Asia/Ho_Chi_Minh'),
                    'updated_at' => now('Asia/Ho_Chi_Minh'),
                ]);
            }

            DB::commit();
            return redirect()->route('giangvien.bai-kiem-tra.danh-sach')->with('success', 'Cập nhật bài kiểm tra thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('errorSystem', 'Lỗi khi cập nhật bài kiểm tra: ' . $e->getMessage());
        }
    }

    public function xoaBaiKiemTra($id)
    {
        $baiKiemTra = BaiKiemTra::where('MaBaiKiemTra', $id)
            ->where('MaGiangVien', Auth::id())
            ->firstOrFail();

        $soLuongSinhVien = DB::table('ket_qua_bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->distinct('MaSinhVien')
            ->count('MaSinhVien');

        if ($soLuongSinhVien > 0) {
            return redirect()->back()->with('errorSystem', 'Không thể xóa bài kiểm tra vì đã có sinh viên làm bài.');
        }

        try {
            DB::beginTransaction();
            CauHoiBaiKiemTra::where('MaBaiKiemTra', $id)->delete();
            $baiKiemTra->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Xóa bài kiểm tra thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('errorSystem', 'Lỗi khi xóa bài kiểm tra: ' . $e->getMessage());
        }
    }

    public function xuatBaiKiemTra($id)
    {
        $giangVienId = Auth::id();

        $laNguoiTao = DB::table('lop_hoc_phan as lhp')
            ->join('bai_kiem_tra as bkt', 'lhp.MaLopHocPhan', '=', 'bkt.MaLopHocPhan')
            ->where('bkt.MaBaiKiemTra', $id)
            ->where('bkt.MaGiangVien', $giangVienId)
            ->where('lhp.MaNguoiTao', $giangVienId)
            ->exists();

        if (!$laNguoiTao) {
            return redirect()->back()->with('errorSystem', 'Bạn không có quyền truy cập vào bài kiểm tra này.');
        }

        $baiKiemTra = DB::table('bai_kiem_tra')->where('MaBaiKiemTra', $id)->first();
        if (!$baiKiemTra) {
            return redirect()->back()->with('errorSystem', 'Bài kiểm tra không tồn tại.');
        }

        $cauHois = DB::table('cau_hoi_bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($baiKiemTra->TenBaiKiemTra);

        // Thông tin bài kiểm tra
        $sheet->setCellValue('A1', 'Tên bài kiểm tra');
        $sheet->setCellValue('B1', $baiKiemTra->TenBaiKiemTra);
        $sheet->setCellValue('A2', 'Thời gian bắt đầu');
        $sheet->setCellValue('B2', \Carbon\Carbon::parse($baiKiemTra->ThoiGianBatDau)->format('d/m/Y H:i:s'));
        $sheet->setCellValue('A3', 'Thời gian kết thúc');
        $sheet->setCellValue('B3', \Carbon\Carbon::parse($baiKiemTra->ThoiGianKetThuc)->format('d/m/Y H:i:s'));
        $sheet->setCellValue('A4', 'Mô tả');
        $sheet->setCellValue('B4', $baiKiemTra->MoTa);
        $sheet->setCellValue('A5', 'Trạng thái');
        $sheet->setCellValue('B5', $baiKiemTra->TrangThai ? 'Hiện' : 'Ẩn');

        for ($i = 1; $i <= 5; $i++) {
            $sheet->getStyle("A$i")->getFont()->setBold(true);
            $sheet->getStyle("A$i")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ADD8E6');
            $sheet->getStyle("A$i")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
            $sheet->getStyle("B$i")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Câu hỏi 
        $headers = ['Câu hỏi', 'Đáp án A', 'Đáp án B', 'Đáp án C', 'Đáp án D', 'Đáp án đúng'];
        $sheet->fromArray($headers, null, 'A6');
        $headerRange = 'A6:F6';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);

        // Dữ liệu câu hỏi
        $row = 7;
        foreach ($cauHois as $q) {
            $sheet->setCellValue("A$row", $q->CauHoi);
            $sheet->setCellValue("B$row", $q->DapAnA);
            $sheet->setCellValue("C$row", $q->DapAnB);
            $sheet->setCellValue("D$row", $q->DapAnC);
            $sheet->setCellValue("E$row", $q->DapAnD);
            $sheet->setCellValue("F$row", $q->DapAnDung);
            $row++;
        }

        if ($row > 7) {
            $sheet->getStyle("A7:F" . ($row - 1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'BaiKiemTra_' . str_replace(' ', '_', $baiKiemTra->TenBaiKiemTra) . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function xuatKetQuaBaiLam($id)
    {
        $giangVienId = Auth::id();

        $laNguoiTao = DB::table('lop_hoc_phan as lhp')
            ->join('bai_kiem_tra as bkt', 'lhp.MaLopHocPhan', '=', 'bkt.MaLopHocPhan')
            ->where('bkt.MaBaiKiemTra', $id)
            ->where('bkt.MaGiangVien', $giangVienId)
            ->where('lhp.MaNguoiTao', $giangVienId)
            ->exists();

        if (!$laNguoiTao) {
            return redirect()->back()->with('errorSystem', 'Bạn không có quyền truy cập vào bài kiểm tra này.');
        }

        $baiKiemTra = DB::table('bai_kiem_tra')
            ->join('lop_hoc_phan', 'bai_kiem_tra.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->where('bai_kiem_tra.MaBaiKiemTra', $id)
            ->select('bai_kiem_tra.*', 'lop_hoc_phan.TenLopHocPhan')
            ->first();

        if (!$baiKiemTra) return redirect()->back()->with('errorSystem', 'Bài kiểm tra không tồn tại.');

        $soCauHoi = DB::table('cau_hoi_bai_kiem_tra')->where('MaBaiKiemTra', '=', $id)->count();
        if ($soCauHoi == 0) return redirect()->back()->with('errorSystem', 'Bài kiểm tra không có câu hỏi');

        $ketQua = DB::table('ket_qua_bai_kiem_tra as kq')
            ->join('sinh_vien as sv', 'sv.MaNguoiDung', '=', 'kq.MaSinhVien')
            ->join('nguoi_dung as nd', 'nd.MaNguoiDung', '=', 'sv.MaNguoiDung')
            ->where('kq.MaBaiKiemTra', $id)
            ->select('sv.*', 'nd.HoTen', 'nd.Email', 'kq.*')
            ->orderBy('kq.NgayNop')
            ->get();

        if ($ketQua->isEmpty()) return redirect()->back()->with('errorSystem', 'Chưa có sinh viên làm bài kiểm tra');

        $spreadsheet = new Spreadsheet();

        // Sheet 1: Thông tin bài 
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Thông tin');
        $sheet1->setCellValue('A1', 'THÔNG TIN BÀI KIỂM TRA');
        $sheet1->mergeCells('A1:B1');
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet1->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet1->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet1->getStyle('A1:B1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet1->getStyle('A1:B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D0E3FA');

        $sheet1->fromArray([
            ['Tên bài kiểm tra:', $baiKiemTra->TenBaiKiemTra],
            ['Lớp học phần:', $baiKiemTra->TenLopHocPhan],
            ['Thời gian tạo:', \Carbon\Carbon::parse($baiKiemTra->created_at)->format('d/m/Y H:i:s')],
            ['Thời gian cập nhật:', \Carbon\Carbon::parse($baiKiemTra->updated_at)->format('d/m/Y H:i:s')],
            ['Thời gian bắt đầu:', \Carbon\Carbon::parse($baiKiemTra->ThoiGianBatDau)->format('d/m/Y H:i:s')],
            ['Thời gian kết thúc:', \Carbon\Carbon::parse($baiKiemTra->ThoiGianKetThuc)->format('d/m/Y H:i:s')],
            ['Tổng số câu hỏi:', $soCauHoi ?? 'Không có câu hỏi'],
            ['Số sinh viên nộp:', count($ketQua)],
            ['Ngày xuất báo cáo:', now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')],
        ], null, 'A3');

        $sheet1->getStyle('A3:A11')->getFont()->setBold(true);
        $sheet1->getStyle('A3:B11')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet1->getStyle('A3:B11')->getAlignment()->setWrapText(true);
        $sheet1->getStyle('A3:B11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet1->getStyle('A3:B11')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet1->getStyle('A3:B11')->getFont()->setSize(12);
        $sheet1->getStyle('A3:B11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet1->getColumnDimension('A')->setAutoSize(true);
        $sheet1->getColumnDimension('B')->setAutoSize(true);

        // Sheet 2: Kết quả bài làm
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Kết quả');

        $headers = ['STT', 'MSSV', 'Tên sinh viên', 'Email', 'Thời gian nộp', 'Tổng câu', 'Số đúng', 'Điểm', 'Câu hỏi', 'Đáp án sinh viên', 'Đáp án đúng', 'Kết quả'];
        $sheet2->fromArray($headers, null, 'A1');
        $sheet2->getStyle('A1:L1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet2->getStyle('A1:L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4682B4');

        $row = 2;
        $index = 1;

        foreach ($ketQua as $kq) {
            $chiTiet = DB::table('chi_tiet_ket_qua')
                ->join('cau_hoi_bai_kiem_tra as ch', 'chi_tiet_ket_qua.MaCauHoi', '=', 'ch.MaCauHoi')
                ->where('MaKetQua', $kq->MaKetQua)
                ->select('ch.CauHoi', 'ch.DapAnDung', 'chi_tiet_ket_qua.DapAnSinhVien', 'chi_tiet_ket_qua.KetQua')
                ->get();

            $startRow = $row;
            foreach ($chiTiet as $ct) {
                $sheet2->setCellValue("I$row", $ct->CauHoi);
                $sheet2->setCellValue("J$row", $ct->DapAnSinhVien);
                $sheet2->setCellValue("K$row", $ct->DapAnDung);
                $sheet2->setCellValue("L$row", $ct->KetQua ? 'Đúng' : 'Sai');

                $sheet2->getStyle("L$row")->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($ct->KetQua ? '90EE90' : 'FFA07A');

                $row++;
            }

            $endRow = $row - 1;
            if ($startRow <= $endRow) {
                for ($col = 'A'; $col <= 'H'; $col++) {
                    $sheet2->mergeCells("$col$startRow:$col$endRow");
                    $sheet2->getStyle("$col$startRow:$col$endRow")->getAlignment()->setVertical('center')->setHorizontal('center');
                }

                $sheet2->setCellValue("A$startRow", $index++);
                $sheet2->setCellValue("B$startRow", $kq->MSSV);
                $sheet2->setCellValue("C$startRow", $kq->HoTen);
                $sheet2->setCellValue("D$startRow", $kq->Email);
                $sheet2->setCellValue("E$startRow", \Carbon\Carbon::parse($kq->NgayNop)->format('d/m/Y H:i:s'));
                $sheet2->setCellValue("F$startRow", $kq->TongSoCauHoi);
                $sheet2->setCellValue("G$startRow", $kq->TongCauDung);
                $sheet2->setCellValue("H$startRow", round($kq->TongCauDung / $soCauHoi * 10, 2));
            }
        }

        $sheet2->getStyle("A1:L" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('A', 'L') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // Xuất file
        $filename = 'KetQuaBaiKiemTra_' . str_replace(' ', '_', $baiKiemTra->TenBaiKiemTra) . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
