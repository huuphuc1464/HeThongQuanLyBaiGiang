<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lop_hoc_phan', function (Blueprint $table) {
            $table->id('MaLopHocPhan');
            $table->foreignId('MaHocPhan')->constrained('hoc_phan', 'MaHocPhan');
            $table->foreignId('MaNguoiTao')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->string('TenLopHocPhan', 100);
            $table->text('MoTa')->nullable();
            $table->integer('TrangThai')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lop_hoc_phan');
    }
}; 