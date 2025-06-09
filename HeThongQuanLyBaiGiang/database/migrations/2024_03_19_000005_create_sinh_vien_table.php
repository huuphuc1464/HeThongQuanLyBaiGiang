<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sinh_vien', function (Blueprint $table) {
            $table->foreignId('MaNguoiDung')->primary()->constrained('nguoi_dung', 'MaNguoiDung');
            $table->string('MSSV', 10)->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sinh_vien');
    }
}; 