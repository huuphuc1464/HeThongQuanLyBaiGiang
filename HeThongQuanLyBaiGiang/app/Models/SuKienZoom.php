<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuKienZoom extends Model
{
    protected $table = 'su_kien_zoom';
    protected $primaryKey = 'MaSuKienZoom';
    protected $fillable = [
        'MaLopHocPhan',
        'MaGiangVien',
        'TenSuKien',
        'MoTa',
        'ThoiGianBatDau',
        'ThoiGianKetThuc',
        'LinkSuKien',
        'KhoaChuTri',
        'MatKhauSuKien'
    ];
    public $timestamps = true;

    // Relationships
    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'MaLopHocPhan', 'MaLopHocPhan');
    }

    public function giangVien()
    {
        return $this->belongsTo(NguoiDung::class, 'MaGiangVien', 'MaNguoiDung');
    }
}
