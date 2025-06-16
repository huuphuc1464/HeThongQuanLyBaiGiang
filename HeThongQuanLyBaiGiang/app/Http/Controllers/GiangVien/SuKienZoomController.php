<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\SuKienZoom;
use App\Services\Zoom;
use App\Services\ZoomService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuKienZoomController extends Controller
{
    protected $zoom;

    public function __construct(ZoomService $zoom)
    {
        $this->zoom = $zoom;
    }

    public function danhSachSuKien()
    {
        $danhSachSuKien = DB::table('su_kien_zoom')
            ->join('lop_hoc_phan', 'su_kien_zoom.MaLopHocPhan', '=', 'lop_hoc_phan.MaLopHocPhan')
            ->select('su_kien_zoom.*', 'lop_hoc_phan.TenLopHocPhan')
            ->paginate(10);
        return view('giangvien.danhSachSuKienZoom', compact('danhSachSuKien'));
    }

    public function hienFormThemZoom()
    {
        $lopHocPhan = DB::table('lop_hoc_phan')
            ->select('MaLopHocPhan', 'TenLopHocPhan')
            ->where('MaNguoiTao', Auth::id())
            ->where('TrangThai', 1)
            ->get();
        return view('giangvien.themSuKienZoom', compact('lopHocPhan'));
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
            'MatKhauSuKien' => 'required|string|min:6|max:100',
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
            'MatKhauSuKien.max' => 'Mật khẩu không được vượt quá 100 ký tự.',
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
                'LinkThamGiaSuKien' => $zoomResponse['join_url'],
                'LinkBatDauSuKien' => $zoomResponse['start_url'],
            ]);

            return redirect()->route('giangvien.su-kien-zoom.danhsach')->with('success', 'Tạo sự kiện Zoom thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('errorSystem', 'Lỗi khi tạo Zoom: ' . $e->getMessage());
        }
    }

    public function xoaSuKienZoom($id)
    {
        $suKien = SuKienZoom::findOrFail($id);
        try {
            $this->zoom->xoaSuKienZoom($this->layZoomId($suKien->LinkThamGiaSuKien));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('errorSystem', 'Lỗi khi xóa sự kiện Zoom: ' . $e->getMessage());
        }

        $suKien->delete();

        return redirect()->back()->with('success', 'Xóa sự kiện Zoom thành công.');
    }

    function layZoomId($zoomLink)
    {
        if (preg_match('/zoom\.us\/[jw]\/([0-9]+)/', $zoomLink, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
