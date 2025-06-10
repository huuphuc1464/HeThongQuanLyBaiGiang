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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoc_phan');
    }
};