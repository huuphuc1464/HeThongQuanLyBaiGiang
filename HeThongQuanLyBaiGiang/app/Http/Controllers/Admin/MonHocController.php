<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonHoc;
use App\Models\Khoa;

class MonHocController extends Controller
{
    public function danhSach(Request $request)
    {
        $search = $request->input('search');
        $filterKhoa = $request->input('filterKhoa');
        
        $query = MonHoc::where('TrangThai', 1)->with('khoa');
        
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
            'TenMonHoc' => 'required|string|max:255|unique:mon_hoc,TenMonHoc,NULL,MaMonHoc,MaKhoa,' . $request->MaKhoa . ',TrangThai,1',
            'MaKhoa' => 'required|exists:khoa,MaKhoa',
            'MoTa' => 'nullable|string'
        ], [
            'TenMonHoc.unique' => 'Tên môn học đã tồn tại trong khoa này.',
            'MaKhoa.exists' => 'Khoa không tồn tại trong hệ thống.'
        ]);

        MonHoc::create([
            'TenMonHoc' => $request->TenMonHoc,
            'MaKhoa' => $request->MaKhoa,
            'MoTa' => $request->MoTa,
            'TrangThai' => 1
        ]);

        return redirect()->route('admin.mon-hoc.danh-sach')
            ->with('success', 'Thêm môn học thành công');
    }

    public function capNhat(Request $request, MonHoc $monHoc)
    {
        $request->validate([
       'TenMonHoc' => 'required|string|max:255|unique:mon_hoc,TenMonHoc,' . $monHoc->MaMonHoc . ',MaMonHoc,MaKhoa,' . $request->MaKhoa . ',TrangThai,1',
            'MaKhoa' => 'required|exists:khoa,MaKhoa',
            'MoTa' => 'nullable|string'
        ], [
            'TenMonHoc.unique' => 'Tên môn học đã tồn tại trong khoa này.',
            'MaKhoa.exists' => 'Khoa không tồn tại trong hệ thống.'
        ]);

        $monHoc->update([
            'TenMonHoc' => $request->TenMonHoc,
            'MaKhoa' => $request->MaKhoa,
            'MoTa' => $request->MoTa
        ]);

        return redirect()->route('admin.mon-hoc.danh-sach')
            ->with('success', 'Cập nhật môn học thành công');
    }

    public function xoa(MonHoc $monHoc)
    {
        if ($monHoc->hocPhans()->count() > 0) {
            return redirect()->route('admin.mon-hoc.danh-sach')
                ->with('error', 'Không thể xóa môn học này vì đang có học phần thuộc môn học');
        }
        
        $monHoc->update(['TrangThai' => 0]);

        return redirect()->route('admin.mon-hoc.danh-sach')
            ->with('success', 'Xóa môn học thành công');
    }
}