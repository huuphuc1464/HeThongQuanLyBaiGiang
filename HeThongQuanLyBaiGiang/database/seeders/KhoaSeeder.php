<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('khoa')->insert([
            [
                'TenKhoa' => 'Công nghệ thông tin',
                'TrangThai' => 1,
            ],
            [
                'TenKhoa' => 'Cơ khí',
                'TrangThai' => 1,
            ],
            [
                'TenKhoa' => 'Cơ khí - Động lực',
                'TrangThai' => 1,
            ],
            [
                'TenKhoa' => 'Điện - Điện tử',
                'TrangThai' => 1,
            ],
            [
                'TenKhoa' => 'Công nghệ Nhiệt - Lạnh',
                'TrangThai' => 1,
            ],
            [
                'TenKhoa' => 'Giáo dục đại cương',
                'TrangThai' => 1,
            ],
            [
                'TenKhoa' => 'Kế toán',
                'TrangThai' => 1,
            ]
        ]);
    }
}