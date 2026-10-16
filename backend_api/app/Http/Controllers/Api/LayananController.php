<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Mengambil daftar semua layanan.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Layanan::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_layanan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $layanan = $query->withCount('pengajuan')->orderBy('id', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'total' => $layanan->count(),
            'data' => $layanan,
        ]);
    }

    /**
     * Mengambil detail layanan berdasarkan ID atau Slug.
     */
    public function show(string|int $id): JsonResponse
    {
        $layanan = is_numeric($id)
            ? Layanan::find($id)
            : Layanan::where('slug', $id)->first();

        if (! $layanan && is_numeric($id)) {
            $layanan = Layanan::where('slug', (string) $id)->first();
        }

        if (! $layanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $layanan,
        ]);
    }
}

