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
            'MaBaiGiang' => 'required|exists:bai_giang,MaBaiGiang',
            'NoiDung' => 'required|string|max:255',
        ]);

        DB::table('binh_luan_bai_giang')->insert([
            'MaNguoiGui' => Auth::id(),
            'MaBaiGiang' => $request->MaBaiGiang,
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
            'MaBaiGiang' => 'required|exists:bai_giang,MaBaiGiang',
            'NoiDung' => 'required|string|max:255',
        ]);

        $nguoiGui = Auth::id();

        DB::table('binh_luan_bai_giang')->insert([
            'MaNguoiGui' => $nguoiGui,
            'MaBaiGiang' => $request->MaBaiGiang,
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

        DB::table('binh_luan_bai_giang')
            ->where('MaBinhLuan', $request->MaBinhLuan)
            ->update([
                'NoiDung' => $request->NoiDung,
                'DaChinhSua' => true,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Cập nhật bình luận thành công');
    }

    public function xoa($id)
    {
        DB::transaction(
            function () use ($id) {
                DB::table('binh_luan_bai_giang')->where('MaBinhLuanCha', $id)->delete();
                DB::table('binh_luan_bai_giang')->where('MaBinhLuan', $id)->delete();
            }
        );
        return redirect()->back()->with('success', 'Đã xóa bình luận và các phản hồi liên quan.');
    }
}
