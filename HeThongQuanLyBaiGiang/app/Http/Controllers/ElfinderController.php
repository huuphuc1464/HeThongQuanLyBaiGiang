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
        $path = public_path("BaiGiang/HocPhan_$maHocPhan");
        $url  = asset("BaiGiang/HocPhan_$maHocPhan");

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $roots = [
            [
                'driver' => 'LocalFileSystem',
                'path'   => $path,
                'URL'    => $url,
                'accessControl' => function ($attr, $path, $data, $volume, $isDir, $relpath) {
                    return null;
                },
                'alias'  => "Tài liệu bài giảng",
                'attributes' => [
                    [
                        'pattern' => '/\.tmb$/',
                        'hidden'  => true,
                    ]
                ]
            ]
        ];

        $opts = [
            'roots' => $roots,
            'bind' => [
                'upload' => [$this, 'onUploadSave']
            ]
        ];

        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }


    public function onUploadSave($cmd, &$result, $args, $elfinder)
    {
        $userId = Auth::id();
        $jsonPath = storage_path("app/file_bai_giang/{$userId}.json");

        // Khởi tạo file nếu chưa có
        $data = file_exists($jsonPath)
            ? json_decode(file_get_contents($jsonPath), true)
            : ['maNguoiDung' => $userId, 'tenFile' => []];

        // Kiểm tra giá trị trong $result
        if (!empty($result['added']) && is_array($result['added'])) {
            foreach ($result['added'] as $file) {
                if (is_array($file) && !empty($file['url'])) {
                    $path = ltrim(parse_url($file['url'], PHP_URL_PATH), '/');
                    if (!in_array($path, $data['tenFile'])) {
                        $data['tenFile'][] = $path;
                    }
                }
            }
        }

        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists(dirname($jsonPath))) {
            mkdir(dirname($jsonPath), 0777, true);
        }

        Log::info('onUploadSave(): ghi JSON', ['data' => $data]);
        file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
