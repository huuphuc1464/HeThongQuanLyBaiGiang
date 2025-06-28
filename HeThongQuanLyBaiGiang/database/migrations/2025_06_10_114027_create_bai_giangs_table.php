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
        Schema::create('bai_giang', function (Blueprint $table) {
            $table->id('MaBaiGiang');
            $table->foreignId('MaGiangVien')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->foreignId('MaKhoa')->constrained('khoa', 'MaKhoa');
            $table->string('TenBaiGiang', 255);
            $table->string('AnhBaiGiang', 255)->nullable();
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
        Schema::dropIfExists('bai_giang');
    }
};