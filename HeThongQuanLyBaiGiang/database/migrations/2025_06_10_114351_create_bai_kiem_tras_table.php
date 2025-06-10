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
        Schema::create('bai_kiem_tra', function (Blueprint $table) {
            $table->id('MaBaiKiemTra');
            $table->foreignId('MaLopHocPhan')->constrained('lop_hoc_phan', 'MaLopHocPhan');
            $table->foreignId('MaGiangVien')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->string('TenBaiKiemTra', 255);
            $table->dateTime('ThoiGianBatDau');
            $table->dateTime('ThoiGianKetThuc');
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
        Schema::dropIfExists('bai_kiem_tra');
    }
};
