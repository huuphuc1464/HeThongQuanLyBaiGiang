<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BaiKiemTra;
use App\Models\CauHoiBaiKiemTra;
use App\Models\KetQuaBaiKiemTra;
use App\Models\ChiTietKetQua;
use App\Models\SinhVien;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class KetQuaBaiKiemTraController extends Controller
{
    /**
     * Hiển thị form làm bài kiểm tra
     */
    public function lamBaiKiemTra($maBaiKiemTra)
    {
        $baiKiemTra = BaiKiemTra::with(['giangVien', 'lopHocPhan.BaiGiang', 'cauHoiBaiKiemTra'])
            ->findOrFail($maBaiKiemTra);

        // Kiểm tra sinh viên có thuộc lớp học phần không
        $sinhVien = SinhVien::where('MaNguoiDung', Auth::id())->first();

        $thuocLop = $sinhVien->danhSachLop()
            ->where('MaLopHocPhan', $baiKiemTra->MaLopHocPhan)
            ->where('TrangThai', 1)
            ->exists();

        if (!$thuocLop) {
            return redirect()->back()->with('error', 'Bạn không thuộc lớp học phần này!');
        }


        // Kiểm tra sinh viên đã làm bài chưa
        $ketQuaBaiLam = KetQuaBaiKiemTra::where('MaBaiKiemTra', $maBaiKiemTra)
            ->where('MaSinhVien', $sinhVien->MaNguoiDung)
            ->first();

        if ($ketQuaBaiLam) {
            return redirect()->route('danh-sach-bai-kiem-tra')
                ->with('error', 'Bạn đã làm bài kiểm tra này rồi!');
        }
        // Kiểm tra thời gian làm bài
        $now = Carbon::now();
        $thoiGianBatDau = Carbon::parse($baiKiemTra->ThoiGianBatDau);
        $thoiGianKetThuc = Carbon::parse($baiKiemTra->ThoiGianKetThuc);

        if ($now < $thoiGianBatDau) {
            return redirect()->back()->with('error', 'Bài kiểm tra chưa bắt đầu!');
        }

        if ($now > $thoiGianKetThuc) {
            return redirect()->back()->with('error', 'Bài kiểm tra đã kết thúc!');
        }

        // Tạo session để lưu thời gian bắt đầu làm bài
        if (!session()->has('exam_start_time_' . $maBaiKiemTra)) {
            session(['exam_start_time_' . $maBaiKiemTra => $now->timestamp]);
        }

        // Tính thời gian còn lại
        $thoiGianConLai = $this->getThoiGianConLai($baiKiemTra, $maBaiKiemTra);
        if ($thoiGianConLai <= 0) {
            session()->forget('exam_start_time_' . $maBaiKiemTra);
            return redirect()->route('ket-qua-bai-kiem-tra', $maBaiKiemTra)
                ->with('error', 'Thời gian làm bài đã hết! Bài làm của bạn đã được nộp tự động.');
        }
        return view('sinhvien.lambaikiemtra', compact('baiKiemTra', 'thoiGianConLai'));
    }

    /**
     * Xử lý nộp bài kiểm tra
     */
    public function nopBaiKiemTra(Request $request, $maBaiKiemTra)
    {
        $baiKiemTra = BaiKiemTra::with('cauHoiBaiKiemTra')->findOrFail($maBaiKiemTra);

        // Kiểm tra sinh viên có thuộc lớp học phần không
        $sinhVien = SinhVien::where('MaNguoiDung', Auth::id())->first();
        $thuocLop = $sinhVien->danhSachLop()
            ->where('MaLopHocPhan', $baiKiemTra->MaLopHocPhan)
            ->where('TrangThai', 1)
            ->exists();

        if (!$thuocLop) {
            return response()->json([
                'message' => 'Bạn không thuộc lớp học phần này!',
                'redirect' => route('danh-sach-bai-kiem-tra')
            ], 403);
        }

        // Kiểm tra thời gian
        $now = Carbon::now();
        $thoiGianKetThuc = Carbon::parse($baiKiemTra->ThoiGianKetThuc);
        if ($now > $thoiGianKetThuc) {
            return response()->json([
                'message' => 'Bài kiểm tra đã kết thúc!',
                'redirect' => route('danh-sach-bai-kiem-tra')
            ], 422);
        }

        // Kiểm tra sinh viên đã làm bài chưa
        $daLamBai = KetQuaBaiKiemTra::where('MaBaiKiemTra', $maBaiKiemTra)
            ->where('MaSinhVien', $sinhVien->MaNguoiDung)
            ->exists();

        if ($daLamBai) {
            session()->forget('exam_start_time_' . $maBaiKiemTra);
            return response()->json([
                'message' => 'Bạn đã làm bài kiểm tra này rồi!',
                'redirect' => route('ket-qua-bai-kiem-tra', $maBaiKiemTra)
            ], 422);
        }

        // Kiểm tra thời gian làm bài dựa trên session
        $isTimeUp = false;
        if (session()->has('exam_start_time_' . $maBaiKiemTra)) {
            $thoiGianConLai = $this->getThoiGianConLai($baiKiemTra, $maBaiKiemTra);
            if ($thoiGianConLai <= 0) {
                $isTimeUp = true;
            }
        }

        // Kiểm tra đáp án (trừ trường hợp hết thời gian)
        if (!$isTimeUp) {
            foreach ($baiKiemTra->cauHoiBaiKiemTra as $cauHoi) {
                $dapAn = $request->input("cauhoi_{$cauHoi->MaCauHoi}");
                if ($dapAn === null || !in_array($dapAn, ['A', 'B', 'C', 'D'])) {
                    return response()->json([
                        'message' => 'Vui lòng chọn đáp án cho tất cả câu hỏi!',
                        'redirect' => route('lam-bai-kiem-tra', $maBaiKiemTra)
                    ], 422);
                }
            }
        }

        // Tính điểm
        $tongCauDung = 0;
        $tongSoCauHoi = $baiKiemTra->cauHoiBaiKiemTra->count();
        $chiTietKetQua = [];

        foreach ($baiKiemTra->cauHoiBaiKiemTra as $cauHoi) {
            $dapAnSinhVien = $request->input("cauhoi_{$cauHoi->MaCauHoi}");
            $ketQua = ($dapAnSinhVien && $dapAnSinhVien == $cauHoi->DapAnDung) ? 1 : 0;

            if ($ketQua) {
                $tongCauDung++;
            }

            $chiTietKetQua[] = [
                'MaCauHoi' => $cauHoi->MaCauHoi,
                'DapAnSinhVien' => $dapAnSinhVien,
                'KetQua' => $ketQua
            ];
        }

        // Lưu kết quả bài kiểm tra
        try {
            $ketQuaBaiKiemTra = KetQuaBaiKiemTra::create([
                'MaBaiKiemTra' => $maBaiKiemTra,
                'MaSinhVien' => $sinhVien->MaNguoiDung,
                'TongCauDung' => $tongCauDung,
                'TongSoCauHoi' => $tongSoCauHoi,
                'NgayNop' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            foreach ($chiTietKetQua as $chiTiet) {
                ChiTietKetQua::create([
                    'MaKetQua' => $ketQuaBaiKiemTra->MaKetQua,
                    'MaCauHoi' => $chiTiet['MaCauHoi'],
                    'DapAnSinhVien' => $chiTiet['DapAnSinhVien'],
                    'KetQua' => $chiTiet['KetQua'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }

            session()->forget('exam_start_time_' . $maBaiKiemTra);
            Log::channel('baikiemtra')->info('Sinh viên nộp bài kiểm tra', [
                'user_id' => Auth::id(),
                'ma_bai_kiem_tra' => $maBaiKiemTra,
                'thoi_gian_nop' => now()->toDateTimeString(),
                'ip' => $request->ip(),
                'data' => $request->all(),
            ]);
            return response()->json([
                'message' => $isTimeUp ? 'Thời gian làm bài đã hết! Bài làm của bạn đã được nộp tự động.' : 'Nộp bài kiểm tra thành công!',
                'redirect' => route('ket-qua-bai-kiem-tra', $maBaiKiemTra)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lưu kết quả bài kiểm tra!',
                'redirect' => route('danh-sach-bai-kiem-tra')
            ], 500);
        }
    }

    /**
     * Hiển thị kết quả bài kiểm tra
     */
    public function ketQuaBaiKiemTra($maBaiKiemTra)
    {
        $baiKiemTra = BaiKiemTra::with(['giangVien', 'lopHocPhan.BaiGiang', 'cauHoiBaiKiemTra'])
            ->findOrFail($maBaiKiemTra);

        $sinhVien = SinhVien::where('MaNguoiDung', Auth::id())->first();
        $ketQua = KetQuaBaiKiemTra::with('chiTietKetQua.cauHoi')
            ->where('MaBaiKiemTra', $maBaiKiemTra)
            ->where('MaSinhVien', $sinhVien->MaNguoiDung)
            ->first();

        if (!$ketQua) {
            return redirect()->back()->with('error', 'Bạn chưa làm bài kiểm tra này!');
        }

        if (!$baiKiemTra->ChoPhepXemKetQua) {
            return view('sinhvien.ketquabaikiemtra', [
                'baiKiemTra' => $baiKiemTra,
                'ketQua' => $ketQua,
                'khongChoXemKetQua' => true
            ]);
        }

        return view('sinhvien.ketquabaikiemtra', compact('baiKiemTra', 'ketQua'));
    }

    /**
     * Danh sách bài kiểm tra của sinh viên
     */
    public function danhSachBaiKiemTra(Request $request)
    {
        $sinhVien = SinhVien::where('MaNguoiDung', Auth::id())->first();

        $lopHocPhanIds = $sinhVien->danhSachLop()->pluck('MaLopHocPhan');

        $query = BaiKiemTra::with(['giangVien', 'lopHocPhan.BaiGiang', 'cauHoiBaiKiemTra'])
            ->whereIn('MaLopHocPhan', $lopHocPhanIds);

        // Lọc theo trạng thái
        $trangThai = $request->input('trang_thai');
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        switch ($trangThai) {
            case 'sap_dien_ra':
                $query->where('ThoiGianBatDau', '>', $now);
                break;
            case 'dang_dien_ra':
                $query->where('ThoiGianBatDau', '<=', $now)
                    ->where('ThoiGianKetThuc', '>=', $now);
                break;
            case 'da_ket_thuc':
                $query->where('ThoiGianKetThuc', '<', $now);
                break;
            case 'da_lam':
                $query->whereHas('ketQuaBaiKiemTra', function ($q) use ($sinhVien) {
                    $q->where('MaSinhVien', $sinhVien->MaNguoiDung);
                });
                break;
        }

        $baiKiemTra = $query->orderBy('ThoiGianBatDau', 'desc')->get();

        foreach ($baiKiemTra as $bai) {
            $bai->daLam = KetQuaBaiKiemTra::where('MaBaiKiemTra', $bai->MaBaiKiemTra)
                ->where('MaSinhVien', $sinhVien->MaNguoiDung)
                ->exists();

            $thoiGianBatDau = Carbon::parse($bai->ThoiGianBatDau, 'Asia/Ho_Chi_Minh');
            $thoiGianKetThuc = Carbon::parse($bai->ThoiGianKetThuc, 'Asia/Ho_Chi_Minh');

            if ($thoiGianBatDau > $now) {
                $bai->trangThai = 'Sắp diễn ra';
            } elseif ($thoiGianBatDau <= $now && $thoiGianKetThuc >= $now) {
                $bai->trangThai = 'Đang diễn ra';
            } else {
                $bai->trangThai = 'Đã kết thúc';
            }
        }

        return view('sinhvien.danhsachbaikiemtra', compact('baiKiemTra'));
    }


    /**
     * Tính thời gian còn lại (giây) cho sinh viên làm bài
     */
    private function getThoiGianConLai($baiKiemTra, $maBaiKiemTra)
    {
        $now = Carbon::now();
        $thoiGianKetThuc = Carbon::parse($baiKiemTra->ThoiGianKetThuc);
        if (!session()->has('exam_start_time_' . $maBaiKiemTra)) {
            session(['exam_start_time_' . $maBaiKiemTra => $now->timestamp]);
        }
        // lấy thời gian bắt đầu làm bài dựa vào seesion
        $thoiGianBatDauLamBai = Carbon::createFromTimestamp(session('exam_start_time_' . $maBaiKiemTra));
        // Thời gian kết thúc làm bài bằng thời gian bắt đầu + thời gian làm bài kiểm tra
        $thoiGianKetThucLamBai = $thoiGianBatDauLamBai->copy()->addMinutes($baiKiemTra->ThoiGianLamBai);
        // Thời gian bài làm còn lại = thời gian 
        $thoiGianConLaiLamBai = $now->diffInSeconds($thoiGianKetThucLamBai, false);
        $thoiGianConLaiKetThuc = $now->diffInSeconds($thoiGianKetThuc, false);

        //thời gian còn lại 
        return min($thoiGianConLaiLamBai, $thoiGianConLaiKetThuc);
    }
}
