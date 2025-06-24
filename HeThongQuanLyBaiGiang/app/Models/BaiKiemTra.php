<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaiKiemTra extends Model
{
    protected $table = 'bai_kiem_tra';
    protected $primaryKey = 'MaBaiKiemTra';
    protected $fillable = [
        'MaLopHocPhan',
        'MaGiangVien',
        'TenBaiKiemTra',
        'ThoiGianBatDau',
        'ThoiGianKetThuc',
        'ThoiGianLamBai',
        'ChoPhepXemKetQua',
        'MoTa',
        'TrangThai'
    ];

    protected $casts = [
        'ThoiGianBatDau' => 'datetime',
        'ThoiGianKetThuc' => 'datetime',
        'ChoPhepXemKetQua' => 'boolean',
        'ThoiGianLamBai' => 'integer'
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

    public function cauHoiBaiKiemTra()
    {
        return $this->hasMany(CauHoiBaiKiemTra::class, 'MaBaiKiemTra', 'MaBaiKiemTra');
    }

    public function ketQuaBaiKiemTra()
    {
        return $this->hasMany(KetQuaBaiKiemTra::class, 'MaBaiKiemTra', 'MaBaiKiemTra');
    }
}
