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
        // Tạo bảng upvote cho bình luận
        Schema::create('binh_luan_upvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('MaBinhLuan')->constrained('binh_luan_bai_giang', 'MaBinhLuan')->onDelete('cascade');
            $table->foreignId('MaNguoiDung')->constrained('nguoi_dung', 'MaNguoiDung')->onDelete('cascade');
            $table->enum('LoaiUpvote', ['upvote', 'downvote'])->default('upvote');
            $table->timestamps();

            // Đảm bảo mỗi người dùng chỉ có thể upvote/downvote một lần cho mỗi bình luận
            $table->unique(['MaBinhLuan', 'MaNguoiDung']);
        });

        // Cập nhật bảng bình luận hiện tại
        Schema::table('binh_luan_bai_giang', function (Blueprint $table) {
            // Thay đổi kiểu dữ liệu của NoiDung để hỗ trợ rich text
            $table->longText('NoiDung')->change();

            // Thêm các trường mới
            $table->integer('SoUpvote')->default(0);
            $table->integer('SoDownvote')->default(0);
            $table->boolean('DaAn')->default(false);
            $table->timestamp('ThoiGianChinhSua')->nullable();
            $table->string('LyDoChinhSua', 255)->nullable();

            // Thêm index để tối ưu hiệu suất
            $table->index(['MaBai', 'MaBinhLuanCha']);
            $table->index(['SoUpvote', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binh_luan_upvotes');

        Schema::table('binh_luan_bai_giang', function (Blueprint $table) {
            $table->string('NoiDung', 255)->change();
            $table->dropColumn(['SoUpvote', 'SoDownvote', 'DaAn', 'ThoiGianChinhSua', 'LyDoChinhSua']);
            $table->dropIndex(['MaBai', 'MaBinhLuanCha']);
            $table->dropIndex(['SoUpvote', 'created_at']);
        });
    }
};
