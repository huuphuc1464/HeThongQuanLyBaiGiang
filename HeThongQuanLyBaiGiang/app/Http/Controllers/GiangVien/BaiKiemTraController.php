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
}
