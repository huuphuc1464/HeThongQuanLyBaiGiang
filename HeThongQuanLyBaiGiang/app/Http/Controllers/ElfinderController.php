<?php

namespace App\Http\Controllers;

use App\Models\FileBaiGiang;
use Illuminate\Http\Request;
use elFinder;
use elFinderConnector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ElfinderController extends Controller
{
    public static $tempUploadedFiles = [];

    public function connector(Request $request)
    {
        $maBaiGiang = preg_replace('/[^0-9]/', '', $request->query('maBaiGiang'));
        $maBai = preg_replace('/[^0-9]/', '', $request->query('maBai'));
        $maNguoiDung = Auth::id();
        if (!$maBaiGiang) {
            abort(400, 'Thiếu mã bài giảng');
        }

        // Tạo đường dẫn lưu file
        $folderName = $maBai
            ? "BaiGiang_{$maBaiGiang}/Bai_{$maBai}" // Sửa bài
            : "BaiGiang_{$maBaiGiang}/temp_{$maNguoiDung}_{$maBaiGiang}"; // Thêm bài

        $path = public_path("BaiGiang/{$folderName}");
        $url  = asset("BaiGiang/{$folderName}");

        Log::info("Elfinder path: {$path}");
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true);
        }

        $roots = [
            [
                'driver' => 'LocalFileSystem',
                'path' => $path,
                'URL' => $url,
                'accessControl' => function ($attr, $path, $data, $volume, $isDir, $relpath) {
                    return null;
                },
                'alias' => 'Tài liệu bài học',
                'attributes' => [
                    [
                        'pattern' => '/\.tmb$/',
                        'hidden' => true,
                    ]
                ]
            ]
        ];

        $opts = [
            'roots' => $roots
        ];

        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }

    public function popup(Request $request)
    {
        $type = $request->query('type', 'file');
        $maBaiGiang = $request->query('maBaiGiang');
        $maBai = $request->query('maBai');

        return view('elfinder.popup', compact('type', 'maBaiGiang', 'maBai'));
    }
}