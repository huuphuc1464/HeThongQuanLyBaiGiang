<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinhLuanBaiGiang extends Model
{
    protected $table = 'binh_luan_bai_giang';
    protected $primaryKey = 'MaBinhLuan';
    protected $fillable = ['MaNguoiGui', 'MaBaiGiang', 'MaBinhLuanCha', 'NoiDung', 'DaChinhSua'];
    public $timestamps = true;

    // Relationships
    public function nguoiGui()
    {
        return $this->belongsTo(NguoiDung::class, 'MaNguoiGui', 'MaNguoiDung');
    }

    public function baiGiang()
    {
        return $this->belongsTo(BaiGiang::class, 'MaBaiGiang', 'MaBaiGiang');
    }

    public function binhLuanCha()
    {
        return $this->belongsTo(BinhLuanBaiGiang::class, 'MaBinhLuanCha', 'MaBinhLuan');
    }

    public function binhLuanCon()
    {
        return $this->hasMany(BinhLuanBaiGiang::class, 'MaBinhLuanCha', 'MaBinhLuan');
    }
}
