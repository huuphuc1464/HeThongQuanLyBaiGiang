<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cau_hoi_bai_kiem_tra', function (Blueprint $table) {
            $table->id('MaCauHoi');
            $table->foreignId('MaBaiKiemTra')->constrained('bai_kiem_tra', 'MaBaiKiemTra');
            $table->text('CauHoi');
            $table->text('DapAnA');
            $table->text('DapAnB');
            $table->text('DapAnC');
            $table->text('DapAnD');
            $table->text('DapAnDung');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cau_hoi_bai_kiem_tra');
    }
}; 