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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lop_hoc_phan');
    }
};