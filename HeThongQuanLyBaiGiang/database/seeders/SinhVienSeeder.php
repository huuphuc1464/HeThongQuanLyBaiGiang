<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SinhVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sinh_vien')->insert([
            [
                'MaNguoiDung' => 5,
                'MSSV' => '0306221464',
            ],
            [
                'MaNguoiDung' => 6,
                'MSSV' => '0306221407',
            ],
        ]);
    }
}
