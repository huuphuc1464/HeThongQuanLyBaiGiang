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
        Schema::create('danh_sach_lop', function (Blueprint $table) {
            $table->id('MaDanhSachLop');
            $table->foreignId('MaLopHocPhan')->constrained('lop_hoc_phan', 'MaLopHocPhan');
            $table->foreignId('MaSinhVien')->constrained('sinh_vien', 'MaNguoiDung');
            $table->string('MaXacNhan', 50)->nullable();
            $table->integer('TrangThai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_sach_lop');
    }
};
