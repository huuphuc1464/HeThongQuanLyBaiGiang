<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GiangVienController extends Controller
{
    public function danhSachGiangVien(Request $request)
    {
        //Multi-keyword Search (tách từ khóa để tìm)
        $query = DB::table('nguoi_dung')
            ->where('MaVaiTro', 2); // Giảng viên

        if ($request->filled('search')) {
            $keywords = preg_split('/\s+/', trim($request->search));

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = strtolower($kw);
                    $q->orWhereRaw('LOWER(HoTen) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(Email) LIKE ?', ["%$kw%"])
                        ->orWhereRaw('LOWER(SoDienThoai) LIKE ?', ["%$kw%"]);
                }
            });
        }
        $query->orderBy('MaNguoiDung'); // tăng dần
        $query->orderBy('HoTen');

        $danhSachGiangVien = $query->paginate(10)->withQueryString();

        return view('admin.quanLyGiangVien.danhSachGiangVien', compact('danhSachGiangVien'));
    }

    public function xoaGiangVien($id)
    {
        $giangVien = NguoiDung::where('MaNguoiDung', $id)
            ->where('MaVaiTro', 2)
            ->firstOrFail();

        $giangVien->TrangThai = 0;
        $giangVien->save();

        return redirect()->back()->with('success', 'Xóa giảng viên thành công.');
    }

    public function khoiPhucGiangVien($id)
    {
        $giangVien = NguoiDung::where('MaNguoiDung', $id)
            ->where('MaVaiTro', 2)
            ->firstOrFail();

        $giangVien->TrangThai = 1;
        $giangVien->save();

        return redirect()->back()->with('success', 'Khôi phục giảng viên thành công.');
    }

    public function hienFormThemGiangVien()
    {
        return view('admin.quanLyGiangVien.themGiangVien');
    }

    public function hienFormSuaGiangVien($id)
    {
        $giangVien = NguoiDung::findOrFail($id);
        return view('admin.quanLyGiangVien.suaGiangVien', compact('giangVien'));
    }

    public function themGiangVien(Request $request, EmailService $emailService)
    {
        $validated = $request->validate([
            'HoTen' => 'required|string|max:100',
            'Email' => 'required|email|max:100|unique:nguoi_dung,Email',
            'DiaChi' => 'required|string|max:255',
            'SoDienThoai' => 'required|regex:/^0\d{9}$/|unique:nguoi_dung,SoDienThoai',
            'NgaySinh' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(17)->format('Y-m-d'),
                'after_or_equal:1950-01-01',
            ],
            'GioiTinh' => 'required|in:Nam,Nữ',
            'AnhDaiDien' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'HoTen.required' => 'Vui lòng nhập họ và tên.',
            'HoTen.max' => 'Họ tên không được vượt quá 100 ký tự.',
            'Email.required' => 'Vui lòng nhập email.',
            'Email.max' => 'Email không được vượt quá 100 ký tự.',
            'Email.email' => 'Email không đúng định dạng.',
            'Email.unique' => 'Email đã tồn tại.',
            'DiaChi.required' => 'Vui lòng nhập địa chỉ thường trú.',
            'DiaChi.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'SoDienThoai.regex' => 'Số điện thoại phải bắt đầu bằng số 0 và gồm 10 chữ số.',
            'SoDienThoai.unique' => 'Số điện thoại đã tồn tại.',
            'NgaySinh.required' => 'Vui lòng chọn ngày sinh.',
            'NgaySinh.date' => 'Ngày sinh không hợp lệ.',
            'NgaySinh.before_or_equal' => 'Giảng viên phải đủ 17 tuổi trở lên.',
            'NgaySinh.after_or_equal' => 'Ngày sinh không được trước năm 1950.',
            'GioiTinh.required' => 'Vui lòng chọn giới tính.',
            'GioiTinh.in' => 'Giới tính không hợp lệ.',
            'AnhDaiDien.image' => 'Tệp tải lên phải là hình ảnh.',
            'AnhDaiDien.mimes' => 'Chỉ chấp nhận tệp JPG, JPEG hoặc PNG.',
            'AnhDaiDien.max' => 'Ảnh không được lớn hơn 2MB.',
        ]);


        $user = NguoiDung::create([
            'TenTaiKhoan' => $validated['Email'],
            'HoTen' => $validated['HoTen'],
            'Email' => $validated['Email'],
            'DiaChi' => $validated['DiaChi'],
            'SoDienThoai' => $validated['SoDienThoai'],
            'NgaySinh' => $validated['NgaySinh'],
            'GioiTinh' => $validated['GioiTinh'],
            'TenTaiKhoan' => $validated['Email'],
            'MatKhau' => Hash::make($validated['SoDienThoai']),
            'MaVaiTro' => 2,
            'LanDauDangNhap' => 1,
            'TrangThai' => 1,
        ]);

        if ($request->hasFile('AnhDaiDien')) {
            $file = $request->file('AnhDaiDien');
            $fileName = 'nguoidung_' . $user->MaNguoiDung . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('AnhDaiDien'), $fileName);
            $user->AnhDaiDien = 'AnhDaiDien/' . $fileName;
            $user->save();
        }

        $subject = 'Tài khoản giảng viên trên hệ thống Quản lý bài giảng';
        $body = '
            <div style="font-family: Arial, sans-serif; font-size: 15px; color: #333;">
                <p>Xin chào <strong>' . $user->HoTen . '</strong>,</p>
                <p>Hệ thống Quản lý bài giảng xin thông báo rằng tài khoản giảng viên của bạn đã được khởi tạo thành công. Dưới đây là thông tin đăng nhập:</p>
                <table cellpadding="6" cellspacing="0" border="0" style="background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                    <tr>
                        <td><strong>Tên đăng nhập (email):</strong></td>
                        <td>' . $user->Email . '</td>
                    </tr>
                    <tr>
                        <td><strong>Mật khẩu tạm thời:</strong></td>
                        <td>' . $user->SoDienThoai . '</td>
                    </tr>
                </table>
                <p><strong>Lưu ý:</strong> Đây là mật khẩu tạm thời. Vui lòng đăng nhập và thay đổi mật khẩu ngay lần đầu tiên sử dụng để đảm bảo bảo mật thông tin.</p>
                <p>Nếu bạn có bất kỳ thắc mắc hoặc cần hỗ trợ, xin vui lòng liên hệ với quản trị viên hệ thống.</p>
                <p style="margin-top: 30px;">Trân trọng,<br>
                <strong>Hệ thống Quản lý bài giảng</strong></p>
            </div>
        ';

        $success = $emailService->sendEmail($user->Email, $subject, $body);
        if ($success) {
            return redirect()->route('admin.giang-vien.danh-sach')->with('success', 'Thêm giảng viên thành công.');
        } else {
            return redirect()->route('admin.giang-vien.danh-sach')->with('success', 'Thêm giảng viên thành công, nhưng xảy ra lỗi khi gửi thông tin tài khoản đến email' . $user->Email);
        }
    }

    public function capNhatGiangVien(Request $request, $id)
    {
        $giangVien = NguoiDung::where('MaNguoiDung', $id)
            ->where('MaVaiTro', 2)
            ->firstOrFail();

        $validated = $request->validate([
            'HoTen' => 'required|string|max:100',
            'DiaChi' => 'required|string|max:255',
            'NgaySinh' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(17)->format('Y-m-d'),
                'after_or_equal:1950-01-01',
            ],
            'GioiTinh' => 'required|in:Nam,Nữ',
            'AnhDaiDien' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'HoTen.required' => 'Vui lòng nhập họ và tên.',
            'HoTen.max' => 'Họ tên không được vượt quá 100 ký tự.',
            'DiaChi.required' => 'Vui lòng nhập địa chỉ thường trú.',
            'DiaChi.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'NgaySinh.required' => 'Vui lòng chọn ngày sinh.',
            'NgaySinh.date' => 'Ngày sinh không hợp lệ.',
            'NgaySinh.before_or_equal' => 'Bạn phải đủ 17 tuổi trở lên.',
            'NgaySinh.after_or_equal' => 'Ngày sinh không được trước năm 1950.',
            'GioiTinh.required' => 'Vui lòng chọn giới tính.',
            'GioiTinh.in' => 'Giới tính không hợp lệ.',
            'AnhDaiDien.image' => 'Tệp tải lên phải là hình ảnh.',
            'AnhDaiDien.mimes' => 'Chỉ chấp nhận tệp JPG, JPEG hoặc PNG.',
            'AnhDaiDien.max' => 'Ảnh không được lớn hơn 2MB.',
        ]);

        if ($request->hasFile('AnhDaiDien')) {
            $file = $request->file('AnhDaiDien');
            $fileName = 'nguoidung_' . $giangVien->MaNguoiDung . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('AnhDaiDien'), $fileName);

            if ($giangVien->AnhDaiDien && file_exists(public_path('AnhDaiDien/' . $giangVien->AnhDaiDien))) {
                unlink(public_path('AnhDaiDien/' . $giangVien->AnhDaiDien));
            }
            $giangVien->AnhDaiDien = 'AnhDaiDien/' . $fileName;
        }

        $giangVien->HoTen = $validated['HoTen'];
        $giangVien->DiaChi = $validated['DiaChi'];
        $giangVien->NgaySinh = $validated['NgaySinh'];
        $giangVien->GioiTinh = $validated['GioiTinh'];
        $giangVien->save();

        return redirect()->route('admin.giang-vien.danh-sach')->with('success', 'Cập nhật giảng viên thành công.');
    }
}
