<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BaiKiemTraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Thêm bài kiểm tra
        $baiKiemTra1 = DB::table('bai_kiem_tra')->insertGetId([
            'MaLopHocPhan' => 1,
            'MaGiangVien' => 3,
            'TenBaiKiemTra' => 'Kiểm tra giữa kỳ ASP.NET Core',
            'ThoiGianBatDau' => Carbon::now(),
            'ThoiGianKetThuc' => Carbon::now()->addDays(7),
            'ThoiGianLamBai' => 45, // 45 phút
            'ChoPhepXemKetQua' => true, // Cho phép xem kết quả
            'MoTa' => 'Bài kiểm tra về các kiến thức cơ bản của ASP.NET Core',
            'TrangThai' => 1,
            'created_at' => now(),
        ]);

        $baiKiemTra2 = DB::table('bai_kiem_tra')->insertGetId([
            'MaLopHocPhan' => 2,
            'MaGiangVien' => 3,
            'TenBaiKiemTra' => 'Kiểm tra giữa kỳ Laravel',
            'ThoiGianBatDau' => Carbon::now(),
            'ThoiGianKetThuc' => Carbon::now()->addDays(7),
            'ThoiGianLamBai' => 60, // 60 phút
            'ChoPhepXemKetQua' => false, // Không cho phép xem kết quả
            'MoTa' => 'Bài kiểm tra về các kiến thức cơ bản của Laravel',
            'TrangThai' => 1,
            'created_at' => now(),
        ]);

        // Thêm câu hỏi cho bài kiểm tra 1
        DB::table('cau_hoi_bai_kiem_tra')->insert([
            [
                'MaBaiKiemTra' => $baiKiemTra1,
                'CauHoi' => 'ASP.NET Core là gì?',
                'DapAnA' => 'Một framework web mã nguồn mở của Microsoft',
                'DapAnB' => 'Một ngôn ngữ lập trình',
                'DapAnC' => 'Một hệ quản trị cơ sở dữ liệu',
                'DapAnD' => 'Một hệ điều hành',
                'DapAnDung' => 'A',
                'created_at' => now(),
            ],
            [
                'MaBaiKiemTra' => $baiKiemTra1,
                'CauHoi' => 'Middleware trong ASP.NET Core được sử dụng để làm gì?',
                'DapAnA' => 'Để xử lý cơ sở dữ liệu',
                'DapAnB' => 'Để xử lý request và response trong pipeline',
                'DapAnC' => 'Để tạo giao diện người dùng',
                'DapAnD' => 'Để gửi email',
                'DapAnDung' => 'B',
                'created_at' => now(),
            ],
        ]);

        // Thêm câu hỏi cho bài kiểm tra 2
        DB::table('cau_hoi_bai_kiem_tra')->insert([
            [
                'MaBaiKiemTra' => $baiKiemTra2,
                'CauHoi' => 'Laravel là gì?',
                'DapAnA' => 'Một framework PHP',
                'DapAnB' => 'Một ngôn ngữ lập trình',
                'DapAnC' => 'Một hệ quản trị cơ sở dữ liệu',
                'DapAnD' => 'Một text editor',
                'DapAnDung' => 'A',
                'created_at' => now(),
            ],
            [
                'MaBaiKiemTra' => $baiKiemTra2,
                'CauHoi' => 'Artisan trong Laravel là gì?',
                'DapAnA' => 'Một thư viện JavaScript',
                'DapAnB' => 'Một công cụ command line interface',
                'DapAnC' => 'Một template engine',
                'DapAnD' => 'Một package manager',
                'DapAnDung' => 'B',
                'created_at' => now(),
            ],
        ]);
    }
}
