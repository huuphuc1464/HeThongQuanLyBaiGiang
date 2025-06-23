<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class BinhLuan extends Component
{
    public $baiGiang;
    public $binhLuans;

    public function __construct($baiGiang)
    {
        $this->baiGiang = $baiGiang;

        $this->binhLuans = DB::table('binh_luan_bai_giang as bl')
            ->join('nguoi_dung as nd', 'nd.MaNguoiDung', '=', 'bl.MaNguoiGui')
            ->where('bl.MaBaiGiang', $baiGiang->MaBaiGiang)
            ->whereNull('bl.MaBinhLuanCha')
            ->select('bl.*', 'nd.HoTen', 'nd.AnhDaiDien')
            ->orderBy('bl.created_at', 'desc')
            ->get()
            ->map(function ($bl) {
                $bl->traLoi = DB::table('binh_luan_bai_giang as tl')
                    ->join('nguoi_dung as nd', 'nd.MaNguoiDung', '=', 'tl.MaNguoiGui')
                    ->where('tl.MaBinhLuanCha', $bl->MaBinhLuan)
                    ->select('tl.*', 'nd.HoTen', 'nd.AnhDaiDien')
                    ->orderBy('tl.created_at')
                    ->get();
                return $bl;
            });
    }

    public function render()
    {
        return view('components.binh-luan');
    }
}
