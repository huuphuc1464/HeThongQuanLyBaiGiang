<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KetQuaBaiKiemTra extends Model
{
    protected $table = 'ket_qua_bai_kiem_tra';
    protected $primaryKey = 'MaKetQua';
    protected $fillable = ['MaBaiKiemTra', 'MaSinhVien', 'TongCauDung', 'TongSoCauHoi', 'NgayNop'];
    public $timestamps = true;

    // Relationships
    public function baiKiemTra()
    {
        return $this->belongsTo(BaiKiemTra::class, 'MaBaiKiemTra', 'MaBaiKiemTra');
    }

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'MaSinhVien', 'MaNguoiDung');
    }

    public function chiTietKetQua()
    {
        return $this->hasMany(ChiTietKetQua::class, 'MaKetQua', 'MaKetQua');
    }
}
