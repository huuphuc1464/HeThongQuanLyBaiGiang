<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\HocPhan;
use App\Models\MonHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HocPhanController extends Controller
{
    public function danhSach(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = HocPhan::with(['monHoc', 'nguoiTao'])->withCount('baiGiang')
            ->where('MaNguoiTao', Auth::id());

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('TenHocPhan', 'like', '%' . $request->search . '%')
                  ->orWhere('MoTa', 'like', '%' . $request->search . '%')
                  ->orWhereHas('monHoc', function($q) use ($request) {
                      $q->where('TenMonHoc', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $danhSachHocPhan = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $danhSachMonHoc = MonHoc::where('TrangThai', 1)->get();

        return view('giangvien.quanLyHocPhan', compact('danhSachHocPhan', 'danhSachMonHoc'));
    }

    public function themMoi(Request $request)
    {
        try {
            \Log::info('Bắt đầu thêm học phần', $request->all());
            
            $request->validate([
                'TenHocPhan' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('hoc_phan')->where(function ($query) use ($request) {
                        return $query->where('MaMonHoc', $request->MaMonHoc)
                                     ->where('TrangThai', 1);
                    })
                ],
                'MaMonHoc' => 'required|exists:mon_hoc,MaMonHoc',
                'MoTa' => 'nullable|string|max:255',
                'AnhHocPhan' => 'nullable|image|mimes:jpeg,png,jpg,gif'
            ], [
                'TenHocPhan.unique' => 'Tên học phần đã tồn tại trong môn học này.'
            ]);

            $data = [
                'TenHocPhan' => $request->TenHocPhan,
                'MaMonHoc' => $request->MaMonHoc,
                'MoTa' => $request->MoTa,
                'MaNguoiTao' => Auth::id(),
                'TrangThai' => 1
            ];

            \Log::info('Dữ liệu học phần', $data);

            // Xử lý upload hình ảnh
            if ($request->hasFile('AnhHocPhan')) {
                $path = $request->file('AnhHocPhan')->store('hocphan', 'public');
                $data['AnhHocPhan'] = $path;
                \Log::info('Đã upload hình ảnh', ['path' => $path]);
            }

            $hocPhan = HocPhan::create($data);
            \Log::info('Đã tạo học phần thành công', ['MaHocPhan' => $hocPhan->MaHocPhan]);

            return redirect()->back()->with('success', 'Thêm học phần thành công!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Lỗi validation khi thêm học phần', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Lỗi khi thêm học phần', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm học phần: ' . $e->getMessage())->withInput();
        }
    }

    public function chiTiet($id)
    {
        $hocPhan = HocPhan::with(['monHoc', 'nguoiTao', 'baiGiang'])
            ->where('MaHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();

        return response()->json($hocPhan);
    }

    public function chinhSua($id)
    {
        $hocPhan = HocPhan::with(['monHoc'])
            ->where('MaHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();

        return response()->json($hocPhan);
    }

    public function capNhat(Request $request, $id)
    {
        $hocPhan = HocPhan::where('MaHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();

        $request->validate([
            'TenHocPhan' => [
                'required',
                'string',
                'max:100',
                Rule::unique('hoc_phan')->where(function ($query) use ($request) {
                    return $query->where('MaMonHoc', $request->MaMonHoc)
                                 ->where('TrangThai', 1);
                })->ignore($hocPhan->MaHocPhan, 'MaHocPhan')
            ],
            'MaMonHoc' => 'required|exists:mon_hoc,MaMonHoc',
            'MoTa' => 'nullable|string|max:255',
            'AnhHocPhan' => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ], [
            'TenHocPhan.unique' => 'Tên học phần đã tồn tại trong môn học này.'
        ]);

        $data = [
            'TenHocPhan' => $request->TenHocPhan,
            'MaMonHoc' => $request->MaMonHoc,
            'MoTa' => $request->MoTa
        ];

        // Xử lý upload hình ảnh mới
        if ($request->hasFile('AnhHocPhan')) {
            // Xóa hình ảnh cũ nếu có
            if ($hocPhan->AnhHocPhan) {
                Storage::disk('public')->delete($hocPhan->AnhHocPhan);
            }

            $path = $request->file('AnhHocPhan')->store('hocphan', 'public');
            $data['AnhHocPhan'] = $path;
        }

        $hocPhan->update($data);

        return redirect()->back()->with('success', 'Cập nhật học phần thành công!');
    }

    public function xoa($id)
    {
        $hocPhan = HocPhan::with('baiGiang')
            ->where('MaHocPhan', $id)
            ->where('MaNguoiTao', Auth::id())
            ->firstOrFail();

        // Kiểm tra xem học phần có bài giảng không
        if ($hocPhan->baiGiang->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa học phần này vì đã có bài giảng liên quan!');
        }

        // Xóa hình ảnh nếu có
        if ($hocPhan->AnhHocPhan) {
            Storage::disk('public')->delete($hocPhan->AnhHocPhan);
        }

        $hocPhan->delete();

        return redirect()->back()->with('success', 'Xóa học phần thành công!');
    }

    public function layDanhSachMonHoc()
    {
        $danhSachMonHoc = MonHoc::where('TrangThai', 1)->get();
        return response()->json($danhSachMonHoc);
    }
}
