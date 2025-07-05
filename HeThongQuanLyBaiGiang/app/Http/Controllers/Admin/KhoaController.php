<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhoaController extends Controller
{
    //Relevance-based Search with Field Weighting (tìm kiếm có sắp xếp theo ưu tiên)
    public function danhSach(Request $request)
    {
        $search = $request->input('search');

        $query = Khoa::where('TrangThai', 1);

        if ($search) {
            $search = trim($search);

            $query->select('*', DB::raw('
                CASE 
                    WHEN LOWER(TenKhoa) = LOWER("' . $search . '") THEN 3
                    WHEN LOWER(TenKhoa) LIKE LOWER("%' . $search . '%") THEN 2
                    WHEN LOWER(MoTa) LIKE LOWER("%' . $search . '%") THEN 1
                    ELSE 0
                END as relevance_score
            '))
                ->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(TenKhoa) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(MoTa) LIKE ?', ["%$search%"]);
                })
                ->orderBy('relevance_score', 'desc')
                ->orderBy('TenKhoa');
        } else {
            $query->orderBy('MaKhoa')
                ->orderBy('TenKhoa');
        }

        $danhSachKhoa = $query->paginate(10)->withQueryString();

        return view('admin.quanLyKhoa', compact('danhSachKhoa', 'search'));
    }

    public function themMoi(Request $request)
    {
        $request->validate([
            'TenKhoa' => 'required|string|max:255|unique:khoa,TenKhoa,NULL,MaKhoa,TrangThai,1',
            'MoTa' => 'nullable|string'
        ], [
            'TenKhoa.unique' => 'Tên khoa đã tồn tại trong hệ thống.'
        ]);

        Khoa::create([
            'TenKhoa' => $request->TenKhoa,
            'MoTa' => $request->MoTa,
            'TrangThai' => 1
        ]);

        return redirect()->route('admin.khoa.danh-sach')
            ->with('success', 'Thêm khoa thành công');
    }

    public function capNhat(Request $request, Khoa $khoa)
    {
        $request->validate([
            'TenKhoa' => 'required|string|max:255|unique:khoa,TenKhoa,' . $khoa->MaKhoa . ',MaKhoa,TrangThai,1',
            'MoTa' => 'nullable|string'
        ], [
            'TenKhoa.unique' => 'Tên khoa đã tồn tại trong hệ thống.'
        ]);

        $khoa->update([
            'TenKhoa' => $request->TenKhoa,
            'MoTa' => $request->MoTa,
        ]);

        return redirect()->route('admin.khoa.danh-sach')
            ->with('success', 'Cập nhật khoa thành công');
    }

    public function xoa(Khoa $khoa)
    {
        // Kiểm tra xem khoa bài giảng nào không
        if ($khoa->baiGiangs()->where('TrangThai', 1)->count() > 0) {
            return redirect()->route('admin.khoa.danh-sach')
                ->with('error', 'Không thể xóa khoa này vì đang có bài giảng thuộc khoa');
        }

        $khoa->update(['TrangThai' => 0]);

        return redirect()->route('admin.khoa.danh-sach')
            ->with('success', 'Xóa khoa thành công');
    }
}
