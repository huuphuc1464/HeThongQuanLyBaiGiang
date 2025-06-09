<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ket_qua_bai_kiem_tra', function (Blueprint $table) {
            $table->id('MaKetQua');
            $table->foreignId('MaBaiKiemTra')->constrained('bai_kiem_tra', 'MaBaiKiemTra');
            $table->foreignId('MaSinhVien')->constrained('sinh_vien', 'MaNguoiDung');
            $table->integer('TongCauDung');
            $table->integer('TongSoCauHoi');
            $table->dateTime('NgayNop');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ket_qua_bai_kiem_tra');
    }
}; 