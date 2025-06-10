<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonHoc extends Model
{
    protected $primaryKey = 'MaMonHoc';
    protected $table = 'mon_hoc';
    protected $fillable = ['MaKhoa', 'TenMonHoc', 'MoTa', 'TrangThai'];
    public $timestamps = true;

    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'MaKhoa', 'MaKhoa');
    }

    public function hocPhans()
    {
        return $this->hasMany(HocPhan::class, 'MaMonHoc', 'MaMonHoc');
    }
}
