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

class KetQuaBaiKiemTraController extends Controller
{
    /**
     * Hiển thị form làm bài kiểm tra
     */
    public function lamBaiKiemTra($maBaiKiemTra)
    {
        $baiKiemTra = BaiKiemTra::with(['giangVien', 'lopHocPhan.hocPhan', 'cauHoiBaiKiemTra'])
            ->findOrFail($maBaiKiemTra);

        // Kiểm tra thời gian làm bài
        $now = Carbon::now();
        $thoiGianBatDau = Carbon::parse($baiKiemTra->ThoiGianBatDau);
        $thoiGianKetThuc = Carbon::parse($baiKiemTra->ThoiGianKetThuc);

        // Kiểm tra sinh viên đã làm bài chưa
        $sinhVien = SinhVien::where('MaNguoiDung', Auth::id())->first();
        $daLamBai = KetQuaBaiKiemTra::where('MaBaiKiemTra', $maBaiKiemTra)
            ->where('MaSinhVien', $sinhVien->MaNguoiDung)
            ->exists();

        if ($daLamBai) {
            return redirect()->route('ket-qua-bai-kiem-tra', $maBaiKiemTra)
                ->with('error', 'Bạn đã làm bài kiểm tra này rồi!');
        }

        if ($now < $thoiGianBatDau) {
            return redirect()->back()->with('error', 'Bài kiểm tra chưa bắt đầu!');
        }

        if ($now > $thoiGianKetThuc) {
            return redirect()->back()->with('error', 'Bài kiểm tra đã kết thúc!');
        }

        // Tính thời gian còn lại
        $thoiGianConLai = $thoiGianKetThuc->diffInSeconds($now);

        return view('sinhvien.lambaikiemtra', compact('baiKiemTra', 'thoiGianConLai'));
    }

    /**
     * Xử lý nộp bài kiểm tra
     */
    public function nopBaiKiemTra(Request $request, $maBaiKiemTra)
    {
        $baiKiemTra = BaiKiemTra::with('cauHoiBaiKiemTra')->findOrFail($maBaiKiemTra);

        // Kiểm tra thời gian
        $now = Carbon::now();
        $thoiGianKetThuc = Carbon::parse($baiKiemTra->ThoiGianKetThuc);

        if ($now > $thoiGianKetThuc) {
            return redirect()->back()->with('error', 'Thời gian làm bài đã hết!');
        }

        // Kiểm tra sinh viên đã làm bài chưa
        $sinhVien = SinhVien::where('MaNguoiDung', Auth::id())->first();
        $daLamBai = KetQuaBaiKiemTra::where('MaBaiKiemTra', $maBaiKiemTra)
            ->where('MaSinhVien', $sinhVien->MaNguoiDung)
            ->exists();

        if ($daLamBai) {
            return redirect()->route('ket-qua-bai-kiem-tra', $maBaiKiemTra)
                ->with('error', 'Bạn đã làm bài kiểm tra này rồi!');
        }

        // Tính điểm
        $tongCauDung = 0;
        $tongSoCauHoi = $baiKiemTra->cauHoiBaiKiemTra->count();
        $chiTietKetQua = [];

        foreach ($baiKiemTra->cauHoiBaiKiemTra as $cauHoi) {
            $dapAnSinhVien = $request->input("cauhoi_{$cauHoi->MaCauHoi}");
            $ketQua = ($dapAnSinhVien == $cauHoi->DapAnDung) ? 1 : 0;

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
        $ketQuaBaiKiemTra = KetQuaBaiKiemTra::create([
            'MaBaiKiemTra' => $maBaiKiemTra,
            'MaSinhVien' => $sinhVien->MaNguoiDung,
            'TongCauDung' => $tongCauDung,
            'TongSoCauHoi' => $tongSoCauHoi,
            'NgayNop' => $now
        ]);

        // Lưu chi tiết kết quả
        foreach ($chiTietKetQua as $chiTiet) {
            ChiTietKetQua::create([
                'MaKetQua' => $ketQuaBaiKiemTra->MaKetQua,
                'MaCauHoi' => $chiTiet['MaCauHoi'],
                'DapAnSinhVien' => $chiTiet['DapAnSinhVien'],
                'KetQua' => $chiTiet['KetQua']
            ]);
        }

        return redirect()->route('ket-qua-bai-kiem-tra', $maBaiKiemTra)
            ->with('success', 'Nộp bài thành công!');
    }

    /**
     * Hiển thị kết quả bài kiểm tra
     */
    public function ketQuaBaiKiemTra($maBaiKiemTra)
    {
        $baiKiemTra = BaiKiemTra::with(['giangVien', 'lopHocPhan.hocPhan', 'cauHoiBaiKiemTra'])
            ->findOrFail($maBaiKiemTra);

        $sinhVien = SinhVien::where('MaNguoiDung', Auth::id())->first();
        $ketQua = KetQuaBaiKiemTra::with('chiTietKetQua.cauHoi')
            ->where('MaBaiKiemTra', $maBaiKiemTra)
            ->where('MaSinhVien', $sinhVien->MaNguoiDung)
            ->first();

        if (!$ketQua) {
            return redirect()->back()->with('error', 'Bạn chưa làm bài kiểm tra này!');
        }

        return view('sinhvien.ketquabaikiemtra', compact('baiKiemTra', 'ketQua'));
    }

    /**
     * Danh sách bài kiểm tra của sinh viên
     */
    public function danhSachBaiKiemTra()
    {
        $sinhVien = SinhVien::where('MaNguoiDung', Auth::id())->first();

        // Lấy các lớp học phần mà sinh viên tham gia
        $lopHocPhanIds = $sinhVien->danhSachLop()->pluck('MaLopHocPhan');

        // Lấy bài kiểm tra từ các lớp học phần
        $baiKiemTra = BaiKiemTra::with(['giangVien', 'lopHocPhan.hocPhan'])
            ->whereIn('MaLopHocPhan', $lopHocPhanIds)
            ->orderBy('ThoiGianBatDau', 'desc')
            ->get();

        // Kiểm tra sinh viên đã làm bài nào
        foreach ($baiKiemTra as $bai) {
            $bai->daLam = KetQuaBaiKiemTra::where('MaBaiKiemTra', $bai->MaBaiKiemTra)
                ->where('MaSinhVien', $sinhVien->MaNguoiDung)
                ->exists();
        }

        return view('sinhvien.danhsachbaikiemtra', compact('baiKiemTra'));
    }
}
