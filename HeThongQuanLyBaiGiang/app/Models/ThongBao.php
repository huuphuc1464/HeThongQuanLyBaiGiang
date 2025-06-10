<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    protected $table = 'thong_bao';
    protected $primaryKey = 'MaThongBao';
    protected $fillable = ['MaLopHocPhan', 'MaNguoiTao', 'NoiDung', 'ThoiGianTao', 'TrangThai'];
    public $timestamps = true;

    // Relationships
    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'MaLopHocPhan', 'MaLopHocPhan');
    }

    public function nguoiTao()
    {
        return $this->belongsTo(NguoiDung::class, 'MaNguoiTao', 'MaNguoiDung');
    }
}
