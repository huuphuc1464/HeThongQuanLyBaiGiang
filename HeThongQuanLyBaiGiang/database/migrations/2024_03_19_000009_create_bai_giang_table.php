<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bai_giang', function (Blueprint $table) {
            $table->id('MaBaiGiang');
            $table->foreignId('MaGiangVien')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->foreignId('MaHocPhan')->constrained('hoc_phan', 'MaHocPhan');
            $table->string('TenChuong', 255);
            $table->string('TenBai', 255);
            $table->string('TenMuc', 255);
            $table->string('TenBaiGiang', 255);
            $table->text('NoiDung');
            $table->string('MoTa', 255)->nullable();
            $table->integer('TrangThai')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bai_giang');
    }
}; 