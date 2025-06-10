<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietKetQua extends Model
{
    protected $table = 'chi_tiet_ket_qua';
    protected $primaryKey = 'MaChiTietKetQua';
    protected $fillable = ['MaKetQua', 'MaCauHoi', 'DapAnSinhVien', 'KetQua'];
    public $timestamps = true;

    // Relationships
    public function ketQua()
    {
        return $this->belongsTo(KetQuaBaiKiemTra::class, 'MaKetQua', 'MaKetQua');
    }

    public function cauHoi()
    {
        return $this->belongsTo(CauHoiBaiKiemTra::class, 'MaCauHoi', 'MaCauHoi');
    }
}
