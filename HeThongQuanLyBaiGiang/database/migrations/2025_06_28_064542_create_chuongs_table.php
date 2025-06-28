<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chuong', function (Blueprint $table) {
            $table->id('MaChuong');
            $table->foreignId('MaBaiGiang')->constrained('bai_giang', 'MaBaiGiang');
            $table->foreignId('MaGiangVien')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->string('TenChuong', 255);
            $table->string('MoTa', 255)->nullable();
            $table->integer('TrangThai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuong');
    }
};