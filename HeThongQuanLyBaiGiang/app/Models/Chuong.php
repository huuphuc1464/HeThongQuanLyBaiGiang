<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chuong extends Model
{
    protected $table = 'chuong';
    protected $primaryKey = 'MaChuong';
    public $timestamps = true;

    protected $fillable = [
        'MaBaiGiang',
        'MaGiangVien',
        'TenChuong',
        'MoTa',
        'TrangThai',
    ];

    public function baiGiang()
    {
        return $this->belongsTo(BaiGiang::class, 'MaBaiGiang', 'MaBaiGiang');
    }

    public function giangVien()
    {
        return $this->belongsTo(NguoiDung::class, 'MaGiangVien', 'MaNguoiDung');
    }
    public function bai()
    {
        return $this->hasMany(Bai::class, 'MaChuong', 'MaChuong');
    }
}
