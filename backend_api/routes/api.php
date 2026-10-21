<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\PengajuanController;
use Illuminate\Support\Facades\Route;

// Endpoint Autentikasi (Admin & Mahasiswa SSO Google)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google', [AuthController::class, 'googleLogin']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::get('/ping', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Halo Gas! Server Laravel backend berhasil terhubung ke Nuxt 🎉',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Endpoint Layanan Mahasiswa
Route::get('/layanan', [LayananController::class, 'index']);
Route::get('/layanan/{id}', [LayananController::class, 'show']);

// Endpoint Pengajuan Layanan
Route::post('/pengajuan', [PengajuanController::class, 'store']);
Route::get('/pengajuan/riwayat', [PengajuanController::class, 'index']);
Route::get('/pengajuan/stats', [PengajuanController::class, 'stats']);
Route::get('/pengajuan/lacak/{nomor_pelacakan}', [PengajuanController::class, 'lacak']);
Route::post('/pengajuan/{nomor_pelacakan}/unggah-revisi', [PengajuanController::class, 'unggahRevisi']);
Route::put('/pengajuan/{id}/status', [PengajuanController::class, 'updateStatus']);