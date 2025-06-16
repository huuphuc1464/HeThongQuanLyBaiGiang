<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\KhoaController;

Route::middleware('api')->group(function () {
    Route::post('/khoa/check-ten-khoa', [KhoaController::class, 'checkTenKhoa']);
}); 