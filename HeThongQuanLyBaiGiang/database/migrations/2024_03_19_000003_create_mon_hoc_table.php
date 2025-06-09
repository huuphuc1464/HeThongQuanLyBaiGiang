<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mon_hoc', function (Blueprint $table) {
            $table->id('MaMonHoc');
            $table->foreignId('MaKhoa')->constrained('khoa', 'MaKhoa');
            $table->string('TenMonHoc', 100);
            $table->string('MoTa', 255)->nullable();
            $table->integer('TrangThai')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mon_hoc');
    }
}; 