<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VaiTroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vai_tro')->insert([
            [
                'TenVaiTro' => 'Quản trị viên',
                'MoTa' => 'Quản lý toàn bộ hệ thống',
                'TrangThai' => true,
            ],
            [
                'TenVaiTro' => 'Giảng viên',
                'MoTa' => 'Tạo và quản lý bài giảng, lớp học,...',
                'TrangThai' => true,
            ],
            [
                'TenVaiTro' => 'Sinh viên',
                'MoTa' => 'Tham gia lớp học, làm bài kiểm tra, xem bài giảng, học trực tuyến,...',
                'TrangThai' => true,
            ],
        ]);
    }
}
