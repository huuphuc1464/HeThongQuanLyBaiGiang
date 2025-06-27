<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\HocPhan;
use App\Models\MonHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
            $query->where(function ($q) use ($request) {
                $q->where('TenHocPhan', 'like', '%' . $request->search . '%')
                    ->orWhere('MoTa', 'like', '%' . $request->search . '%')
                    ->orWhereHas('monHoc', function ($q) use ($request) {
                        $q->where('TenMonHoc', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $danhSachHocPhan = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $danhSachMonHoc = MonHoc::where('TrangThai', 1)->get();

        return view('giangvien.quanLyHocPhan.quanLyHocPhan', compact('danhSachHocPhan', 'danhSachMonHoc'));
    }

    public function themMoi(Request $request)
    {
        try {
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

            // Xử lý upload hình ảnh
            if ($request->hasFile('AnhHocPhan')) {
                $image = $request->file('AnhHocPhan');
                $imageName = time() . '-' . uniqid() . '.' . $image->extension();
                $image->move(public_path('img/hocphan'), $imageName);
                $data['AnhHocPhan'] = 'hocphan/' . $imageName;
            }

            $hocPhan = HocPhan::create($data);

            return redirect()->back()->with('success', 'Thêm học phần thành công!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm học phần.')->withInput();
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
                $oldImagePath = public_path('img/' . $hocPhan->AnhHocPhan);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $image = $request->file('AnhHocPhan');
            $imageName = time() . '-' . uniqid() . '.' . $image->extension();
            $image->move(public_path('img/hocphan'), $imageName);
            $data['AnhHocPhan'] = 'hocphan/' . $imageName;
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
            $imagePath = public_path('img/' . $hocPhan->AnhHocPhan);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
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
