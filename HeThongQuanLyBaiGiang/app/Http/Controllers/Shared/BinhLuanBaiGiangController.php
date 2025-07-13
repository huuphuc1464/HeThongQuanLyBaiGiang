<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\BinhLuanBaiGiang;
use App\Models\BinhLuanUpvote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BinhLuanBaiGiangController extends Controller
{
    public function guiBinhLuan(Request $request)
    {
        $request->validate([
            'MaBai' => 'required|exists:bai,MaBai',
            'NoiDung' => 'required|string',
        ]);

        $binhLuan = BinhLuanBaiGiang::create([
            'MaNguoiGui' => Auth::id(),
            'MaBai' => $request->MaBai,
            'MaBinhLuanCha' => $request->MaBinhLuanCha ?? null,
            'NoiDung' => $request->NoiDung,
            'DaChinhSua' => false,
            'SoUpvote' => 0,
            'SoDownvote' => 0,
            'DaAn' => false,
        ]);

        // Broadcast event cho realtime
        // broadcast(new \App\Events\BinhLuanMoi($binhLuan->load('nguoiGui')))->toOthers();

        if ($request->expectsJson()) {
            // Thêm thông tin vote cho bình luận mới
            $maNguoiDung = Auth::id();
            $binhLuan->daUpvote = false;
            $binhLuan->daDownvote = false;

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi bình luận thành công.',
                'binhLuan' => $binhLuan->load('nguoiGui')
            ]);
        }
    }

    public function traLoiBinhLuan(Request $request)
    {
        $request->validate([
            'MaBinhLuan' => 'required|exists:binh_luan_bai_giang,MaBinhLuan',
            'MaBai' => 'required|exists:bai,MaBai',
            'NoiDung' => 'required|string',
        ]);

        $binhLuan = BinhLuanBaiGiang::create([
            'MaNguoiGui' => Auth::id(),
            'MaBai' => $request->MaBai,
            'MaBinhLuanCha' => $request->MaBinhLuan,
            'NoiDung' => $request->NoiDung,
            'DaChinhSua' => false,
            'SoUpvote' => 0,
            'SoDownvote' => 0,
            'DaAn' => false,
        ]);

        // Broadcast event cho realtime
        // broadcast(new \App\Events\BinhLuanMoi($binhLuan->load('nguoiGui')))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Phản hồi của bạn đã được gửi.',
                'binhLuan' => $binhLuan->load('nguoiGui')
            ]);
        }
    }

    public function capNhat(Request $request)
    {
        $request->validate([
            'MaBinhLuan' => 'required|exists:binh_luan_bai_giang,MaBinhLuan',
            'NoiDung' => 'required|string',
            'LyDoChinhSua' => 'nullable|string|max:255',
        ]);

        $binhLuan = BinhLuanBaiGiang::where('MaBinhLuan', $request->MaBinhLuan)
            ->where('MaNguoiGui', Auth::id())
            ->first();

        if (!$binhLuan) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền chỉnh sửa bình luận này.'
                ], 403);
            }
            return back()->with('error', 'Bạn không có quyền chỉnh sửa bình luận này.');
        }

        $binhLuan->update([
            'NoiDung' => $request->NoiDung,
            'DaChinhSua' => true,
            'ThoiGianChinhSua' => now(),
            'LyDoChinhSua' => $request->LyDoChinhSua,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật bình luận thành công.',
                'binhLuan' => $binhLuan->load('nguoiGui')
            ]);
        }
    }

    public function xoa($maBinhLuan)
    {
        $binhLuan = BinhLuanBaiGiang::where('MaBinhLuan', $maBinhLuan)
            ->where('MaNguoiGui', Auth::id())
            ->first();

        if (!$binhLuan) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa bình luận này.'
                ], 403);
            }
        }

        $maBai = $binhLuan->MaBai;
        // Xóa tất cả upvotes trước
        $binhLuan->upvotes()->delete();
        // Xóa tất cả bình luận con trước
        $binhLuan->binhLuanCon()->delete();
        // Xóa bình luận chính
        $binhLuan->delete();
        // Broadcast event cho realtime
        // broadcast(new \App\Events\BinhLuanDeleted($maBinhLuan, $maBai))->toOthers();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bình luận thành công.',
                'maBinhLuan' => $maBinhLuan
            ]);
        }
    }

    /**
     * Upvote/Downvote bình luận
     */
    public function vote(Request $request): JsonResponse
    {
        $request->validate([
            'MaBinhLuan' => 'required|exists:binh_luan_bai_giang,MaBinhLuan',
            'LoaiVote' => 'required|in:upvote,downvote',
        ]);

        $maNguoiDung = Auth::id();
        $maBinhLuan = $request->MaBinhLuan;
        $loaiVote = $request->LoaiVote;

        $binhLuan = BinhLuanBaiGiang::findOrFail($maBinhLuan);
        // Kiểm tra xem người dùng đã vote chưa
        $existingVote = BinhLuanUpvote::where('MaBinhLuan', $maBinhLuan)
            ->where('MaNguoiDung', $maNguoiDung)
            ->first();

        if ($existingVote) {
            if ($existingVote->LoaiUpvote === $loaiVote) {
                // Nếu vote cùng loại, hủy vote
                $existingVote->delete();

                // Cập nhật số lượng
                if ($loaiVote === 'upvote') {
                    $binhLuan->decrement('SoUpvote');
                } else {
                    $binhLuan->decrement('SoDownvote');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Đã hủy ' . ($loaiVote === 'upvote' ? 'upvote' : 'downvote'),
                    'loaiVote' => null,
                    'soUpvote' => $binhLuan->fresh()->SoUpvote,
                    'soDownvote' => $binhLuan->fresh()->SoDownvote,
                    'daUpvote' => false,
                    'daDownvote' => false,
                ]);
            } else {
                // Nếu vote khác loại, thay đổi loại vote
                $existingVote->update(['LoaiUpvote' => $loaiVote]);

                // Cập nhật số lượng
                if ($loaiVote === 'upvote') {
                    $binhLuan->increment('SoUpvote');
                    $binhLuan->decrement('SoDownvote');
                } else {
                    $binhLuan->decrement('SoUpvote');
                    $binhLuan->increment('SoDownvote');
                }
            }
        } else {
            // Tạo vote mới
            BinhLuanUpvote::create([
                'MaBinhLuan' => $maBinhLuan,
                'MaNguoiDung' => $maNguoiDung,
                'LoaiUpvote' => $loaiVote,
            ]);

            // Cập nhật số lượng
            if ($loaiVote === 'upvote') {
                $binhLuan->increment('SoUpvote');
            } else {
                $binhLuan->increment('SoDownvote');
            }
        }

        // broadcast(new \App\Events\BinhLuanVoted($binhLuan->MaBinhLuan, $binhLuan->SoUpvote, $binhLuan->SoDownvote))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Đã ' . ($loaiVote === 'upvote' ? 'upvote' : 'downvote') . ' thành công',
            'loaiVote' => $loaiVote,
            'soUpvote' => $binhLuan->fresh()->SoUpvote,
            'soDownvote' => $binhLuan->fresh()->SoDownvote,
            'daUpvote' => $loaiVote === 'upvote',
            'daDownvote' => $loaiVote === 'downvote',
        ]);
    }

    /**
     * Lấy danh sách bình luận với sắp xếp
     */
    public function layDanhSach(Request $request): JsonResponse
    {
        $request->validate([
            'MaBai' => 'required|exists:bai,MaBai',
            'SapXep' => 'nullable|in:moi_nhat,cu_nhat,nhieu_upvote,it_upvote',
        ]);

        $maBai = $request->MaBai;
        $sapXep = $request->SapXep ?? 'moi_nhat';
        $maNguoiDung = Auth::id();

        $query = BinhLuanBaiGiang::with(['nguoiGui', 'binhLuanCon.nguoiGui'])
            ->where('MaBai', $maBai)
            ->whereNull('MaBinhLuanCha')
            ->where('DaAn', false);

        switch ($sapXep) {
            case 'moi_nhat':
                $query->orderBy('created_at', 'desc');
                break;
            case 'cu_nhat':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nhieu_upvote':
                $query->orderBy('SoUpvote', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'it_upvote':
                $query->orderBy('SoUpvote', 'asc')->orderBy('created_at', 'desc');
                break;
        }

        $binhLuans = $query->paginate(10);

        // Thêm thông tin trạng thái vote của người dùng hiện tại
        $binhLuans->getCollection()->transform(function ($binhLuan) use ($maNguoiDung) {
            $vote = BinhLuanUpvote::where('MaBinhLuan', $binhLuan->MaBinhLuan)
                ->where('MaNguoiDung', $maNguoiDung)
                ->first();

            $binhLuan->daUpvote = $vote && $vote->LoaiUpvote === 'upvote';
            $binhLuan->daDownvote = $vote && $vote->LoaiUpvote === 'downvote';

            // Thêm thông tin vote cho bình luận con
            if ($binhLuan->binhLuanCon) {
                $binhLuan->binhLuanCon->transform(function ($binhLuanCon) use ($maNguoiDung) {
                    $voteCon = BinhLuanUpvote::where('MaBinhLuan', $binhLuanCon->MaBinhLuan)
                        ->where('MaNguoiDung', $maNguoiDung)
                        ->first();

                    $binhLuanCon->daUpvote = $voteCon && $voteCon->LoaiUpvote === 'upvote';
                    $binhLuanCon->daDownvote = $voteCon && $voteCon->LoaiUpvote === 'downvote';

                    return $binhLuanCon;
                });
            }

            return $binhLuan;
        });

        return response()->json([
            'success' => true,
            'binhLuans' => $binhLuans,
        ]);
    }
}
