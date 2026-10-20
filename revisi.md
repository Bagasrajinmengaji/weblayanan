# 📌 PROMPT INSTRUKSI AGENT AI: REVISI UI & UX FRONTEND (`weblayanan/`)

## 🎯 FOKUS UTAMA
Tugas ini fokus pada penyempurnaan Antarmuka (UI) dan Pengalaman Pengguna (UX) pada folder `weblayanan/` agar portal layanan sangat ramah pengguna awam (non-IT), bebas dari kebingungan (*frictionless*), serta responsif.

---

## 🟢 DAFTAR REVISI & EKSEKUSI COMPONENT

### 1. Form Pengajuan (`app/pages/pengajuan/index.vue` & Component Terkait)
- [ ] **Pemisahan Slot Unggah Berkas (Multi-Dropzone / Slot Specific):**
  - Ganti 1 area dropzone umum menjadi slot kartu terpisah sesuai persyaratan dokumen yang dipilih (misal: Slot 1: KTM, Slot 2: Bukti UKT, Slot 3: KRS).
  - Tampilkan indikator visual jelas per slot: nama file, ukuran file, status terunggah (border hijau), dan tombol `Hapus` / `Ganti`.
- [ ] **Validasi Berkas Real-Time di Frontend:**
  - Berikan petunjuk format file yang diterima (`.pdf`, `.jpg`, `.png`) dan batasan ukuran max 10MB.
  - Tampilkan pesan error instan jika format file tidak sesuai sebelum form dikirim.
- [ ] **Modal Ringkasan Data (Review Dialog) Sebelum Submit:**
  - Tambahkan dialog konfirmasi (`<Dialog>` / `<Sheet>`) saat tombol "Kirim Pengajuan" diklik.
  - Tampilkan ringkasan data: Nama, NIM, Jenis Layanan, No. WhatsApp, dan Jumlah Berkas Terlampir.
  - Sediakan tombol aksi: `Perbaiki Data` (tutup modal) dan `Kirim Sekarang` (proses submit API).

---

### 2. Pelacakan Status Berkas (`app/pages/pengajuan/lacak.vue`)
- [ ] **Fitur Unggah Ulang Berkas Revisi (Status 'Perlu Perbaikan'):**
  - Jika status backend bernilai `'Perlu Perbaikan'`, tampilkan Callout Box berisi `catatan_revisi` dari BAAK.
  - Sediakan tombol `Unggah Berkas Perbaikan` beserta form mini inline untuk mengganti file yang bermasalah saja tanpa mengisi ulang identitas dari nol.
- [ ] **Kejelasan Aksi Penyelesaian (Status 'Disetujui'):**
  - Tampilkan tombol utama `Unduh Surat Digital (PDF)` (jika ada file terbit).
  - Tampilkan informasi lokasi fisik: *"Atau ambil dokumen cetak di Loket BAAK Ruang 102 (Senin–Jumat, 08:00–15:00 WIB)"*.
- [ ] **Bantuan Lupa Kode Pelacakan:**
  - Tambahkan tautan teks di bawah input kode: *"Lupa nomor pelacakan? Cari berdasarkan Nama atau NIM di menu Riwayat Pengajuan."*

---

### 3. Integrasi Detail Layanan & Auto-Select (`app/pages/layanan/[id].vue`)
- [ ] **Auto-Select Dropdown Layanan:**
  - Saat tombol "Ajukan Permohonan" di halaman detail layanan diklik, arahkan ke `/pengajuan?layanan_id={id}`.
  - Pastikan dropdown jenis layanan pada formulir pengajuan otomatis terisi/terpilih sesuai ID layanan dari query parameter URL.

---

### 4. Pembersihan UI & Label Tabel (`DataTable.vue` & Header)
- [ ] **Mapping Label Kolom TanStack Table:**
  - Ubah nama header & dropdown "Sesuaikan Kolom" dari nama variabel koding menjadi label Bahasa Indonesia yang ramah:
    - `deskripsi` $\rightarrow$ **Deskripsi Singkat**
    - `persyaratan` $\rightarrow$ **Syarat Dokumen**
    - `created_at` $\rightarrow$ **Tanggal Pengajuan**
- [ ] **Interaktivitas Dropdown Notifikasi (`SiteHeader.vue`):**
  - Hubungkan item notifikasi agar ketika diklik, pengguna langsung diarahkan (*router push*) ke halaman pelacakan nomor surat terkait (`/pengajuan/lacak?nomor=REQ-...`).