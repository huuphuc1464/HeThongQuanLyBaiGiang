<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    protected $table = 'vai_tro';

    protected $primaryKey = 'MaVaiTro';

    protected $fillable = [
        'TenVaiTro',
        'MoTa',
        'TrangThai',
    ];

    public $timestamps = true;

    public function nguoiDungs()
    {
        return $this->hasMany(NguoiDung::class, 'MaVaiTro', 'MaVaiTro');
    }
}
