<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HocPhanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hoc_phan')->insert([
            [
                'MaMonHoc' => 1,
                'MaNguoiTao' => 3,
                'TenHocPhan' => 'Lập trình Web ASP.NET Core',
                'MoTa' => 'Học về framework ASP.NET',
                'AnhHocPhan' => 'https://placehold.co/25',
                'TrangThai' => 1,
            ],
            [
                'MaMonHoc' => 2,
                'MaNguoiTao' => 3,
                'TenHocPhan' => 'Lập trình Web Laravel',
                'MoTa' => 'Học về framework Laravel',
                'AnhHocPhan' => 'https://placehold.co/25',
                'TrangThai' => 1,
            ],
            [
                'MaMonHoc' => 3,
                'MaNguoiTao' => 4,
                'TenHocPhan' => 'Lập trình Python',
                'MoTa' => 'Khái niệm class, object, kế thừa trong Python.',
                'AnhHocPhan' => 'https://placehold.co/25',
                'TrangThai' => 1,
            ],
        ]);
    }
}