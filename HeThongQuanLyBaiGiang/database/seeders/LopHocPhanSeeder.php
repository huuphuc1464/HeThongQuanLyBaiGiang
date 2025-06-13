<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LopHocPhanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lop_hoc_phan')->insert([
            [
                'MaHocPhan' => 1,
                'MaNguoiTao' => 3,
                'TenLopHocPhan' => 'Lớp CDTH22WEBC - ASP.NET Core',
                'TrangThai' => 1,
            ],
            [
                'MaHocPhan' => 2,
                'MaNguoiTao' => 3,
                'TenLopHocPhan' => 'Lớp CDTH22WEBC - Laravel',
                'TrangThai' => 1,
            ],
            [
                'MaHocPhan' => 1,
                'MaNguoiTao' => 3,
                'TenLopHocPhan' => 'Lớp CDTH22WEBB - ASP.NET Core',
                'TrangThai' => 1,
            ],
        ]);
    }
}