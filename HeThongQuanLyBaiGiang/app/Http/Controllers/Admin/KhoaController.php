<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khoa;
use Illuminate\Http\Request;

class KhoaController extends Controller
{
    public function danhSach(Request $request)
    {
        $search = $request->input('search');
        
        $query = Khoa::query();
        
        if ($search) {
            $query->where('TenKhoa', 'like', '%' . $search . '%');
        }
        
        $danhSachKhoa = $query->paginate(10);
        
        return view('admin.quanLyKhoa', compact('danhSachKhoa', 'search'));
    }

    public function themMoi(Request $request)
    {
        $request->validate([
            'TenKhoa' => 'required|string|max:255',
            'MoTa' => 'nullable|string'
        ]);

        Khoa::create([
            'TenKhoa' => $request->TenKhoa,
            'MoTa' => $request->MoTa,
            'TrangThai' => 1
        ]);

        return redirect()->route('admin.quan-ly-khoa.danh-sach')
            ->with('success', 'Thêm khoa thành công');
    }

    public function capNhat(Request $request, Khoa $khoa)
    {
        $request->validate([
            'TenKhoa' => 'required|string|max:255',
            'MoTa' => 'nullable|string'
        ]);

        $khoa->update([
            'TenKhoa' => $request->TenKhoa,
            'MoTa' => $request->MoTa
        ]);

        return redirect()->route('admin.quan-ly-khoa.danh-sach')
            ->with('success', 'Cập nhật khoa thành công');
    }

    public function xoa(Khoa $khoa)
    {
        // Kiểm tra xem khoa có môn học nào không
        if ($khoa->monHocs()->count() > 0) {
            return redirect()->route('admin.quan-ly-khoa.danh-sach')
                ->with('error', 'Không thể xóa khoa này vì đang có môn học thuộc khoa');
        }

        $khoa->delete();

        return redirect()->route('admin.quan-ly-khoa.danh-sach')
            ->with('success', 'Xóa khoa thành công');
    }
} 