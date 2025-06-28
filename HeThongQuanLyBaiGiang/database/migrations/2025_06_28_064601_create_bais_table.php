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
        Schema::create('bai', function (Blueprint $table) {
            $table->id('MaBai');
            $table->foreignId('MaChuong')->constrained('chuong', 'MaChuong');
            $table->foreignId('MaGiangVien')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->string('TenBai', 255);
            $table->text('NoiDung');
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
        Schema::dropIfExists('bai');
    }
};