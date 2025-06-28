<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileBaiGiang extends Model
{
    protected $table = 'file_bai_giang';
    protected $primaryKey = 'MaFileBaiGiang';
    protected $fillable = ['MaBai', 'DuongDan', 'LoaiFile', 'TrangThai'];
    public $timestamps = true;

    // Relationships
    public function bai()
    {
        return $this->belongsTo(Bai::class, 'MaBai', 'MaBai');
    }
}