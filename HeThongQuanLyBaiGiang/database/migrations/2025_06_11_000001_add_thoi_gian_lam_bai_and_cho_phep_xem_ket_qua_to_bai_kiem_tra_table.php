<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bai_kiem_tra', function (Blueprint $table) {
            // Thêm trường thời gian làm bài (tính bằng phút)
            $table->integer('ThoiGianLamBai')->after('ThoiGianKetThuc')->default(60);

            // Thêm trường cho phép xem kết quả
            $table->boolean('ChoPhepXemKetQua')->after('ThoiGianLamBai')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_kiem_tra', function (Blueprint $table) {
            $table->dropColumn('ThoiGianLamBai');
            $table->dropColumn('ChoPhepXemKetQua');
        });
    }
};
