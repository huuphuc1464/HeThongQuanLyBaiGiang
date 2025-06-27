<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function hienThiFormLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->MaVaiTro ?? $user->MaVaiTro;

            return match ((int) $role) {
                1 => redirect('/admin'),
                2 => redirect('/giang-vien'),
                3 => redirect('/')
            };
        }

        // Nếu chưa đăng nhập, hiển thị form đăng nhập
        return view('login.dangNhap');
    }

    public function dangNhap(Request $request)
    {
        $TenTaiKhoan = $request->input('TenTaiKhoan');
        $MatKhau = $request->input('MatKhau');

        if (empty($TenTaiKhoan) && empty($MatKhau)) {
            return back()->with('swal_error', 'Bạn chưa điền đầy đủ thông tin đăng nhập...');
        }

        if (empty($TenTaiKhoan)) {
            return back()->with('swal_warning', 'Tài khoản đang để trống...');
        }

        if (empty($MatKhau)) {
            return back()->with('swal_warning', 'Mật khẩu đang để trống...')->withInput();
        }

        $user = NguoiDung::where('TenTaiKhoan', $TenTaiKhoan)->where('TrangThai', 1)->first();

        if (!$user) {
            return back()->with('swal_error', 'Tài khoản không tồn tại...');
        }

        if (!Hash::check($MatKhau, $user->MatKhau)) {
            return back()->with('swal_error', 'Sai mật khẩu')->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->LanDauDangNhap) {
            return redirect()->route('changePassFirst.form', ['username' => $user->TenTaiKhoan])
                ->with('swal_success', 'Đăng nhập thành công. Vui lòng đổi mật khẩu trong lần đăng nhập đầu tiên');
        }

        $role = $user->MaVaiTro;

        if ($role == 1) {
            return redirect('/admin');
        } elseif ($role == 2) {
            return redirect('/giang-vien');
        }
        return redirect('/')->with('swal_success', 'Đăng nhập thành công');
    }

    public function dangXuat(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function hienThiFormKhoiPhuc()
    {
        return view('login.khoiPhucMatKhau');
    }

    public function guiOtpKhoiPhuc(Request $request, EmailService $emailService)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ],
            [
                'email.required' => 'Email không được để trống',
                'email.email' => 'Email không đúng định dạng'
            ]
        );

        if ($validator->fails()) {
            return back()->with('swal_error', $validator->errors()->first())->withInput();
        }

        $email = $request->email;
        $exists = NguoiDung::where('Email', $email)->where('TrangThai', 1)->exists();

        if (!$exists) {
            return back()->with('swal_error', 'Email không tồn tại trong hệ thống.');
        }

        $otp = rand(100000, 999999);
        $body = "
            <p>Xin chào,</p>
            <p>Bạn đã yêu cầu khôi phục mật khẩu. Mã OTP của bạn là:</p>
            <h2 style='color: #2d3748;'>$otp</h2>
            <p>Mã này có giá trị trong vòng 5 phút.</p>
            <p>Vui lòng không chia sẻ mã này với bất kỳ ai.</p>
            <p>Thân mến,<br>Hệ thống Quản lý Bài giảng</p>
        ";

        $success = $emailService->sendEmail($email, 'Mã OTP khôi phục mật khẩu', $body);

        if ($success) {
            Session::put('otp_data' . $email, [
                'email' => $email,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5)
            ]);
            return redirect()->route('otp.form', ['email' => $request->email])
                ->with('swal_success', 'OTP đã được gửi đến email.');
        }
        return back()->with('swal_error', 'Gửi email thất bại. Vui lòng thử lại sau.');
    }

    public function hienThiFormXacNhanOTP()
    {
        return view('login.xacNhanOTP');
    }

    public function xacNhanOTP(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'otp' => 'required|digits:6'
            ],
            [
                'email.required' => 'Email không được để trống',
                'email.email' => 'Email không đúng định dạng',
                'otp.required' => 'OTP không được để trống',
                'otp.digits' => 'OTP phải gồm đúng 6 chữ số'
            ]
        );

        if ($validator->fails()) {
            return back()->with('swal_error', $validator->errors()->first())->withInput();
        }

        $email = $request->email;
        $exists = NguoiDung::where('Email', $email)->where('TrangThai', 1)->exists();

        if (!$exists) {
            return back()->with('swal_error', 'Email không tồn tại trong hệ thống.');
        }

        $otpData = Session::get('otp_data' . $email);
        if (!$otpData || $otpData['email'] !== $email) {
            return back()->with('swal_error', 'Không tìm thấy OTP cho email này.');
        }

        if ($otpData['otp'] !== (int)$request->otp) {
            return back()->with('swal_error', 'OTP không chính xác.');
        }

        if (now()->gt($otpData['expires_at'])) {
            Session::forget('otp_data' . $email);
            return back()->with('swal_error', 'Mã OTP đã hết hạn.');
        }
        Session::forget('otp_data' . $email);
        return redirect()->route('resetPass.form', ['email' => $email])
            ->with('swal_success', 'Xác nhận OTP thành công.');
    }

    public function hienThiFormDatLaiMatKhau()
    {
        return view('login.datLaiMatKhau');
    }

    public function datLaiMatKhau(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'string',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^_-])[A-Za-z\d@$!%*#?&^_-]{8,}$/',
                'confirmed'
            ],
        ], [
            'password.required' => 'Mật khẩu không được để trống',
            'password.regex' => 'Mật khẩu phải có ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        if ($validator->fails()) {
            return back()->with('swal_error', $validator->errors()->first());
        }

        $user = NguoiDung::where('Email', $request->email)->where('TrangThai', 1)->first();

        if (!$user) {
            return back()->with('swal_error', 'Tài khoản không tồn tại.');
        }

        $user->MatKhau = Hash::make($request->password);
        $user->save();
        return redirect()->route('login')->with('swal_success', 'Mật khẩu đã được đặt lại. Vui lòng đăng nhập lại.');
    }

    public function hienThiFormDoiMatKhauLanDau()
    {
        return view('login.doiMatKhauLanDau');
    }

    public function doiMatKhauLanDau(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'string',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^_-])[A-Za-z\d@$!%*#?&^_-]{8,}$/',
                'confirmed'
            ],
        ], [
            'password.required' => 'Mật khẩu không được để trống',
            'password.regex' => 'Mật khẩu phải có ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        if ($validator->fails()) {
            return back()->with('swal_error', $validator->errors()->first());
        }

        $user = NguoiDung::where('TenTaiKhoan', $request->username)->where('TrangThai', 1)->first();

        if (!$user) {
            return back()->with('swal_error', 'Tài khoản không tồn tại.');
        }

        $user->MatKhau = Hash::make($request->password);
        $user->LanDauDangNhap = 0;
        $user->save();
        return redirect()->route('login');
    }

    public function doiMatKhau(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'oldPassword' => 'required',
            'newPassword' => [
                'required',
                'string',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^_-])[A-Za-z\d@$!%*#?&^_-]{8,}$/',
                'confirmed'
            ]
        ], [
            'oldPassword.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'newPassword.required' => 'Vui lòng nhập mật khẩu mới.',
            'newPassword.regex' => 'Mật khẩu phải có ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.',
            'newPassword.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        $userId = Auth::id();
        $user = NguoiDung::find($userId);
        if (!$user || !Hash::check($request->oldPassword, $user->MatKhau)) {
            return back()->withErrors(['oldPassword' => 'Mật khẩu hiện tại không đúng.']);
        }
        $user->MatKhau = Hash::make($request->newPassword);
        $user->save();

        return redirect()->back()->with('success', 'Đổi mật khẩu thành công.');
    }

    public function doiThongTin(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'HoTen' => 'required|string|max:100',
            'DiaChi' => 'required|string|max:255',
            'SoDienThoai' => [
                'required',
                'regex:/^0\d{9}$/',
                Rule::unique('nguoi_dung', 'SoDienThoai')->ignore(Auth::id(), 'MaNguoiDung')
            ],
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
            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'SoDienThoai.regex' => 'Số điện thoại không hợp lệ. Phải bắt đầu bằng số 0 và có 10 chữ số.',
            'SoDienThoai.unique' => 'Số điện thoại đã tồn tại.',
            'NgaySinh.required' => 'Vui lòng chọn ngày sinh.',
            'NgaySinh.date' => 'Ngày sinh không hợp lệ.',
            'NgaySinh.before_or_equal' => 'Bạn phải đủ 17 tuổi trở lên.',
            'NgaySinh.after_or_equal' => 'Ngày sinh không được trước năm 1950.',
            'GioiTinh.required' => 'Vui lòng chọn giới tính.',
            'GioiTinh.in' => 'Giới tính không hợp lệ.',
            'AnhDaiDien.image' => 'Tệp tải lên phải là hình ảnh.',
            'AnhDaiDien.mimes' => 'Ảnh chỉ chấp nhận tệp JPG, JPEG hoặc PNG.',
            'AnhDaiDien.max' => 'Ảnh không được lớn hơn 2MB.'
        ]);

        $user = NguoiDung::find(Auth::id());

        if (!$user) {
            return back()->withErrors(['user' => 'Không tìm thấy người dùng.']);
        }
        if ($request->hasFile('AnhDaiDien')) {
            $file = $request->file('AnhDaiDien');

            // Xóa ảnh cũ nếu tồn tại
            if ($user->AnhDaiDien && file_exists(public_path($user->AnhDaiDien))) {
                unlink(public_path($user->AnhDaiDien));
            }

            // Tạo tên file duy nhất
            $fileName = 'nguoidung_' . $user->MaNguoiDung . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Di chuyển ảnh vào thư mục public/AnhDaiDien/
            $file->move(public_path('AnhDaiDien'), $fileName);

            $user->AnhDaiDien = 'AnhDaiDien/' . $fileName;
        }

        $user->HoTen = preg_replace('/\s+/', ' ', trim($request->HoTen));
        $user->DiaChi = preg_replace('/\s+/', ' ', trim($request->DiaChi));
        $user->SoDienThoai = $request->SoDienThoai;
        $user->NgaySinh = $request->NgaySinh;
        $user->GioiTinh = preg_replace('/\s+/', ' ', trim($request->GioiTinh));
        $user->save();

        return back()->with('success', 'Cập nhật thông tin thành công.');
    }

    public function username()
    {
        return 'TenTaiKhoan';
    }
}