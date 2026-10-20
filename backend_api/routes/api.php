<?php

use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\PengajuanController;
use Illuminate\Support\Facades\Route;

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