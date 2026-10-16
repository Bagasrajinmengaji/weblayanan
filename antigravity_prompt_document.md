# 📌 PROMPT INSTRUCTION FOR ANTIGRAVITY IDE

## Project: Portal Layanan Mahasiswa (Decoupled: Laravel REST API + Nuxt 3)

### 🚀 CONTEXT & ARCHITECTURE

Anda adalah Senior Fullstack Developer yang membantu membangun **Portal Layanan Mahasiswa** menggunakan arsitektur *decoupled*:

* **Backend (`backend_api`):** Laravel 11 (REST API via Laravel Herd)

* **Frontend (`weblayanan`):** Nuxt 3 (Vue 3, Composition API `<script setup>`, Tailwind CSS)

### 📋 SPESIFIKASI KEBUTUHAN STIPULASI STUDY CASE

#### **1. Fitur Utama & Kebutuhan Pengguna**

* **Pengguna Utama:** Mahasiswa & Petugas Administrasi.

* **Jenis Layanan (Minimal 6 Data Simulation/Database):**

  1. Surat Keterangan Aktif Kuliah

  2. Surat Pengantar Penelitian

  3. Legalisasi Ijazah & Transkrip

  4. Surat Keterangan Bebas Pustaka

  5. Surat Permohonan Cutip Akademik

  6. Surat Pengantar Magang / PKL

* **Alur Utama:**

  1. Menemukan layanan & membaca persyaratan ringkas/jelas.

  2. Mengisi formulir pengajuan + melampirkan dokumen + memeriksa ringkasan data.

  3. Mengirim pengajuan dan memperoleh **Nomor Pelacakan / Tracking ID**.

  4. Memantau status pengajuan + membaca catatan perbaikan jika pengajuan dikembalikan.

#### **2. Kebutuhan UI/UX, Aksesibilitas & Responsivitas**

* **4 State Handling Wajib (UI States):**

  * `Memuat (Loading)`: Indikator proses / Skeleton loader.

  * `Berhasil (Success)`: Tampilan data / Umpan balik sukses.

  * `Kosong (Empty)`: Pesan jelas bila pencarian/data tidak ditemukan + saran tindakan.

  * `Gagal (Error)`: Pesan error informatif + tombol "Coba Lagi" (Retry).

* **Komponen & Keterbacaan:**

  * Status pengajuan wajib menggunakan **Label Teks + Indikator Warna** (contoh: `[Diproses]` - Kuning, `[Dikembalikan]` - Merah, `[Selesai]` - Hijau).

  * Tampilan responsif (Mobile & Desktop).

  * Input form memiliki `<label>` semantik, pesan error didekat field terkait, serta tombol aksi spesifik (misal: "Kirim Pengajuan").

### 🛠️ TUGAS KERJA / IMPLEMENTASI

#### **BAGIAN 1: BACKEND LARAVEL (`backend_api`)**

1. **Database Migration & Model (`PengajuanLayanan`):**

   * `id` (Primary Key)

   * `nomor_pelacakan` (String, Unique - Contoh: `TRX-20261015-XXXX`)

   * `layanan_id` (String / Foreign Key)

   * `nama_layanan` (String)

   * `nama_mahasiswa` (String)

   * `nim` (String)

   * `email` (String)

   * `dokumen_persyaratan` (String / Nullable)

   * `catatan_perbaikan` (Text / Nullable)

   * `status` (Enum: `diproses`, `dikembalikan`, `disetujui`, `ditolak`)

   * `timestamps`

2. **Seeder Data Layanan:**

   * Buat seeder/dummy data untuk 6 jenis layanan beserta rincian persyaratan lengkapnya.

3. **API Controller & Routes (`routes/api.php`):**

   * `GET /api/layanan` : Mengambil daftar 6 layanan (Mendukung query parameter pencarian `?search=`).

   * `GET /api/layanan/{id}` : Detail persyaratan layanan.

   * `POST /api/pengajuan` : Mengirim pengajuan baru (Dengan validasi input ketat & generate `nomor_pelacakan`).

   * `GET /api/pengajuan/track/{nomor_pelacakan}` : Memantau status pengajuan & membaca catatan perbaikan.

4. **Standards Response JSON:**
   Selalu kembalikan format JSON standar:

   ```
   {
     "status": "success",
     "message": "Pesan deskriptif",
     "data": { ... }
   }
   
   ```

#### **BAGIAN 2: FRONTEND NUXT 3 (`weblayanan`)**

1. **Struktur Halaman (Minimal 5 Pages):**

   * `pages/index.vue`: Beranda + Pencarian Layanan (Case-insensitive & Trimmed) + Daftar Kartu Layanan (Komponen Reusable).

   * `pages/layanan/[id].vue`: Detail Persyaratan Layanan + Button "Ajukan Sekarang".

   * `pages/pengajuan/index.vue`: Form Pengajuan + Upload Dokumen Simulasi + Step Ringkasan sebelum Send.

   * `pages/pengajuan/sukses.vue`: Tampilan Sukses + Informasi Nomor Pelacakan.

   * `pages/lacak.vue`: Form Cek Status Pengajuan via Nomor Pelacakan + Tampilan Status & Catatan Perbaikan.

2. **Komponen Reusable (`components/`):**

   * `CardLayanan.vue` (Props: `id`, `title`, `description`, `icon`)

   * `StatusBadge.vue` (Props: `status`)

   * `BaseInput.vue` / `BaseAlert.vue`

   * `LoadingSkeleton.vue` & `EmptyState.vue`

3. **Layout (`layouts/default.vue`):**

   * Navbar Navigasi Utama + Footer Informasi Kampus.

### 🎯 INTRUKSI EKSEKUSI UNTUK AGENT ANTIGRAVITY

Berdasarkan spesifikasi di atas, kerjakan tugas berikut secara berurutan:

1. Buat **Migration**, **Model**, dan **Seeder** untuk `Layanan` dan `Pengajuan` di Laravel (`backend_api`).

2. Buat **Controller & Route API** untuk pengajuan dan tracking di Laravel.

3. Buat **Layout & Komponen Nuxt 3** yang mengimplementasikan 4 state handling (`pending`, `error`, `empty`, `success`).

4. Buat seluruh **Halaman Nuxt 3** sesuai alur user story dari membaca persyaratan hingga lacak status pengajuan.

```
eof

Teks prompt di atas sudah berisikan seluruh instruksi dan rincian spesifikasi tugas dari file PDF "Portal Layanan Mahasiswa" milikmu.

### Cara Penggunaan:
1. Kamu tinggal *copy* isi file `prompt_portal_layanan_mahasiswa.md` tersebut.
2. Tempel (*paste*) langsung ke kolom chat Agent di **Antigravity IDE**.
3. Perintahkan agent untuk mulai mengerjakan dari **BAGIAN 1 (Backend Laravel)** terlebih dahulu!

```