<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bai extends Model
{
    protected $table = 'bai';
    protected $primaryKey = 'MaBai';
    protected $fillable = [
        'MaGiangVien',
        'MaChuong',
        'TenBai',
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

    public function fileBai()
    {
        return $this->hasMany(FileBaiGiang::class, 'MaBai', 'MaBai');
    }

    public function binhLuanBai()
    {
        return $this->hasMany(BinhLuanBaiGiang::class, 'MaBai', 'MaBai');
    }

    public function chuong()
    {
        return $this->belongsTo(Chuong::class, 'MaChuong', 'MaChuong');
    }
}
