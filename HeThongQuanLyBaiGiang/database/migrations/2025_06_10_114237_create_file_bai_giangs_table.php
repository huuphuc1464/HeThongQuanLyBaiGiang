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
        Schema::create('file_bai_giang', function (Blueprint $table) {
            $table->id('MaFileBaiGiang');
            $table->foreignId('MaBaiGiang')->constrained('bai_giang', 'MaBaiGiang');
            $table->string('DuongDan', 255);
            $table->string('LoaiFile', 50);
            $table->integer('TrangThai')->default(1);
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_bai_giang');
    }
};