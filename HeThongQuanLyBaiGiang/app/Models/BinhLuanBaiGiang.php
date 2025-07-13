<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BinhLuanBaiGiang extends Model
{
    protected $table = 'binh_luan_bai_giang';
    protected $primaryKey = 'MaBinhLuan';

    protected $fillable = [
        'MaNguoiGui',
        'MaBai',
        'MaBinhLuanCha',
        'NoiDung',
        'DaChinhSua',
        'SoUpvote',
        'SoDownvote',
        'DaAn',
        'ThoiGianChinhSua',
        'LyDoChinhSua'
    ];

    public $timestamps = true;

    protected $casts = [
        'DaChinhSua' => 'boolean',
        'DaAn' => 'boolean',
        'SoUpvote' => 'integer',
        'SoDownvote' => 'integer',
        'ThoiGianChinhSua' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function nguoiGui(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'MaNguoiGui', 'MaNguoiDung');
    }

    public function bai(): BelongsTo
    {
        return $this->belongsTo(Bai::class, 'MaBai', 'MaBai');
    }

    public function binhLuanCha(): BelongsTo
    {
        return $this->belongsTo(BinhLuanBaiGiang::class, 'MaBinhLuanCha', 'MaBinhLuan');
    }

    public function binhLuanCon(): HasMany
    {
        return $this->hasMany(BinhLuanBaiGiang::class, 'MaBinhLuanCha', 'MaBinhLuan');
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(BinhLuanUpvote::class, 'MaBinhLuan', 'MaBinhLuan');
    }

    /**
     * Lấy tổng điểm (upvote - downvote)
     */
    public function getDiemAttribute(): int
    {
        return $this->SoUpvote - $this->SoDownvote;
    }

    /**
     * Kiểm tra xem người dùng đã upvote chưa
     */
    public function daUpvote($maNguoiDung): bool
    {
        return $this->upvotes()
            ->where('MaNguoiDung', $maNguoiDung)
            ->where('LoaiUpvote', 'upvote')
            ->exists();
    }

    /**
     * Kiểm tra xem người dùng đã downvote chưa
     */
    public function daDownvote($maNguoiDung): bool
    {
        return $this->upvotes()
            ->where('MaNguoiDung', $maNguoiDung)
            ->where('LoaiUpvote', 'downvote')
            ->exists();
    }

    /**
     * Lấy loại vote của người dùng cho bình luận này
     */
    public function getLoaiVote($maNguoiDung): ?string
    {
        $upvote = $this->upvotes()
            ->where('MaNguoiDung', $maNguoiDung)
            ->first();

        return $upvote ? $upvote->LoaiUpvote : null;
    }
}
