<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->id('MaThongBao');
            $table->foreignId('MaLopHocPhan')->constrained('lop_hoc_phan', 'MaLopHocPhan');
            $table->foreignId('MaNguoiTao')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->string('NoiDung', 255);
            $table->dateTime('ThoiGianTao');
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('thong_bao');
    }
};
