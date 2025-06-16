<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonHoc;
use App\Models\Khoa;

class MonHocController extends Controller
{
    public function danhSach(Request $request) {
        $search = $request->input('search');
        $filterKhoa = $request->input('filterKhoa');
        
        $query = MonHoc::where('TrangThai', 1)
                       ->with('khoa'); // Eager load relationship with khoa
        
        if ($search) {
            $query->where('TenMonHoc', 'like', '%' . $search . '%');
        }
        
        if ($filterKhoa) {
            $query->where('MaKhoa', $filterKhoa);
        }
        
        $danhSachMonHoc = $query->paginate(10);
        $danhSachKhoa = Khoa::where('TrangThai', 1)->get();
        
        return view('admin.quanLyMonHoc', compact('danhSachMonHoc', 'danhSachKhoa', 'search', 'filterKhoa'));
    }

    public function themMoi(Request $request)
    {
        $request->validate([
            'TenMonHoc' => 'required|string|max:255|unique:monhoc,TenMonHoc,NULL,MaMonHoc,TrangThai,1',
            'MaKhoa' => 'required|exists:khoa,MaKhoa',
            'MoTa' => 'nullable|string'
        ],[
            'TenMonHoc.unique' => 'Tên môn học đã tồn tại trong hệ thống.',
            'MaKhoa.exists' => 'Khoa không tồn tại trong hệ thống.'
        ]);

        MonHoc::create([
            'TenMonHoc' => $request->TenMonHoc,
            'MaKhoa' => $request->MaKhoa,
            'MoTa' => $request->MoTa,
            'TrangThai' => 1
        ]);

        return redirect()->route('admin.quan-ly-mon-hoc.danh-sach')
            ->with('success', 'Thêm môn học thành công');
    }

    public function capNhat(Request $request, MonHoc $monHoc)
    {
        $request->validate([
            'TenMonHoc' => 'required|string|max:255|unique:monhoc,TenMonHoc,' . $monHoc->MaMonHoc . ',MaMonHoc,TrangThai,1',
            'MaKhoa' => 'required|exists:khoa,MaKhoa',
            'MoTa' => 'nullable|string'
        ],[
            'TenMonHoc.unique' => 'Tên môn học đã tồn tại trong hệ thống.',
            'MaKhoa.exists' => 'Khoa không tồn tại trong hệ thống.'
        ]);

        $monHoc->update([
            'TenMonHoc' => $request->TenMonHoc,
            'MaKhoa' => $request->MaKhoa,
            'MoTa' => $request->MoTa
        ]);

        return redirect()->route('admin.quan-ly-mon-hoc.danh-sach')
            ->with('success', 'Cập nhật môn học thành công');
    }

    public function xoa(MonHoc $monHoc)
    {
        // Kiểm tra xem môn học có bài giảng nào không
        if ($monHoc->baiGiangs()->count() > 0) {
            return redirect()->route('admin.quan-ly-mon-hoc.danh-sach')
                ->with('error', 'Không thể xóa môn học này vì đang có bài giảng thuộc môn học');
        }
        
        $monHoc->update(['TrangThai' => 0]);

        return redirect()->route('admin.quan-ly-mon-hoc.danh-sach')
            ->with('success', 'Xóa môn học thành công');
    }

}
