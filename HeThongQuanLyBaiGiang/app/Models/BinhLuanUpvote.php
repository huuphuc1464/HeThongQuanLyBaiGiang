<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BinhLuanUpvote extends Model
{
    protected $table = 'binh_luan_upvotes';

    protected $fillable = [
        'MaBinhLuan',
        'MaNguoiDung',
        'LoaiUpvote'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ với bình luận
     */
    public function binhLuan(): BelongsTo
    {
        return $this->belongsTo(BinhLuanBaiGiang::class, 'MaBinhLuan', 'MaBinhLuan');
    }

    /**
     * Quan hệ với người dùng
     */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'MaNguoiDung', 'MaNguoiDung');
    }
}
