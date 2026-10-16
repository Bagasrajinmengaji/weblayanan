<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pengajuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalLayananApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LayananSeeder::class);
    }

    public function test_can_get_list_of_layanan(): void
    {
        $response = $this->getJson('/api/layanan');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'status',
                'total',
                'data' => [
                    '*' => [
                        'id',
                        'nama_layanan',
                        'slug',
                        'deskripsi',
                        'persyaratan',
                    ],
                ],
            ]);

        $this->assertGreaterThanOrEqual(6, count($response->json('data')));
    }

    public function test_can_get_layanan_detail_by_id_and_slug(): void
    {
        $layanan = Layanan::first();

        // By ID
        $responseById = $this->getJson("/api/layanan/{$layanan->id}");
        $responseById->assertStatus(200)
            ->assertJsonPath('data.id', $layanan->id)
            ->assertJsonPath('data.slug', $layanan->slug);

        // By Slug
        $responseBySlug = $this->getJson("/api/layanan/{$layanan->slug}");
        $responseBySlug->assertStatus(200)
            ->assertJsonPath('data.id', $layanan->id)
            ->assertJsonPath('data.slug', $layanan->slug);
    }

    public function test_returns_404_when_layanan_not_found(): void
    {
        $response = $this->getJson('/api/layanan/slug-tidak-ada');
        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Layanan tidak ditemukan.',
            ]);
    }

    public function test_can_create_new_pengajuan(): void
    {
        $layanan = Layanan::first();

        $payload = [
            'layanan_id' => $layanan->id,
            'nama_mahasiswa' => 'Budi Santoso',
            'nim' => '2301019999',
            'email' => 'budi.santoso@student.ac.id',
            'no_hp' => '081299998888',
            'berkas_persyaratan' => [
                'ktm' => 'uploads/test_ktm.pdf',
            ],
        ];

        $response = $this->postJson('/api/pengajuan', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pengajuan layanan berhasil disimpan.',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'nomor_pelacakan',
                'data' => [
                    'id',
                    'nomor_pelacakan',
                    'status',
                    'nama_mahasiswa',
                    'nim',
                ],
            ]);

        $nomorPelacakan = $response->json('nomor_pelacakan');
        $this->assertMatchesRegularExpression('/^REQ-\d{8}-[A-Z0-9]{4}$/', $nomorPelacakan);

        $this->assertDatabaseHas('pengajuan', [
            'nomor_pelacakan' => $nomorPelacakan,
            'nim' => '2301019999',
            'status' => 'Pending',
        ]);
    }

    public function test_can_track_pengajuan_by_nomor_pelacakan(): void
    {
        $pengajuan = Pengajuan::first();

        $response = $this->getJson("/api/pengajuan/lacak/{$pengajuan->nomor_pelacakan}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'nomor_pelacakan' => $pengajuan->nomor_pelacakan,
                    'status' => $pengajuan->status,
                ],
            ]);
    }

    public function test_returns_404_when_tracking_invalid_nomor_pelacakan(): void
    {
        $response = $this->getJson('/api/pengajuan/lacak/REQ-00000000-XXXX');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_can_get_riwayat_pengajuan(): void
    {
        $response = $this->getJson('/api/pengajuan/riwayat');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'status',
                'total',
                'data',
            ]);

        $this->assertNotEmpty($response->json('data'));
    }

    public function test_can_update_pengajuan_status(): void
    {
        $pengajuan = Pengajuan::first();

        $response = $this->putJson("/api/pengajuan/{$pengajuan->id}/status", [
            'status' => 'Diproses',
            'catatan_revisi' => 'Sedang diverifikasi oleh staf akademik.',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $pengajuan->id,
                    'status' => 'Diproses',
                    'catatan_revisi' => 'Sedang diverifikasi oleh staf akademik.',
                ],
            ]);

        $this->assertDatabaseHas('pengajuan', [
            'id' => $pengajuan->id,
            'status' => 'Diproses',
            'catatan_revisi' => 'Sedang diverifikasi oleh staf akademik.',
        ]);
    }

    public function test_can_get_stats(): void
    {
        $response = $this->getJson('/api/pengajuan/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'layanan_tersedia',
                    'sedang_diproses',
                    'perlu_perbaikan',
                    'selesai_terbit',
                    'pending',
                    'ditolak',
                    'total_pengajuan',
                ],
            ]);
    }
}
