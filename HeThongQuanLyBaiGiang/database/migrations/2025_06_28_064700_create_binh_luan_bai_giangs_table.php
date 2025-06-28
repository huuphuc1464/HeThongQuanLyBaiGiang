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
        Schema::create('binh_luan_bai_giang', function (Blueprint $table) {
            $table->id('MaBinhLuan');
            $table->foreignId('MaNguoiGui')->constrained('nguoi_dung', 'MaNguoiDung');
            $table->foreignId('MaBai')->constrained('bai', 'MaBai');
            $table->foreignId('MaBinhLuanCha')->nullable()->constrained('binh_luan_bai_giang', 'MaBinhLuan');
            $table->string('NoiDung', 255);
            $table->boolean('DaChinhSua')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binh_luan_bai_giang');
    }
};