<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\SuKienZoom;
use App\Services\EmailService;
use App\Services\Zoom;
use App\Services\ZoomService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuKienZoomController extends Controller
{
    protected $zoom;
    protected $email;

    public function __construct(ZoomService $zoom, EmailService $email)
    {
        $this->zoom = $zoom;
        $this->email = $email;
    }

    public function danhSachSuKien(Request $request)
    {
        $query = DB::table('su_kien_zoom')
            ->join('lop_hoc_phan', 'su_kien_zoom.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->select('su_kien_zoom.*', 'lop_hoc_phan.TenLopHocPhan')
            ->where('su_kien_zoom.MaGiangVien', Auth::id());

        if ($request->filled('search')) {
            $keywords = preg_split('/\s+/', trim($request->search));

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $q->orWhereRaw('LOWER(su_kien_zoom.TenSuKien) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(su_kien_zoom.MoTa) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(su_kien_zoom.LinkSuKien) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(lop_hoc_phan.TenLopHocPhan) LIKE ?', ["%$kw%"]);
                }
            });
        }

        $query->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, NOW(), su_kien_zoom.ThoiGianBatDau))');

        $danhSachSuKien = $query->paginate(10)->withQueryString();

        return view('giangvien.quanLySuKienZoom.danhSachSuKienZoom', compact('danhSachSuKien'));
    }

    public function hienFormThemZoom()
    {
        $lopHocPhan = DB::table('lop_hoc_phan')
            ->select('MaLopHocPhan', 'TenLopHocPhan')
            ->where('MaNguoiTao', Auth::id())
            ->where('TrangThai', 1)
            ->get();
        return view('giangvien.quanLySuKienZoom.themSuKienZoom', compact('lopHocPhan'));
    }

    public function hienFormCapNhatZoom($id)
    {
        $suKienZoom = DB::table('su_kien_zoom')
            ->join('lop_hoc_phan', 'su_kien_zoom.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->select('lop_hoc_phan.TenLopHocPhan', 'su_kien_zoom.*')
            ->where('su_kien_zoom.MaSuKienZoom', $id)
            ->where('su_kien_zoom.MaGiangVien', Auth::id())
            ->first();
        return view('giangvien.quanLySuKienZoom.suaSuKienZoom', compact('suKienZoom'));
    }

    public function chiTietSuKien($id)
    {
        $suKienZoom = DB::table('su_kien_zoom')
            ->join('lop_hoc_phan', 'su_kien_zoom.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->select('lop_hoc_phan.TenLopHocPhan', 'su_kien_zoom.*')
            ->where('su_kien_zoom.MaSuKienZoom', $id)
            ->where('su_kien_zoom.MaGiangVien', Auth::id())
            ->first();
        return view('giangvien.quanLySuKienZoom.chiTietSuKienZoom', compact('suKienZoom'));
    }

    public function themSuKienZoom(Request $request)
    {
        $validated = $request->validate([
            'MaLopHocPhan' => 'required|exists:lop_hoc_phan,MaLopHocPhan',
            'TenSuKien' => 'required|string|max:100',
            'MoTa' => 'nullable|string|max:255',
            'ThoiGianBatDau' => [
                'required',
                'date_format:Y-m-d\TH:i',
                'after_or_equal:' . now()->format('Y-m-d\TH:i'),
            ],
            'ThoiGianKetThuc' => [
                'required',
                'date_format:Y-m-d\TH:i',
                'after:ThoiGianBatDau',
            ],
            'MatKhauSuKien' => 'required|string|min:6|max:10',
        ], [
            'MaLopHocPhan.required' => 'Vui lòng chọn lớp học phần.',
            'MaLopHocPhan.exists' => 'Lớp học phần không tồn tại.',
            'TenSuKien.required' => 'Vui lòng nhập tên sự kiện.',
            'TenSuKien.max' => 'Tên sự kiện không được vượt quá 100 ký tự.',
            'MoTa.max' => 'Mô tả sự kiện không được vượt quá 255 ký tự.',
            'ThoiGianBatDau.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'ThoiGianBatDau.date_format' => 'Thời gian bắt đầu không đúng định dạng.',
            'ThoiGianBatDau.after_or_equal' => 'Thời gian bắt đầu phải từ thời điểm hiện tại trở đi.',
            'ThoiGianKetThuc.required' => 'Vui lòng chọn thời gian kết thúc.',
            'ThoiGianKetThuc.date_format' => 'Thời gian kết thúc không đúng định dạng.',
            'ThoiGianKetThuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'MatKhauSuKien.required' => 'Vui lòng nhập mật khẩu sự kiện.',
            'MatKhauSuKien.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'MatKhauSuKien.max' => 'Mật khẩu không được vượt quá 10 ký tự.',
        ]);


        $startTime = Carbon::parse($validated['ThoiGianBatDau']);
        $endTime = Carbon::parse($validated['ThoiGianKetThuc']);
        $duration = $startTime->diffInMinutes($endTime);

        $zoomData = [
            'topic' => $validated['TenSuKien'],
            'start_time' => $startTime->toIso8601String(),
            'duration' => $duration,
            'password' => $validated['MatKhauSuKien'],
            'agenda' => $validated['MoTa'],
        ];

        try {
            $zoomResponse = $this->zoom->taoSuKienZoom($zoomData);

            $suKien = SuKienZoom::create([
                'MaLopHocPhan' => $validated['MaLopHocPhan'],
                'MaGiangVien' => Auth::id(),
                'TenSuKien' => $validated['TenSuKien'],
                'MoTa' => $validated['MoTa'],
                'ThoiGianBatDau' => $validated['ThoiGianBatDau'],
                'ThoiGianKetThuc' => $validated['ThoiGianKetThuc'],
                'MatKhauSuKien' => $validated['MatKhauSuKien'],
                'LinkSuKien' => $zoomResponse['join_url'],
                'KhoaChuTri' => env('ZOOM_HOST_KEY')
            ]);

            $this->luuThongBaoVaGuiEmail(
                $suKien->MaLopHocPhan,
                Auth::id(),
                $suKien,
                'them'
            );

            return redirect()->route('giangvien.su-kien-zoom.danhsach')->with('success', 'Tạo sự kiện Zoom thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('errorSystem', 'Lỗi khi tạo Zoom: ' . $e->getMessage());
        }
    }

    public function xoaSuKienZoom($id)
    {
        $suKien = SuKienZoom::findOrFail($id);
        try {
            $this->zoom->xoaSuKienZoom($this->layZoomId($suKien->LinkSuKien));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('errorSystem', 'Lỗi khi xóa sự kiện Zoom: ' . $e->getMessage());
        }
        $copySuKien = clone $suKien;

        $suKien->delete();

        $this->luuThongBaoVaGuiEmail(
            $copySuKien->MaLopHocPhan,
            Auth::id(),
            $copySuKien,
            'xoa'
        );
        return redirect()->back()->with('success', 'Xóa sự kiện Zoom thành công.');
    }

    public function capNhatSuKienZoom(Request $request, $id)
    {
        $suKien = SuKienZoom::findOrFail($id);

        $validated = $request->validate([
            'TenSuKien' => 'required|string|max:100',
            'MoTa' => 'nullable|string|max:255',
            'ThoiGianBatDau' => [
                'required',
                'date_format:Y-m-d\TH:i',
                'after_or_equal:' . now()->format('Y-m-d\TH:i'),
            ],
            'ThoiGianKetThuc' => [
                'required',
                'date_format:Y-m-d\TH:i',
                'after:ThoiGianBatDau',
            ],
            'MatKhauSuKien' => 'required|string|min:6|max:10',
        ], [
            'TenSuKien.required' => 'Vui lòng nhập tên sự kiện.',
            'TenSuKien.max' => 'Tên sự kiện không được vượt quá 100 ký tự.',
            'MoTa.max' => 'Mô tả sự kiện không được vượt quá 255 ký tự.',
            'ThoiGianBatDau.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'ThoiGianBatDau.date_format' => 'Thời gian bắt đầu không đúng định dạng.',
            'ThoiGianBatDau.after_or_equal' => 'Thời gian bắt đầu phải từ thời điểm hiện tại trở đi.',
            'ThoiGianKetThuc.required' => 'Vui lòng chọn thời gian kết thúc.',
            'ThoiGianKetThuc.date_format' => 'Thời gian kết thúc không đúng định dạng.',
            'ThoiGianKetThuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'MatKhauSuKien.required' => 'Vui lòng nhập mật khẩu sự kiện.',
            'MatKhauSuKien.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'MatKhauSuKien.max' => 'Mật khẩu không được vượt quá 10 ký tự.',
        ]);

        try {
            $this->zoom->capNhatSuKienZoom($this->layZoomId($suKien->LinkSuKien), [
                'topic' => $validated['TenSuKien'],
                'start_time' => Carbon::parse($validated['ThoiGianBatDau'])->toIso8601String(),
                'duration' => Carbon::parse($validated['ThoiGianBatDau'])->diffInMinutes(Carbon::parse($validated['ThoiGianKetThuc'])),
                'agenda' => $validated['MoTa'],
                'password' => $validated['MatKhauSuKien']
            ]);
        } catch (\Exception $e) {
            return back()->with('errorSystem', 'Lỗi khi cập nhật sự kiện Zoom: ' . $e->getMessage());
        }

        $suKien->update($validated);
        $this->luuThongBaoVaGuiEmail(
            $suKien->MaLopHocPhan,
            Auth::id(),
            $suKien,
            'sua'
        );
        return redirect()->route('giangvien.su-kien-zoom.danhsach')->with('success', 'Cập nhật sự kiện Zoom thành công.');
    }

    public function luuThongBaoVaGuiEmail($maLopHocPhan, $maNguoiTao, $suKien, $hanhDong = 'them')
    {
        try {
            $lopHocPhan = DB::table('lop_hoc_phan')->where('MaLopHocPhan', $maLopHocPhan)->first();

            $danhSachThongTin = DB::table('danh_sach_lop')
                ->join('nguoi_dung', 'danh_sach_lop.MaSinhVien', '=', 'nguoi_dung.MaNguoiDung')
                ->where('danh_sach_lop.MaLopHocPhan', $maLopHocPhan)
                ->select('nguoi_dung.MaNguoiDung', 'nguoi_dung.HoTen', 'nguoi_dung.Email')
                ->get();

            $giangVien = DB::table('nguoi_dung')
                ->where('MaNguoiDung', $maNguoiTao)
                ->select('MaNguoiDung', 'HoTen', 'Email')
                ->first();

            if ($giangVien) {
                $danhSachThongTin->push($giangVien);
            }

            $start = \Carbon\Carbon::parse($suKien->ThoiGianBatDau)->format('H:i:s d/m/Y');
            $end = \Carbon\Carbon::parse($suKien->ThoiGianKetThuc)->format('H:i:s d/m/Y');

            switch ($hanhDong) {
                case 'them':
                    $noiDungThongBao = "Một sự kiện Zoom mới đã được thêm vào lớp {$lopHocPhan->TenLopHocPhan}.";
                    $tieuDeEmail = "🔔 Thêm sự kiện Zoom mới";
                    break;
                case 'sua':
                    $noiDungThongBao = "Sự kiện Zoom trong lớp {$lopHocPhan->TenLopHocPhan} đã được cập nhật.";
                    $tieuDeEmail = "🔄 Cập nhật sự kiện Zoom";
                    break;
                case 'xoa':
                    $noiDungThongBao = "Một sự kiện Zoom trong lớp {$lopHocPhan->TenLopHocPhan} đã bị xóa.";
                    $tieuDeEmail = "❌ Xóa sự kiện Zoom";
                    break;
                default:
                    $noiDungThongBao = "Có thay đổi liên quan đến sự kiện Zoom trong lớp {$lopHocPhan->TenLopHocPhan}.";
                    $tieuDeEmail = "🔔 Thông báo sự kiện Zoom";
                    break;
            }

            DB::table('thong_bao')->insert([
                'MaLopHocPhan' => $maLopHocPhan,
                'MaNguoiTao' => $maNguoiTao,
                'NoiDung' => $noiDungThongBao,
                'ThoiGianTao' => now(),
            ]);

            foreach ($danhSachThongTin as $nd) {
                $studentName = $nd->HoTen;
                $email = $nd->Email;
                $isTeacher = ($nd->MaNguoiDung == $maNguoiTao);

                $body = "Chào {$studentName},<br><br>";
                $body .= "{$noiDungThongBao}<br><br>";
                $body .= "📄 Tên sự kiện: {$suKien->TenSuKien}<br>";
                $body .= "📄 Nội dung sự kiện: {$suKien->MoTa}<br>";
                $body .= "🔗 Link tham gia: {$suKien->LinkSuKien}<br>";
                $body .= "⌚ Bắt đầu: {$start}<br>";
                $body .= "⏳ Kết thúc: {$end}<br>";
                $body .= "🔑 Mật khẩu: {$suKien->MatKhauSuKien}<br>";

                if ($isTeacher && !empty($suKien->KhoaChuTri)) {
                    $body .= "🔑 Mã Host Key: {$suKien->KhoaChuTri}<br>";
                    $body .= "Lưu ý: không chia sẻ mã này với bất kỳ ai.<br>";
                }

                $body .= "<br>Trân trọng,<br>Hệ thống quản lý bài giảng trực tuyến.";

                try {
                    $this->email->sendEmail($email, $tieuDeEmail, $body);
                } catch (\Throwable $e) {
                    Log::error("Không thể gửi email đến {$email}: " . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::error('Lỗi xử lý thông báo và email sự kiện Zoom: ' . $e->getMessage());
        }
    }

    function layZoomId($zoomLink)
    {
        if (preg_match('/zoom\.us\/[jw]\/([0-9]+)/', $zoomLink, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
