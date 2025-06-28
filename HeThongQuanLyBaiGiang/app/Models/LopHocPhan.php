<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LopHocPhan extends Model
{
    protected $table = 'lop_hoc_phan';
    protected $primaryKey = 'MaLopHocPhan';
    protected $fillable = ['MaBaiGiang', 'MaNguoiTao', 'TenLopHocPhan', 'MoTa', 'TrangThai'];
    public $timestamps = true;

    // Relationships
    public function baiGiang()
    {
        return $this->belongsTo(BaiGiang::class, 'MaBaiGiang', 'MaBaiGiang');
    }

    public function nguoiTao()
    {
        return $this->belongsTo(NguoiDung::class, 'MaNguoiTao', 'MaNguoiDung');
    }

    public function danhSachLop()
    {
        return $this->hasMany(DanhSachLop::class, 'MaLopHocPhan', 'MaLopHocPhan');
    }

    public function baiKiemTra()
    {
        return $this->hasMany(BaiKiemTra::class, 'MaLopHocPhan', 'MaLopHocPhan');
    }

    public function suKienZoom()
    {
        return $this->hasMany(SuKienZoom::class, 'MaLopHocPhan', 'MaLopHocPhan');
    }

    public function thongBao()
    {
        return $this->hasMany(ThongBao::class, 'MaLopHocPhan', 'MaLopHocPhan');
    }
}