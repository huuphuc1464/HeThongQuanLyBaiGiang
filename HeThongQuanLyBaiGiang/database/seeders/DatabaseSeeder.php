<?php

namespace Database\Seeders;

use App\Models\LopHocPhan;
use App\Models\SinhVien;
use App\Models\User;
use App\Models\VaiTro;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            VaiTroSeeder::class,
            NguoiDungSeeder::class,
            SinhVienSeeder::class,
            KhoaSeeder::class,
        ]);
    }
}
