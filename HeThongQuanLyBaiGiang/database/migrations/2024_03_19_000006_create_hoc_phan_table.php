<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hoc_phan', function (Blueprint $table) {
            $table->id('MaHocPhan');
            $table->foreignId('MaMonHoc')->constrained('mon_hoc', 'MaMonHoc');
            $table->foreignId('MaNguoiTao')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->string('TenHocPhan', 100);
            $table->string('MoTa', 255)->nullable();
            $table->string('AnhHocPhan', 255)->nullable();
            $table->integer('TrangThai')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hoc_phan');
    }
}; 