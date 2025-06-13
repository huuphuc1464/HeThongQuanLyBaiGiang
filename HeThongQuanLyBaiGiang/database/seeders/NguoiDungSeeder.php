<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nguoi_dung')->insert([
            [
                'MaVaiTro' => 1, // admin
                'TenTaiKhoan' => 'huuphuc1702@gmail.com',
                'MatKhau' => bcrypt('Admin123@'),
                'Email' => 'huuphuc1702@gmail.com',
                'HoTen' => 'Trần Hửu Phúc',
                'SoDienThoai' => '0901234567',
                'AnhDaiDien' => null,
                'DiaChi' => 'Long An',
                'NgaySinh' => '2004-02-17',
                'GioiTinh' => 'Nam',
                'LanDauDangNhap' => true,
                'TrangThai' => true,
            ],
            [
                'MaVaiTro' => 1, // admin
                'TenTaiKhoan' => 'duynvdd2424@gmail.com',
                'MatKhau' => bcrypt('Admin123@'),
                'Email' => 'duynvdd2424@gmail.com',
                'HoTen' => 'Ngô Võ Đức Duy',
                'SoDienThoai' => '0912345678',
                'AnhDaiDien' => null,
                'DiaChi' => 'TP.HCM',
                'NgaySinh' => '2004-01-01',
                'GioiTinh' => 'Nam',
                'LanDauDangNhap' => true,
                'TrangThai' => true,
            ],
            [
                'MaVaiTro' => 2, // teacher
                'TenTaiKhoan' => '0306221464@caothang.edu.vn',
                'MatKhau' => Hash::make('Teacher123@'),
                'Email' => '0306221464@caothang.edu.vn',
                'HoTen' => 'Trần Hửu Phúc',
                'SoDienThoai' => '0901234566',
                'AnhDaiDien' => null,
                'DiaChi' => 'Long An',
                'NgaySinh' => '2004-02-17',
                'GioiTinh' => 'Nam',
                'LanDauDangNhap' => true,
                'TrangThai' => true,
            ],
            [
                'MaVaiTro' => 2, // Teacher
                'TenTaiKhoan' => '0306221407@caothang.edu.vn',
                'MatKhau' => Hash::make('Teacher123@'),
                'Email' => '0306221407@caothang.edu.vn',
                'HoTen' => 'Ngô Võ Đức Duy',
                'SoDienThoai' => '0912345677',
                'AnhDaiDien' => null,
                'DiaChi' => 'TP.HCM',
                'NgaySinh' => '2004-01-01',
                'GioiTinh' => 'Nam',
                'LanDauDangNhap' => true,
                'TrangThai' => true,
            ],
            [
                'MaVaiTro' => 3, // student
                'TenTaiKhoan' => 'sv1@caothang.edu.vn',
                'MatKhau' => bcrypt('Student123@'),
                'Email' => 'sv1@caothang.edu.vn',
                'HoTen' => 'Trần Hửu Phúc',
                'SoDienThoai' => '0901234444',
                'AnhDaiDien' => null,
                'DiaChi' => 'Long An',
                'NgaySinh' => '2004-02-17',
                'GioiTinh' => 'Nam',
                'LanDauDangNhap' => true,
                'TrangThai' => true,
            ],
            [
                'MaVaiTro' => 3, // student
                'TenTaiKhoan' => 'sv2@caothang.edu.vn',
                'MatKhau' => bcrypt('Student123@'),
                'Email' => 'sv2@caothang.edu.vn',
                'HoTen' => 'Ngô Võ Đức Duy',
                'SoDienThoai' => '0912348888',
                'AnhDaiDien' => null,
                'DiaChi' => 'TP.HCM',
                'NgaySinh' => '2004-01-01',
                'GioiTinh' => 'Nam',
                'LanDauDangNhap' => true,
                'TrangThai' => true,
            ]
        ]);
    }
}