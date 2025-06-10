<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NguoiDung extends Model
{
    protected $primaryKey = 'MaNguoiDung';
    protected $table = 'nguoi_dung';

    protected $fillable = [
        'MaVaiTro',
        'TenTaiKhoan',
        'MatKhau',
        'Email',
        'HoTen',
        'SoDienThoai',
        'AnhDaiDien',
        'DiaChi',
        'NgaySinh',
        'GioiTinh',
        'LanDauDangNhap',
        'TrangThai'
    ];
    public $timestamps = true;

    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'MaVaiTro', 'MaVaiTro');
    }

    public function sinhVien()
    {
        return $this->hasOne(SinhVien::class, 'MaNguoiDung', 'MaNguoiDung');
    }

    public function hocPhans()
    {
        return $this->hasMany(HocPhan::class, 'MaNguoiTao', 'MaNguoiDung');
    }

    public function lopHocPhans()
    {
        return $this->hasMany(LopHocPhan::class, 'MaNguoiTao', 'MaNguoiDung');
    }

    public function baiGiangs()
    {
        return $this->hasMany(BaiGiang::class, 'MaGiangVien', 'MaNguoiDung');
    }

    public function binhLuanBaiGiang()
    {
        return $this->hasMany(BinhLuanBaiGiang::class, 'MaNguoiGui', 'MaNguoiDung');
    }

    public function baiKiemTra()
    {
        return $this->hasMany(BaiKiemTra::class, 'MaGiangVien', 'MaNguoiDung');
    }

    public function suKienZoom()
    {
        return $this->hasMany(SuKienZoom::class, 'MaGiangVien', 'MaNguoiDung');
    }

    public function thongBao()
    {
        return $this->hasMany(ThongBao::class, 'MaNguoiTao', 'MaNguoiDung');
    }
}
