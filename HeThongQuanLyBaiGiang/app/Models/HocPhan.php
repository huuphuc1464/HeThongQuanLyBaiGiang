<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HocPhan extends Model
{
    protected $primaryKey = 'MaHocPhan';
    protected $table = 'hoc_phan';
    protected $fillable = [
        'MaMonHoc',
        'MaNguoiTao',
        'TenHocPhan',
        'MoTa',
        'AnhHocPhan',
        'TrangThai'
    ];
    public $timestamps = true;

    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'MaMonHoc', 'MaMonHoc');
    }

    public function nguoiTao()
    {
        return $this->belongsTo(NguoiDung::class, 'MaNguoiTao', 'MaNguoiDung');
    }

    public function lopHocPhan()
    {
        return $this->hasMany(LopHocPhan::class, 'MaHocPhan', 'MaHocPhan');
    }

    public function baiGiang()
    {
        return $this->hasMany(BaiGiang::class, 'MaHocPhan', 'MaHocPhan');
    }
}
