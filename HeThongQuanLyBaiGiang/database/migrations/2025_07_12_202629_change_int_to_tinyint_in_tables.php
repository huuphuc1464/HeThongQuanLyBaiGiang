<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('bai', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });

        Schema::table('bai_giang', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });
        Schema::table('bai_kiem_tra', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });
        Schema::table('chuong', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });
        Schema::table('danh_sach_lop', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });
        Schema::table('file_bai_giang', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });
        Schema::table('khoa', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });
        Schema::table('lop_hoc_phan', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });
        Schema::table('vai_tro', function (Blueprint $table) {
            $table->boolean('TrangThai')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });

        Schema::table('bai_giang', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });
        Schema::table('bai_kiem_tra', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });
        Schema::table('chuong', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });
        Schema::table('danh_sach_lop', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });
        Schema::table('file_bai_giang', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });
        Schema::table('khoa', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });
        Schema::table('lop_hoc_phan', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });
        Schema::table('vai_tro', function (Blueprint $table) {
            $table->integer('TrangThai')->change();
        });
    }
};