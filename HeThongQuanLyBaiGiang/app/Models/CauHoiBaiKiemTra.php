<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CauHoiBaiKiemTra extends Model
{
    protected $table = 'cau_hoi_bai_kiem_tra';
    protected $primaryKey = 'MaCauHoi';
    protected $fillable = ['MaBaiKiemTra', 'CauHoi', 'DapAnA', 'DapAnB', 'DapAnC', 'DapAnD', 'DapAnDung'];
    public $timestamps = true;

    // Relationships
    public function baiKiemTra()
    {
        return $this->belongsTo(BaiKiemTra::class, 'MaBaiKiemTra', 'MaBaiKiemTra');
    }

    public function chiTietKetQua()
    {
        return $this->hasMany(ChiTietKetQua::class, 'MaCauHoi', 'MaCauHoi');
    }
}
