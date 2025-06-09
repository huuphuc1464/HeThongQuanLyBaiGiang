<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chi_tiet_ket_qua', function (Blueprint $table) {
            $table->id('MaChiTietKetQua');
            $table->foreignId('MaKetQua')->constrained('ket_qua_bai_kiem_tra', 'MaKetQua');
            $table->foreignId('MaCauHoi')->constrained('cau_hoi_bai_kiem_tra', 'MaCauHoi');
            $table->text('DapAnSinhVien');
            $table->boolean('KetQua');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chi_tiet_ket_qua');
    }
}; 