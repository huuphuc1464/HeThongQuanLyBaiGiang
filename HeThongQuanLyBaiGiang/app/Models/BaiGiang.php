<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaiGiang extends Model
{
    protected $table = 'bai_giang';
    protected $primaryKey = 'MaBaiGiang';
    protected $fillable = [
        'MaGiangVien',
        'MaHocPhan',
        'TenChuong',
        'TenBai',
        'TenMuc',
        'TenBaiGiang',
        'NoiDung',
        'MoTa',
        'TrangThai'
    ];
    public $timestamps = true;

    // Relationships
    public function giangVien()
    {
        return $this->belongsTo(NguoiDung::class, 'MaGiangVien', 'MaNguoiDung');
    }

    public function hocPhan()
    {
        return $this->belongsTo(HocPhan::class, 'MaHocPhan', 'MaHocPhan');
    }

    public function fileBaiGiang()
    {
        return $this->hasMany(FileBaiGiang::class, 'MaBaiGiang', 'MaBaiGiang');
    }

    public function binhLuanBaiGiang()
    {
        return $this->hasMany(BinhLuanBaiGiang::class, 'MaBaiGiang', 'MaBaiGiang');
    }
}
