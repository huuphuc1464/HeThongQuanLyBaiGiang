<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaiGiang extends Model
{
    protected $table = 'bai_giang';
    protected $primaryKey = 'MaBaiGiang';
    protected $fillable = [
        'MaGiangVien',
        'MaKhoa',
        'TenBaiGiang',
        'AnhBaiGiang',
        'MoTa',
        'TrangThai'
    ];
    public $timestamps = true;

    // Relationships
    public function giangVien()
    {
        return $this->belongsTo(NguoiDung::class, 'MaGiangVien', 'MaNguoiDung');
    }
    
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'MaKhoa', 'MaKhoa');
    }

    public function lopHocPhan()
    {
        return $this->hasMany(LopHocPhan::class, 'MaBaiGiang', 'MaBaiGiang');
    }
    
    public function chuong()
    {
        return $this->hasMany(Chuong::class, 'MaBaiGiang', 'MaBaiGiang');
    }
}