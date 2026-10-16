<?php

use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Halo Gas! Server Laravel backend berhasil terhubung ke Nuxt 🎉',
        'timestamp' => now()->toDateTimeString()
    ]);
});