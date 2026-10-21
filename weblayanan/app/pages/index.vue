<script setup lang="ts">
import {
  IconArrowRight,
  IconBuildingBank,
  IconCheck,
  IconClock,
  IconDeviceLaptop,
  IconFileCertificate,
  IconFileText,
  IconHelpCircle,
  IconLogin,
  IconSearch,
  IconShieldCheck,
  IconUser,
  IconUserPlus,
} from "@tabler/icons-vue"

import { Button } from "~/components/ui/button"

definePageMeta({
  layout: false,
})

useSeoMeta({
  title: "Portal Layanan Mahasiswa - Pengajuan Surat & Dokumen Akademik",
  description:
    "Layanan mandiri pengajuan surat keterangan aktif, izin penelitian, bebas pustaka, dan legalisir dokumen akademik secara online, cepat, dan transparan.",
})

const { isAuthenticated, user, isAdmin } = useAuth()

// Layanan populer untuk ditampilkan pada landing page
const layananPopuler = [
  {
    id: 1,
    nama: "Surat Keterangan Aktif",
    deskripsi: "Untuk syarat beasiswa, tunjangan gaji orang tua, BPJS, dan magang industri.",
    estimasi: "1 Hari Kerja",
    tag: "Paling Banyak Diajukan",
    target: "/pengajuan?layanan_id=1",
  },
  {
    id: 2,
    nama: "Pengantar Penelitian & Riset",
    deskripsi: "Izin pengumpulan data dan observasi lapangan untuk tugas akhir atau skripsi.",
    estimasi: "1-2 Hari Kerja",
    tag: "Akademik",
    target: "/pengajuan?layanan_id=2",
  },
  {
    id: 3,
    nama: "Legalisir Ijazah & Transkrip",
    deskripsi: "Pengesahan salinan dokumen akademik resmi untuk pendaftaran kerja atau studi lanjut.",
    estimasi: "2 Hari Kerja",
    tag: "Alumni & Mahasiswa",
    target: "/pengajuan?layanan_id=3",
  },
  {
    id: 4,
    nama: "Surat Bebas Pustaka",
    deskripsi: "Keterangan bebas pinjaman buku sebagai salah satu syarat pendaftaran yudisium.",
    estimasi: "1 Hari Kerja",
    tag: "Perpustakaan",
    target: "/pengajuan?layanan_id=4",
  },
]

// 4 Langkah mudah pengajuan
const langkahAlur = [
  {
    no: "01",
    judul: "Masuk Akun",
    deskripsi: "Gunakan SSO Google Akun Kampus atau akun mahasiswa terdaftar Anda.",
  },
  {
    no: "02",
    judul: "Pilih Jenis Layanan",
    deskripsi: "Tentukan jenis surat yang dibutuhkan dan isi data formulir pengajuan.",
  },
  {
    no: "03",
    judul: "Verifikasi Berkas",
    deskripsi: "Petugas BAAK memeriksa dan memproses permohonan sesuai SOP akademik.",
  },
  {
    no: "04",
    judul: "Selesai & Unduh",
    deskripsi: "Dapatkan dokumen resmi dengan pengesahan digital dan ambil surat jadi.",
  },
]
</script>

<template>
  <div class="min-h-screen bg-background text-foreground flex flex-col selection:bg-orange-500/20 selection:text-orange-900 dark:selection:text-orange-200">
    <!-- Navbar Atas (Nuxt UI Landing Style) -->
    <header class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/85 backdrop-blur-md transition-all">
      <div class="max-w-6xl mx-auto flex h-16 items-center justify-between px-4 sm:px-6">
        <!-- Logo Kiri -->
        <div class="flex items-center gap-3">
          <NuxtLink to="/" class="flex items-center gap-2 group">
            <div class="size-8 rounded-lg bg-orange-500 text-white flex items-center justify-center font-bold shadow-xs transition-transform group-hover:scale-105">
              <IconBuildingBank class="size-4.5" />
            </div>
            <span class="font-bold text-lg tracking-tight text-foreground">
              Portal<span class="text-orange-600 dark:text-orange-500">Layanan</span>
            </span>
          </NuxtLink>

          <!-- Badge Status / Kategori Template -->
          <div class="hidden sm:inline-flex items-center gap-1.5 rounded-full border border-orange-500/30 bg-orange-500/10 px-2.5 py-0.5 text-[11px] font-medium text-orange-700 dark:text-orange-400">
            <span>Akademik</span>
          </div>
        </div>

        <!-- Menu Tengah -->
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-muted-foreground">
          <a href="#layanan" class="hover:text-foreground transition-colors">Layanan</a>
          <a href="#alur" class="hover:text-foreground transition-colors">Alur Pengajuan</a>
          <a href="#keunggulan" class="hover:text-foreground transition-colors">Keunggulan</a>
          <NuxtLink to="/pengajuan/lacak" class="hover:text-foreground transition-colors">Lacak Berkas</NuxtLink>
        </nav>

        <!-- Aksi Kanan -->
        <div class="flex items-center gap-2.5">
          <!-- Tombol Lacak Status -->
          <Button variant="ghost" size="sm" as-child class="hidden sm:inline-flex text-muted-foreground hover:text-foreground">
            <NuxtLink to="/pengajuan/lacak">
              <IconSearch class="size-4 mr-1.5" />
              Lacak Surat
            </NuxtLink>
          </Button>

          <!-- Sudah Login: Tampilkan Shortcut ke Dashboard -->
          <template v-if="isAuthenticated">
            <Button size="sm" as-child class="bg-orange-500 hover:bg-orange-600 text-white font-medium shadow-xs">
              <NuxtLink :to="isAdmin ? '/admin' : '/dashboard'">
                <span>Dashboard {{ isAdmin ? 'Admin' : 'Mahasiswa' }}</span>
                <IconArrowRight class="size-3.5 ml-1" />
              </NuxtLink>
            </Button>
          </template>

          <!-- Belum Login: Tampilkan Masuk & Daftar -->
          <template v-else>
            <Button variant="outline" size="sm" as-child class="border-border text-foreground hover:bg-muted font-medium">
              <NuxtLink to="/login">
                <IconLogin class="size-4 mr-1.5" />
                Masuk
              </NuxtLink>
            </Button>
            <Button size="sm" as-child class="bg-orange-500 hover:bg-orange-600 text-white font-medium shadow-xs">
              <NuxtLink to="/login">
                Ajukan Surat
              </NuxtLink>
            </Button>
          </template>
        </div>
      </div>
    </header>

    <!-- Konten Utama -->
    <main class="flex-1">
      <!-- HERO SECTION dengan Top Warm Glow persis template -->
      <section class="relative pt-16 pb-12 sm:pt-24 sm:pb-20 overflow-hidden">
        <!-- Radial Gradient Glow di Bagian Atas Tengah -->
        <div class="pointer-events-none absolute inset-x-0 -top-24 h-96 bg-[radial-gradient(ellipse_70%_50%_at_50%_0%,rgba(249,115,22,0.18),transparent_75%)]"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center relative z-10">
          <!-- Pill Badge di atas Headline -->
          <div class="inline-flex items-center gap-2 rounded-full border border-orange-500/20 bg-orange-500/5 px-3 py-1 text-xs font-medium text-orange-600 dark:text-orange-400 mb-6">
            <span class="size-1.5 rounded-full bg-orange-500 animate-pulse"></span>
            Platform Mandiri Layanan Persuratan & Dokumen Resmi
          </div>

          <!-- Headline Utama yang Besar dan Berbobot -->
          <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight text-foreground leading-[1.15] max-w-4xl mx-auto">
            Layanan Persuratan & Dokumen Mahasiswa Terpadu
          </h1>

          <!-- Subtitle Deskripsi -->
          <p class="mt-6 text-base sm:text-lg text-muted-foreground max-w-2xl mx-auto leading-relaxed">
            Permohonan surat keterangan aktif kuliah, izin riset penelitian, bebas perpustakaan, hingga legalisir dokumen resmi kampus secara cepat, tertib, dan transparan.
          </p>

          <!-- Tombol CTA Tengah (Orange & Secondary) -->
          <div class="mt-8 sm:mt-10 flex flex-wrap items-center justify-center gap-3.5">
            <Button size="lg" as-child class="h-11 px-6 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium shadow-sm transition-transform active:scale-95 gap-2">
              <NuxtLink :to="isAuthenticated ? (isAdmin ? '/admin' : '/pengajuan') : '/login'">
                <span>Mulai Pengajuan</span>
                <IconArrowRight class="size-4" />
              </NuxtLink>
            </Button>

            <Button variant="outline" size="lg" as-child class="h-11 px-5 rounded-lg border-border bg-background hover:bg-muted text-foreground font-medium gap-2">
              <NuxtLink to="/pengajuan/lacak">
                <IconSearch class="size-4 text-muted-foreground" />
                <span>Lacak Status Surat</span>
              </NuxtLink>
            </Button>
          </div>

          <!-- Garis Dotted Dekoratif dengan Titik Lingkaran Orange (Persis Screenshot Template) -->
          <div class="relative w-full max-w-xs mx-auto mt-12 mb-8 flex flex-col items-center">
            <div class="w-px h-12 border-l border-dashed border-orange-400/60 dark:border-orange-500/50"></div>
            <div class="size-3 rounded-full border-2 border-orange-500 bg-background ring-4 ring-orange-500/20"></div>
          </div>

          <!-- Dark Showcase Card (Meniru Tampilan Card Bawah Template Screenshot) -->
          <div class="mt-6 text-left rounded-2xl bg-neutral-950 text-white border border-neutral-800 p-6 sm:p-10 shadow-2xl relative overflow-hidden max-w-3xl mx-auto">
            <!-- Background Radial Halus di Dalam Card -->
            <div class="pointer-events-none absolute -top-12 right-0 size-72 bg-orange-500/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex flex-col gap-6">
              <div>
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-semibold uppercase tracking-wider bg-orange-500/20 text-orange-400 border border-orange-500/30 mb-2">
                  Layanan Mandiri
                </span>
                <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                  Pengajuan Berkas Digital Tanpa Antrean Loket
                </h3>
                <p class="text-neutral-400 text-sm sm:text-base mt-2 leading-relaxed">
                  Semua permohonan surat diproses terpusat oleh Bagian Administrasi Akademik & Kemahasiswaan (BAAK). Mahasiswa dapat memantau setiap proses verifikasi dengan nomor resi pelacakan unik.
                </p>
              </div>

              <!-- Tombol Aksi di dalam Dark Card (Demo & Katalog / Lacak) -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <Button variant="outline" as-child class="h-12 rounded-xl bg-neutral-900 border-neutral-700 hover:bg-neutral-800 text-white font-medium justify-center gap-2">
                  <NuxtLink :to="isAuthenticated ? (isAdmin ? '/admin' : '/dashboard') : '/login'">
                    <IconDeviceLaptop class="size-4.5 text-neutral-300" />
                    <span>Buka Portal Mahasiswa</span>
                  </NuxtLink>
                </Button>

                <Button variant="outline" as-child class="h-12 rounded-xl bg-neutral-900 border-neutral-700 hover:bg-neutral-800 text-white font-medium justify-center gap-2">
                  <NuxtLink to="/pengajuan/lacak">
                    <IconSearch class="size-4.5 text-neutral-300" />
                    <span>Lacak Permohonan Surat</span>
                  </NuxtLink>
                </Button>
              </div>

              <!-- 3 Indikator Keunggulan di Bawah Card -->
              <div class="grid grid-cols-3 gap-2 pt-4 border-t border-neutral-800/80 text-center">
                <div>
                  <div class="text-lg font-bold text-white">100%</div>
                  <div class="text-[11px] text-neutral-400">Pengajuan Daring</div>
                </div>
                <div>
                  <div class="text-lg font-bold text-orange-400">1 - 2 Hari</div>
                  <div class="text-[11px] text-neutral-400">Estimasi Selesai</div>
                </div>
                <div>
                  <div class="text-lg font-bold text-emerald-400">Resmi</div>
                  <div class="text-[11px] text-neutral-400">Tervalidasi BAAK</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- SECTION: DAFTAR LAYANAN UNGGULAN -->
      <section id="layanan" class="py-16 sm:py-20 border-t border-border/50 bg-muted/20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
          <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">
              Katalog Layanan Dokumen Akademik
            </h2>
            <p class="text-sm sm:text-base text-muted-foreground mt-2">
              Pilih permohonan surat yang Anda perlukan dan lengkapi persyaratannya secara mandiri.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div
              v-for="item in layananPopuler"
              :key="item.id"
              class="group relative rounded-xl border border-border bg-card p-5 shadow-xs transition-all duration-200 hover:border-orange-500/50 hover:shadow-md flex flex-col justify-between"
            >
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-orange-500/10 text-orange-600 dark:text-orange-400">
                    {{ item.tag }}
                  </span>
                  <div class="size-8 rounded-lg bg-muted flex items-center justify-center text-muted-foreground group-hover:bg-orange-500 group-hover:text-white transition-colors">
                    <IconFileText class="size-4" />
                  </div>
                </div>

                <h3 class="font-semibold text-foreground text-base group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                  {{ item.nama }}
                </h3>

                <p class="text-xs text-muted-foreground mt-2 line-clamp-3 leading-relaxed">
                  {{ item.deskripsi }}
                </p>
              </div>

              <div class="pt-4 mt-4 border-t border-border/60 flex items-center justify-between">
                <span class="text-[11px] text-muted-foreground flex items-center gap-1">
                  <IconClock class="size-3 text-orange-500" />
                  {{ item.estimasi }}
                </span>

                <Button variant="ghost" size="sm" as-child class="h-7 text-xs text-orange-600 hover:text-orange-700 hover:bg-orange-500/10 px-2 gap-1">
                  <NuxtLink :to="isAuthenticated ? item.target : '/login'">
                    Ajukan
                    <IconArrowRight class="size-3" />
                  </NuxtLink>
                </Button>
              </div>
            </div>
          </div>

          <div class="mt-8 text-center">
            <Button variant="outline" size="sm" as-child class="gap-1.5">
              <NuxtLink to="/layanan">
                <span>Lihat Semua Katalog Layanan Lengkap</span>
                <IconArrowRight class="size-3.5" />
              </NuxtLink>
            </Button>
          </div>
        </div>
      </section>

      <!-- SECTION: ALUR 4 LANGKAH PENGURUSAN -->
      <section id="alur" class="py-16 sm:py-20 border-t border-border/50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
          <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">
              Alur Pengajuan Praktis & Transparan
            </h2>
            <p class="text-sm sm:text-base text-muted-foreground mt-2">
              Empat langkah mudah pengurusan surat akademik dari awal hingga berkas selesai diterbitkan.
            </p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">
            <div
              v-for="langkah in langkahAlur"
              :key="langkah.no"
              class="rounded-xl border border-border bg-card p-6 flex flex-col justify-between relative shadow-xs"
            >
              <div>
                <span class="text-3xl font-extrabold text-orange-500/30">
                  {{ langkah.no }}
                </span>
                <h4 class="font-semibold text-base text-foreground mt-2">
                  {{ langkah.judul }}
                </h4>
                <p class="text-xs text-muted-foreground mt-2 leading-relaxed">
                  {{ langkah.deskripsi }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- SECTION: KEUNGGULAN SISTEM -->
      <section id="keunggulan" class="py-16 sm:py-20 border-t border-border/50 bg-muted/30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex items-start gap-4">
              <div class="size-10 rounded-lg bg-orange-500/10 text-orange-600 flex items-center justify-center shrink-0">
                <IconClock class="size-5" />
              </div>
              <div>
                <h4 class="font-semibold text-base text-foreground">Pemrosesan Terukur</h4>
                <p class="text-xs text-muted-foreground mt-1 leading-relaxed">
                  Waktu penerbitan jelas sesuai standar operasional layanan, diproses langsung oleh petugas BAAK.
                </p>
              </div>
            </div>

            <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex items-start gap-4">
              <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                <IconShieldCheck class="size-5" />
              </div>
              <div>
                <h4 class="font-semibold text-base text-foreground">Verifikasi Dokumen Resmi</h4>
                <p class="text-xs text-muted-foreground mt-1 leading-relaxed">
                  Setiap surat dilengkapi nomor registrasi resmi universitas yang dapat diverifikasi keasliannya.
                </p>
              </div>
            </div>

            <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex items-start gap-4">
              <div class="size-10 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center shrink-0">
                <IconSearch class="size-5" />
              </div>
              <div>
                <h4 class="font-semibold text-base text-foreground">Pelacakan Real-Time</h4>
                <p class="text-xs text-muted-foreground mt-1 leading-relaxed">
                  Cek langsung progres berkas Anda: terkirim, diproses, selesai, atau perbaikan revisi.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CALL TO ACTION BANNER -->
      <section class="py-16 sm:py-20 border-t border-border/50 bg-background text-center relative overflow-hidden">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 relative z-10">
          <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-foreground">
            Siap Mengurus Surat Akademik Anda?
          </h2>
          <p class="text-muted-foreground text-sm sm:text-base mt-3 max-w-xl mx-auto leading-relaxed">
            Masuk ke portal dengan akun mahasiswa atau login menggunakan Google SSO kampus Anda sekarang juga.
          </p>

          <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <Button size="lg" as-child class="h-11 px-6 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium shadow-xs">
              <NuxtLink :to="isAuthenticated ? (isAdmin ? '/admin' : '/dashboard') : '/login'">
                <span>Masuk ke Portal</span>
                <IconArrowRight class="size-4 ml-1.5" />
              </NuxtLink>
            </Button>
            <Button variant="outline" size="lg" as-child class="h-11 px-6 rounded-lg font-medium">
              <NuxtLink to="/bantuan">
                <IconHelpCircle class="size-4 mr-1.5 text-muted-foreground" />
                <span>Pusat Bantuan & FAQ</span>
              </NuxtLink>
            </Button>
          </div>
        </div>
      </section>
    </main>

    <!-- FOOTER RESMI -->
    <footer class="border-t border-border/60 bg-muted/40 text-muted-foreground text-xs">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <div class="size-6 rounded-md bg-orange-500 text-white flex items-center justify-center font-bold">
            <IconBuildingBank class="size-3.5" />
          </div>
          <span class="font-semibold text-foreground text-sm">Portal Layanan Akademik</span>
        </div>

        <p class="text-center sm:text-right">
          &copy; {{ new Date().getFullYear() }} Bagian Administrasi Akademik & Kemahasiswaan. Hak Cipta Dilindungi.
        </p>
      </div>
    </footer>
  </div>
</template>
