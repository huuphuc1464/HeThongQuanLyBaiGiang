<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileBaiGiang extends Model
{
    protected $table = 'file_bai_giang';
    protected $primaryKey = 'MaFileBaiGiang';
    protected $fillable = ['MaBaiGiang', 'DuongDan', 'LoaiFile', 'TrangThai'];
    public $timestamps = true;

    // Relationships
    public function baiGiang()
    {
        return $this->belongsTo(BaiGiang::class, 'MaBaiGiang', 'MaBaiGiang');
    }
}
