<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhSachLopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('danh_sach_lop')->insert([
            [
                'MaLopHocPhan' => 1,
                'MaSinhVien' => 5,
                'TrangThai' => 1,
            ],
            [
                'MaLopHocPhan' => 2,
                'MaSinhVien' => 5,
                'TrangThai' => 1,
            ],
            [
                'MaLopHocPhan' => 3,
                'MaSinhVien' => 5,
                'TrangThai' => 1,
            ],
        ]);
    }
}