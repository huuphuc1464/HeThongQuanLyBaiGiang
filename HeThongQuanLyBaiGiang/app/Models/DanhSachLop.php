<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhSachLop extends Model
{
    protected $table = 'danh_sach_lop';
    protected $primaryKey = 'MaDanhSachLop';
    protected $fillable = ['MaLopHocPhan', 'MaSinhVien', 'MaXacNhan', 'TrangThai'];
    public $timestamps = true;

    // Relationships
    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'MaLopHocPhan', 'MaLopHocPhan');
    }

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'MaSinhVien', 'MaNguoiDung');
    }
}