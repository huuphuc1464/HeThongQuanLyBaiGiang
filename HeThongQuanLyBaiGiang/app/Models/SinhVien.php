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

    // mỗi sinh viên là một người dùng, sinh viên thuộc về người dùng
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'MaNguoiDung', 'MaNguoiDung');
    }
    // mỗi sinh viên có thể học nhiều lớp
    public function danhSachLop()
    {
        return $this->hasMany(DanhSachLop::class, 'MaSinhVien', 'MaNguoiDung');
    }
    // Mỗi sinh viên có thể làm nhiều bài kiểm tra
    public function ketQuaBaiKiemTra()
    {
        return $this->hasMany(KetQuaBaiKiemTra::class, 'MaSinhVien', 'MaNguoiDung');
    }
}
