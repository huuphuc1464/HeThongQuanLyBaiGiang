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
        Schema::create('su_kien_zoom', function (Blueprint $table) {
            $table->id('MaSuKienZoom');
            $table->foreignId('MaLopHocPhan')->constrained('lop_hoc_phan', 'MaLopHocPhan');
            $table->foreignId('MaGiangVien')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->string('TenSuKien', 100);
            $table->string('MoTa', 255)->nullable();
            $table->dateTime('ThoiGianBatDau');
            $table->dateTime('ThoiGianKetThuc');
            $table->string('LinkSuKien', 255);
            $table->string('KhoaChuTri', 6);
            $table->string('MatKhauSuKien', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('su_kien_zoom');
    }
};
