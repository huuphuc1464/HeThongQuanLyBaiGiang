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
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id('MaNguoiDung');
            $table->foreignId('MaVaiTro')->constrained('vai_tro', 'MaVaiTro');
            $table->string('TenTaiKhoan', 50)->unique();
            $table->string('MatKhau', 255);
            $table->string('Email', 100)->unique();
            $table->string('HoTen', 100);
            $table->string('SoDienThoai', 10)->nullable();
            $table->string('AnhDaiDien', 255)->nullable();
            $table->string('DiaChi', 255)->nullable();
            $table->date('NgaySinh')->nullable();
            $table->string('GioiTinh', 3)->nullable();
            $table->boolean('LanDauDangNhap')->default(true);
            $table->integer('TrangThai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};