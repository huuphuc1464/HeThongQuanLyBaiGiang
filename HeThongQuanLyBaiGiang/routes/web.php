<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\Elfinder\ElfinderController;

Route::get('/', function () {
    return view('welcome');
});

Route::any('elfinder/connector', [ElfinderController::class, 'showConnector'])->name('elfinder.connector');

Route::get('/editor', function () {
    return view('editor');
});
