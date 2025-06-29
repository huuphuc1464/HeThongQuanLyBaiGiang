<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BinhLuanBaiGiangController extends Controller
{
    public function guiBinhLuan(Request $request)
    {
        $request->validate([
            'MaBai' => 'required|exists:bai,MaBai',
            'NoiDung' => 'required|string|max:255',
        ]);

        DB::table('binh_luan_bai_giang')->insert([
            'MaNguoiGui' => Auth::id(),
            'MaBai' => $request->MaBai,
            'MaBinhLuanCha' => null,
            'NoiDung' => $request->NoiDung,
            'DaChinhSua' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đã gửi bình luận thành công.');
    }

    public function traLoiBinhLuan(Request $request)
    {
        $request->validate([
            'MaBinhLuan' => 'required|exists:binh_luan_bai_giang,MaBinhLuan',
            'MaBai' => 'required|exists:bai,MaBai',
            'NoiDung' => 'required|string|max:255',
        ]);

        $nguoiGui = Auth::id();

        DB::table('binh_luan_bai_giang')->insert([
            'MaNguoiGui' => $nguoiGui,
            'MaBai' => $request->MaBai,
            'MaBinhLuanCha' => $request->MaBinhLuan,
            'NoiDung' => $request->NoiDung,
            'DaChinhSua' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Phản hồi của bạn đã được gửi.');
    }

    public function capNhat(Request $request)
    {
        $request->validate([
            'MaBinhLuan' => 'required|exists:binh_luan_bai_giang,MaBinhLuan',
            'NoiDung' => 'required|string|max:255',
        ]);

        $binhLuan = DB::table('binh_luan_bai_giang')
            ->where('MaBinhLuan', $request->MaBinhLuan)
            ->where('MaNguoiGui', Auth::id())
            ->first();

        if (!$binhLuan) {
            return back()->with('error', 'Bạn không có quyền chỉnh sửa bình luận này.');
        }

        DB::table('binh_luan_bai_giang')
            ->where('MaBinhLuan', $request->MaBinhLuan)
            ->update([
                'NoiDung' => $request->NoiDung,
                'DaChinhSua' => true,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Đã cập nhật bình luận thành công.');
    }

    public function xoa($maBinhLuan)
    {
        $binhLuan = DB::table('binh_luan_bai_giang')
            ->where('MaBinhLuan', $maBinhLuan)
            ->where('MaNguoiGui', Auth::id())
            ->first();

        if (!$binhLuan) {
            return back()->with('error', 'Bạn không có quyền xóa bình luận này.');
        }

        // Xóa tất cả bình luận con trước
        DB::table('binh_luan_bai_giang')
            ->where('MaBinhLuanCha', $maBinhLuan)
            ->delete();

        // Xóa bình luận chính
        DB::table('binh_luan_bai_giang')
            ->where('MaBinhLuan', $maBinhLuan)
            ->delete();

        return back()->with('success', 'Đã xóa bình luận thành công.');
    }
}
