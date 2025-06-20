<?php

namespace App\Http\Controllers;

use App\Models\FileBaiGiang;
use Illuminate\Http\Request;
use elFinder;
use elFinderConnector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ElfinderController extends Controller
{
    public static $tempUploadedFiles = [];

    public function connector(Request $request)
    {
        $maHocPhan = $request->query('maHocPhan');
        $maBaiGiang = $request->query('maBaiGiang');
        $maNguoiDung = Auth::id();
        if ($maBaiGiang) {
            // Sửa bài giảng
            $path = public_path("BaiGiang/HocPhan_{$maHocPhan}/{$maBaiGiang}");
            $url  = asset("BaiGiang/HocPhan_{$maHocPhan}/{$maBaiGiang}");
        } else {
            // Thêm bài giảng
            $path = public_path("BaiGiang/HocPhan_{$maHocPhan}/temp_{$maNguoiDung}");
            $url  = asset("BaiGiang/HocPhan_{$maHocPhan}/temp_{$maNguoiDung}");
        }

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $roots = [
            [
                'driver' => 'LocalFileSystem',
                'path' => $path,
                'URL' => $url,
                'accessControl' => function ($attr, $path, $data, $volume, $isDir, $relpath) {
                    return null;
                },
                'alias' => 'Tài liệu bài giảng',
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
}