# PROMPT INSTRUKSI AGENT AI: PENGEMBANGAN PORTAL LAYANAN MAHASISWA (MONOREPO)

## 📌 RINGKASAN PROYEK & STATUS TERAKHIR
Proyek ini adalah Monorepo Aplikasi Web **Portal Layanan Administrasi Mahasiswa** yang terdiri dari:
1. `backend_api/` : Laravel REST API Backend (Akan dikerjakan).
2. `weblayanan/`  : Nuxt 3 Frontend dengan Tailwind v4 & Shadcn Vue (**STATUS: STATUS LAYOUT & KOMPONEN UTAMA SELESAI**).

---

## 🟢 STATUS PENGERJAAN FRONTEND (`weblayanan/`) — DONE
Seluruh komponen UI dasar di `app/components/` telah selesai disiapkan dan disesuaikan:
- **Konfigurasi**: Tailwind CSS v4, Font Inter, Tabler Icons (`@tabler/icons-vue`), Reka UI / Shadcn Vue.
- **Layout & Navigasi**:
  - `AppSidebar.vue`: Sidebar utama dengan menu khusus Portal Layanan Mahasiswa (`Dashboard Layanan`, `Cek Status`, `Riwayat`).
  - `SiteHeader.vue`: Header atas dengan judul portal, notifikasi, dan tombol cepat pelacakan.
  - `NavMain.vue`, `NavDocuments.vue`, `NavSecondary.vue`, `NavUser.vue`: Komponen navigasi yang sudah disesuaikan dengan alias path Nuxt `~/components/ui/...` dan routing `<NuxtLink>`.
- **Komponen Konten**:
  - `SectionCards.vue`: Statistik metrik layanan (`Layanan Tersedia`, `Sedang Diproses`, `Perlu Perbaikan`, `Selesai Terbit`).
  - `DataTable.vue`: Tabel responsif menggunakan TanStack Table v8 untuk menampilkan daftar 6 Layanan Administrasi lengkap dengan fitur pagination dan tombol *Ajukan*.

---

## 🎯 TUGAS AGENT AI SAAT INI: FOKUS BAGIAN 1 (BACKEND LARAVEL API)

Tolong kerjakan **BAGIAN 1: BACKEND LARAVEL REST API** di dalam folder `backend_api/` sesuai dengan spesifikasi berikut:

### 1. Database Migration & Models
Buat migration dan model untuk tabel-tabel berikut:
- **`layanan`**:
  - `id` (primary Key)
  - `nama_layanan` (string)
  - `slug` (string, unique)
  - `deskripsi` (text)
  - `persyaratan` (json / text - menyimpan array persyaratannya)
  - `timestamps`
- **`pengajuan`**:
  - `id` (primary Key)
  - `nomor_pelacakan` (string, unique, format: `REQ-YYYYMMDD-XXXX`)
  - `layanan_id` (foreign key -> `layanan.id`)
  - `nama_mahasiswa` (string)
  - `nim` (string)
  - `email` (string)
  - `no_hp` (string)
  - `berkas_persyaratan` (json / string - path file ter-upload)
  - `status` (enum: `'Pending'`, `'Diproses'`, `'Perlu Perbaikan'`, `'Disetujui'`, `'Ditolak'`)
  - `catatan_revisi` (text, nullable)
  - `timestamps`

### 2. Database Seeder (`DatabaseSeeder.php`)
Isi seeder untuk tabel `layanan` dengan minimal 6 jenis layanan akademik/kemahasiswaan realistis (misal: *Surat Keterangan Aktif Kuliah*, *Surat Pengantar Penelitian*, *Legalisir Ijazah/SKPI*, *Surat Bebas Pustaka*, *Pengajuan Cuti Akademik*, *Surat Rekomendasi Beasiswa*).

### 3. API Controllers & Routes (`routes/api.php`)
Buat RESTful API Controller (`LayananController` dan `PengajuanController`) dengan endpoint:
- `GET /api/layanan` : Mengambil daftar semua layanan.
- `GET /api/layanan/{id}` : Mengambil detail layanan berdasarkan ID/slug.
- `POST /api/pengajuan` : Menyimpan pengajuan baru (mengembalikan `nomor_pelacakan` unik).
- `GET /api/pengajuan/lacak/{nomor_pelacakan}` : Mengecek status pengajuan & catatan revisi berdasarkan nomor pelacakan.
- `GET /api/pengajuan/riwayat` : Mengambil riwayat pengajuan.
- `PUT /api/pengajuan/{id}/status` : Mengupdate status pengajuan & catatan revisi (untuk admin/proses perbaikan).

---

## 🚀 PETUNJUK EKSEKUSI
1. Jalankan `php artisan migrate --seed` di folder `backend_api/`.
2. Pastikan CORS pada Laravel sudah dikonfigurasi (`config/cors.php`) agar API dapat diakses oleh Nuxt 3 di `http://localhost:3000`.
3. Setelah Backend selesai dan dites, lanjutkan ke **BAGIAN 2 (Integrasi Nuxt 3 Fetch API)** di folder `weblayanan/`.