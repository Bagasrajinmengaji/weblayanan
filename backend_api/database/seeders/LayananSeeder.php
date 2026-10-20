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
    }
}

