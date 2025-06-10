<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SinhVien extends Model
{
    protected $primaryKey = 'MaNguoiDung'; // là khóa chính luôn
    protected $table = 'sinh_vien';
    public $incrementing = false; // không tự tăng

    protected $fillable = ['MaNguoiDung', 'MSSV'];
    public $timestamps = true;

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'MaNguoiDung', 'MaNguoiDung');
    }

    public function danhSachLop()
    {
        return $this->hasMany(DanhSachLop::class, 'MaSinhVien', 'MaNguoiDung');
    }

    public function ketQuaBaiKiemTra()
    {
        return $this->hasMany(KetQuaBaiKiemTra::class, 'MaSinhVien', 'MaNguoiDung');
    }
}
