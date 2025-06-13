<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonHocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mon_hoc')->insert([
            [
                'MaKhoa' => 1,
                'TenMonHoc' => 'Lập trình ASP.NET Core',
                'TrangThai' => 1,
            ],
            [
                'MaKhoa' => 1,
                'TenMonHoc' => 'Lập trình Laravel',
                'TrangThai' => 1,
            ],
            [
                'MaKhoa' => 1,
                'TenMonHoc' => 'Lập trình Python',
                'TrangThai' => 1,
            ]
        ]);
    }
}