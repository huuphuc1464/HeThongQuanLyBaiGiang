<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function hienThiFormLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->MaVaiTro ?? $user->MaVaiTro;

            return match ((int) $role) {
                1 => redirect('/admin'),
                2 => redirect('/teacher'),
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
            return back()->with('swal_warning', 'Mật khẩu đang để trống...');
        }

        $user = NguoiDung::where('TenTaiKhoan', $TenTaiKhoan)->first();

        if (!$user) {
            return back()->with('swal_error', 'Tài khoản không tồn tại...');
        }

        if (!Hash::check($MatKhau, $user->MatKhau)) {
            return back()->with('swal_error', 'Sai mật khẩu');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $role = $user->MaVaiTro;

        if ($role == 1) {
            return redirect('/admin');
        } elseif ($role == 2) {
            return redirect('/teacher');
        } elseif ($role == 3) {
            return redirect('/');
        }

        return redirect('/')->with('swal_success', 'Đăng nhập thành công');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/dang-nhap');
    }

    public function hienThiFormKhoiPhuc()
    {
        return view('login.khoiPhucMatKhau');
    }

    public function hienThiFormDatLaiMatKhau()
    {
        return view('login.datLaiMatKhau');
    }

    public function username()
    {
        return 'TenTaiKhoan';
    }
}