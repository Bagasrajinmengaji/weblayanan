<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Pengajuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengajuanController extends Controller
{
    /**
     * Mengambil riwayat pengajuan (support filtering & search).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Pengajuan::with('layanan')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('nim')) {
            $query->where('nim', $request->input('nim'));
        }

        if ($request->filled('email')) {
            $query->where('email', $request->input('email'));
        }

        if ($request->filled('layanan_id')) {
            $query->where('layanan_id', $request->input('layanan_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pelacakan', 'like', "%{$search}%")
                  ->orWhere('nama_mahasiswa', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $pengajuan = $query->get();

        return response()->json([
            'status' => 'success',
            'total' => $pengajuan->count(),
            'data' => $pengajuan,
        ]);
    }

    /**
     * Menyimpan pengajuan baru (mengembalikan nomor_pelacakan unik).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'layanan_id' => 'required|integer|exists:layanan,id',
            'nama_mahasiswa' => 'required|string|max:255',
            'nim' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:30',
            'catatan_revisi' => 'nullable|string',
            'berkas_persyaratan' => 'nullable',
        ]);

        // Generate nomor pelacakan unik format REQ-YYYYMMDD-XXXX
        $nomorPelacakan = $this->generateNomorPelacakan();

        // Handle berkas persyaratan (baik format file upload multipart maupun array/json payload)
        $berkasData = [];

        if ($request->hasFile('berkas_persyaratan')) {
            $files = $request->file('berkas_persyaratan');
            if (is_array($files)) {
                foreach ($files as $key => $file) {
                    $path = $file->store('uploads/pengajuan', 'public');
                    $berkasData[$key] = [
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_url' => asset('storage/' . $path),
                    ];
                }
            } else {
                $path = $files->store('uploads/pengajuan', 'public');
                $berkasData[] = [
                    'file_name' => $files->getClientOriginalName(),
                    'file_path' => $path,
                    'file_url' => asset('storage/' . $path),
                ];
            }
        } elseif ($request->filled('berkas_persyaratan')) {
            $rawBerkas = $request->input('berkas_persyaratan');
            if (is_string($rawBerkas)) {
                $decoded = json_decode($rawBerkas, true);
                $berkasData = json_last_error() === JSON_ERROR_NONE ? $decoded : ['url' => $rawBerkas];
            } elseif (is_array($rawBerkas)) {
                $berkasData = $rawBerkas;
            }
        }

        $pengajuan = Pengajuan::create([
            'nomor_pelacakan' => $nomorPelacakan,
            'layanan_id' => $validated['layanan_id'],
            'nama_mahasiswa' => $validated['nama_mahasiswa'],
            'nim' => $validated['nim'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'berkas_persyaratan' => $berkasData,
            'status' => 'Pending',
            'catatan_revisi' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan layanan berhasil disimpan.',
            'nomor_pelacakan' => $nomorPelacakan,
            'data' => $pengajuan->load('layanan'),
        ], 201);
    }

    /**
     * Mengecek status pengajuan & catatan revisi berdasarkan nomor pelacakan.
     */
    public function lacak(string $nomor_pelacakan): JsonResponse
    {
        $nomorPelacakanClean = trim($nomor_pelacakan);

        $pengajuan = Pengajuan::with('layanan')
            ->where('nomor_pelacakan', $nomorPelacakanClean)
            ->first();

        if (! $pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => "Nomor pelacakan '{$nomorPelacakanClean}' tidak ditemukan. Pastikan nomor pelacakan sudah sesuai.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pengajuan,
        ]);
    }

    /**
     * Mengupdate status pengajuan & catatan revisi (untuk admin / proses perbaikan).
     */
    public function updateStatus(Request $request, string|int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Diproses,Perlu Perbaikan,Disetujui,Ditolak',
            'catatan_revisi' => 'nullable|string',
        ]);

        $pengajuan = is_numeric($id)
            ? Pengajuan::find($id)
            : Pengajuan::where('nomor_pelacakan', $id)->first();

        if (! $pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengajuan tidak ditemukan.',
            ], 404);
        }

        $pengajuan->status = $validated['status'];
        if (array_key_exists('catatan_revisi', $validated)) {
            $pengajuan->catatan_revisi = $validated['catatan_revisi'];
        }
        $pengajuan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status pengajuan berhasil diperbarui.',
            'data' => $pengajuan->load('layanan'),
        ]);
    }

    /**
     * Mengunggah berkas perbaikan untuk pengajuan yang berstatus 'Perlu Perbaikan'.
     */
    public function unggahRevisi(Request $request, string $nomor_pelacakan): JsonResponse
    {
        $pengajuan = Pengajuan::where('nomor_pelacakan', trim($nomor_pelacakan))->first();

        if (! $pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengajuan tidak ditemukan.',
            ], 404);
        }

        $request->validate([
            'berkas_perbaikan' => 'required',
            'catatan_pemohon' => 'nullable|string|max:500',
        ]);

        $berkasLama = is_array($pengajuan->berkas_persyaratan) ? $pengajuan->berkas_persyaratan : [];
        $berkasBaru = [];

        if ($request->hasFile('berkas_perbaikan')) {
            $files = $request->file('berkas_perbaikan');
            if (is_array($files)) {
                foreach ($files as $file) {
                    $path = $file->store('uploads/pengajuan', 'public');
                    $berkasBaru[] = [
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_url' => asset('storage/' . $path),
                        'is_revision' => true,
                        'uploaded_at' => now()->toIso8601String(),
                    ];
                }
            } else {
                $path = $files->store('uploads/pengajuan', 'public');
                $berkasBaru[] = [
                    'file_name' => $files->getClientOriginalName(),
                    'file_path' => $path,
                    'file_url' => asset('storage/' . $path),
                    'is_revision' => true,
                    'uploaded_at' => now()->toIso8601String(),
                ];
            }
        }

        $pengajuan->berkas_persyaratan = array_merge($berkasLama, $berkasBaru);
        $pengajuan->status = 'Diproses'; // Otomatis kembali diproses oleh BAAK
        if ($request->filled('catatan_pemohon')) {
            $pengajuan->catatan_revisi = ($pengajuan->catatan_revisi ? $pengajuan->catatan_revisi . "\n" : '') .
                "[Respon Mahasiswa: " . $request->input('catatan_pemohon') . "]";
        }
        $pengajuan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Berkas perbaikan berhasil diunggah. Permohonan Anda kini kembali diproses oleh bagian akademik.',
            'data' => $pengajuan->load('layanan'),
        ]);
    }

    /**
     * Statistik ringkasan pengajuan untuk metrik cards.
     */
    public function stats(): JsonResponse
    {
        $totalLayanan = Layanan::count();
        $sedangDiproses = Pengajuan::where('status', 'Diproses')->count();
        $perluPerbaikan = Pengajuan::where('status', 'Perlu Perbaikan')->count();
        $selesaiTerbit = Pengajuan::where('status', 'Disetujui')->count();
        $pending = Pengajuan::where('status', 'Pending')->count();
        $ditolak = Pengajuan::where('status', 'Ditolak')->count();
        $totalPengajuan = Pengajuan::count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'layanan_tersedia' => $totalLayanan,
                'sedang_diproses' => $sedangDiproses,
                'perlu_perbaikan' => $perluPerbaikan,
                'selesai_terbit' => $selesaiTerbit,
                'pending' => $pending,
                'ditolak' => $ditolak,
                'total_pengajuan' => $totalPengajuan,
            ],
        ]);
    }

    /**
     * Helper membuat nomor pelacakan unik format REQ-YYYYMMDD-XXXX
     */
    private function generateNomorPelacakan(): string
    {
        $datePrefix = 'REQ-' . now()->format('Ymd') . '-';
        do {
            $randomSuffix = strtoupper(Str::random(4));
            $nomor = $datePrefix . $randomSuffix;
        } while (Pengajuan::where('nomor_pelacakan', $nomor)->exists());

        return $nomor;
    }
}

