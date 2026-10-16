<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\Pengajuan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'nama_layanan' => 'Surat Keterangan Aktif Kuliah',
                'slug' => 'surat-keterangan-aktif-kuliah',
                'deskripsi' => 'Surat keterangan resmi universitas yang menerangkan bahwa mahasiswa bersangkutan aktif terdaftar dan mengikuti perkuliahan pada semester berjalan, untuk keperluan tunjangan gaji dinas/BUMN orang tua, BPJS Kesehatan/Ketenagakerjaan, atau instansi eksternal.',
                'persyaratan' => [
                    'Scan Kartu Tanda Mahasiswa (KTM)',
                    'Bukti Pembayaran UKT / SPP Semester Berjalan',
                    'Kartu Rencana Studi (KRS) Semester Aktif',
                    'Surat Pengantar / Permohonan dari Instansi Penerima (Bila ada)',
                ],
            ],
            [
                'nama_layanan' => 'Surat Pengantar Penelitian & Riset',
                'slug' => 'surat-pengantar-penelitian',
                'deskripsi' => 'Surat pengantar izin resmi dari fakultas/universitas untuk melaksanakan survei lapangan, wawancara, observasi, atau uji laboratorium di instansi/perusahaan/sekolah guna penyusunan Skripsi/Tesis/Tugas Akhir.',
                'persyaratan' => [
                    'Proposal Penelitian Skripsi yang telah disetujui Dosen Pembimbing',
                    'Scan Kartu Tanda Mahasiswa (KTM)',
                    'Formulir Lembar Pengesahan Dosen Pembimbing',
                    'Nama Instansi, Alamat, dan Pejabat Tujuan Surat',
                ],
            ],
            [
                'nama_layanan' => 'Legalisir Ijazah & Transkrip / SKPI',
                'slug' => 'legalisir-ijazah-skpi',
                'deskripsi' => 'Pelayanan pengesahan dan legalisasi fotokopi dokumen resmi kelulusan (Ijazah Asli, Transkrip Akademik, dan Surat Keterangan Pendamping Ijazah) berstempel basah/tanda tangan elektronik (TTE) pejabat berwenang.',
                'persyaratan' => [
                    'Scan Ijazah Asli & Transkrip Akademik Asli Berwarna (PDF)',
                    'Bukti Bebas Pustaka Universitas & Bebas Administrasi Keuangan',
                    'Formulir Permohonan Legalisir Dokumen Kelulusan',
                ],
            ],
            [
                'nama_layanan' => 'Surat Bebas Perpustakaan & Laboratorium',
                'slug' => 'surat-bebas-pustaka',
                'deskripsi' => 'Surat pernyataan resmi dari UPT Perpustakaan dan Laboratorium yang menerangkan mahasiswa telah menyelesaikan seluruh peminjaman buku/alat serta tidak memiliki tunggakan denda maupun kewajiban lainnya.',
                'persyaratan' => [
                    'Scan Kartu Tanda Mahasiswa (KTM)',
                    'Bukti Pengembalian Semua Bahan Pustaka / Buku Fisik',
                    'Bukti Unggah Softcopy Skripsi / Karya Ilmiah ke Repository Kampus',
                ],
            ],
            [
                'nama_layanan' => 'Pengajuan Cuti Akademik',
                'slug' => 'pengajuan-cuti-akademik',
                'deskripsi' => 'Permohonan penghentian sementara kegiatan akademik perkuliahan untuk periode 1 (satu) atau 2 (dua) semester dengan alasan sakit, kedinasan, musibah keluarga, atau pertimbangan mendesak lainnya.',
                'persyaratan' => [
                    'Surat Permohonan Cuti bermaterai ditandatangani Mahasiswa & Orang Tua/Wali',
                    'Surat Rekomendasi / Persetujuan Dosen Pembimbing Akademik (DPA)',
                    'Surat Keterangan Medis dari Rumah Sakit (bila sakit) atau Surat Tugas Dinas (bila bekerja)',
                    'Bebas Pustaka dan Bebas Tanggungan UKT',
                ],
            ],
            [
                'nama_layanan' => 'Surat Rekomendasi Beasiswa & Prestasi',
                'slug' => 'surat-rekomendasi-beasiswa',
                'deskripsi' => 'Surat rekomendasi pimpinan fakultas/universitas untuk persyaratan pendaftaran beasiswa (Djarum, Bank Indonesia, LPDP, KIP-K, dll.) maupun delegasi perlombaan/konferensi ilmiah mahasiswa tingkat nasional dan internasional.',
                'persyaratan' => [
                    'Transkrip Nilai Akademik Sementara (IPK Minimum 3.00)',
                    'Scan Kartu Tanda Mahasiswa (KTM)',
                    'Sertifikat Prestasi Akademik / Non-Akademik / Organisasi',
                    'Brosur atau Panduan Resmi Beasiswa / Lomba yang Dituju',
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            Layanan::updateOrCreate(
                ['slug' => $serviceData['slug']],
                $serviceData
            );
        }

        // Buat sample pengajuan dengan berbagai status untuk testing tracking & dashboard
        $layananAktif = Layanan::where('slug', 'surat-keterangan-aktif-kuliah')->first();
        $layananRiset = Layanan::where('slug', 'surat-pengantar-penelitian')->first();
        $layananLegalisir = Layanan::where('slug', 'legalisir-ijazah-skpi')->first();
        $layananCuti = Layanan::where('slug', 'pengajuan-cuti-akademik')->first();

        $sampleSubmissions = [
            [
                'nomor_pelacakan' => 'REQ-20261016-1001',
                'layanan_id' => $layananAktif?->id ?? 1,
                'nama_mahasiswa' => 'Bagas Pratama',
                'nim' => '2201011045',
                'email' => 'bagas.pratama@student.ac.id',
                'no_hp' => '081234567890',
                'berkas_persyaratan' => [
                    'ktm' => 'uploads/sample_ktm.pdf',
                    'bukti_ukt' => 'uploads/sample_ukt.pdf',
                    'krs' => 'uploads/sample_krs.pdf',
                ],
                'status' => 'Pending',
                'catatan_revisi' => null,
            ],
            [
                'nomor_pelacakan' => 'REQ-20261016-1002',
                'layanan_id' => $layananRiset?->id ?? 2,
                'nama_mahasiswa' => 'Siti Aisyah',
                'nim' => '2201011088',
                'email' => 'siti.aisyah@student.ac.id',
                'no_hp' => '082198765432',
                'berkas_persyaratan' => [
                    'proposal' => 'uploads/sample_proposal.pdf',
                    'ktm' => 'uploads/sample_ktm.pdf',
                ],
                'status' => 'Diproses',
                'catatan_revisi' => 'Dokumen sedang dalam penandatanganan Wakil Dekan I.',
            ],
            [
                'nomor_pelacakan' => 'REQ-20261016-1003',
                'layanan_id' => $layananLegalisir?->id ?? 3,
                'nama_mahasiswa' => 'Rian Hidayat',
                'nim' => '2101011022',
                'email' => 'rian.hidayat@student.ac.id',
                'no_hp' => '085712345678',
                'berkas_persyaratan' => [
                    'ijazah' => 'uploads/sample_ijazah.pdf',
                ],
                'status' => 'Perlu Perbaikan',
                'catatan_revisi' => 'Mohon unggah ulang scan Ijazah asli dengan resolusi lebih jelas/tidak buram.',
            ],
            [
                'nomor_pelacakan' => 'REQ-20261016-1004',
                'layanan_id' => $layananAktif?->id ?? 1,
                'nama_mahasiswa' => 'Dina Mariana',
                'nim' => '2301011015',
                'email' => 'dina.mariana@student.ac.id',
                'no_hp' => '087812341234',
                'berkas_persyaratan' => [
                    'ktm' => 'uploads/sample_ktm.pdf',
                    'bukti_ukt' => 'uploads/sample_ukt.pdf',
                ],
                'status' => 'Disetujui',
                'catatan_revisi' => 'Surat selesai diterbitkan. Silakan unduh dokumen ber-barcode pada portal atau ambil di loket BAAK.',
            ],
            [
                'nomor_pelacakan' => 'REQ-20261016-1005',
                'layanan_id' => $layananCuti?->id ?? 5,
                'nama_mahasiswa' => 'Andi Wijaya',
                'nim' => '2101011059',
                'email' => 'andi.wijaya@student.ac.id',
                'no_hp' => '089612345678',
                'berkas_persyaratan' => [
                    'surat_cuti' => 'uploads/sample_cuti.pdf',
                ],
                'status' => 'Ditolak',
                'catatan_revisi' => 'Batas waktu pengajuan cuti semester telah lewat kalender akademik.',
            ],
        ];

        foreach ($sampleSubmissions as $submission) {
            Pengajuan::updateOrCreate(
                ['nomor_pelacakan' => $submission['nomor_pelacakan']],
                $submission
            );
        }
    }
}

