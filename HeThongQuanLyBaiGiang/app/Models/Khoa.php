<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    protected $primaryKey = 'MaKhoa';
    protected $table = 'khoa';
    protected $fillable = ['TenKhoa', 'MoTa', 'TrangThai'];
    public $timestamps = true;
    public function baiGiangs()
    {
        return $this->hasMany(BaiGiang::class, 'MaKhoa', 'MaKhoa');
    }
}
