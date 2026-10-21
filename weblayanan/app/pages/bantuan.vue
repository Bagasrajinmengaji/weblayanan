<script setup lang="ts">
import {
  IconArrowLeft,
  IconClock,
  IconHelp,
  IconMail,
  IconMapPin,
  IconMessageCircle,
  IconPhone,
  IconPlus,
  IconQuestionMark,
  IconSearch,
} from "@tabler/icons-vue"

import { Badge } from "~/components/ui/badge"
import { Button } from "~/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "~/components/ui/card"

useSeoMeta({
  title: "Pusat Bantuan & FAQ - Portal Mahasiswa",
  description: "Pertanyaan yang sering diajukan seputar pengajuan berkas administrasi dan kontak layanan BAAK.",
})

const faqs = [
  {
    q: "Berapa lama proses verifikasi dan penerbitan surat akademik?",
    a: "Secara umum, proses peninjauan berkas oleh staf administrasi BAAK memerlukan waktu 1 hingga 3 hari kerja tergantung kelengkapan berkas dan antrean penandatanganan pimpinan fakultas/dekanat.",
  },
  {
    q: "Apa saja berkas yang wajib diunggah untuk Surat Keterangan Aktif Kuliah?",
    a: "Anda diwajibkan mengunggah scan/foto jelas Kartu Tanda Mahasiswa (KTM), Bukti Pembayaran UKT / Slip SPP semester berjalan, dan Kartu Rencana Studi (KRS) aktif.",
  },
  {
    q: "Bagaimana jika status pengajuan saya berubah menjadi 'Perlu Perbaikan'?",
    a: "Silakan buka menu 'Cek Status / Lacak', masukkan nomor pelacakan Anda, lalu baca catatan instruksi dari petugas pada kotak oranye. Anda dapat mengunggah ulang dokumen yang diminta atau melapor ke loket BAAK.",
  },
  {
    q: "Apakah surat digital yang terbit dilengkapi tanda tangan resmi?",
    a: "Ya, seluruh surat resmi yang berstatus 'Disetujui' telah dibubuhi Tanda Tangan Elektronik (TTE) dan QR Code verifikasi keabsahan yang sah digunakan untuk keperluan instansi eksternal, kedinasan, BPJS, maupun beasiswa.",
  },
  {
    q: "Format dan batas ukuran file apa saja yang diterima?",
    a: "Sistem mendukung format file dokumen PDF serta gambar JPG, JPEG, dan PNG dengan ukuran maksimum 10 MB per berkas.",
  },
  {
    q: "Bagaimana jika saya lupa atau kehilangan nomor registrasi pelacakan?",
    a: "Anda dapat melihat kembali nomor pelacakan pada menu 'Riwayat Pengajuan', lalu gunakan fitur pencarian dengan mengetikkan Nama Lengkap atau NIM Anda.",
  },
]
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 lg:px-6 w-full flex flex-col gap-6">
    <!-- Breadcrumb & Navigasi -->
    <div class="flex items-center gap-3">
      <Button variant="ghost" size="sm" as-child class="gap-1.5 -ml-2 text-muted-foreground hover:text-foreground">
        <NuxtLink to="/dashboard">
          <IconArrowLeft class="size-4" />
          Kembali ke Dashboard
        </NuxtLink>
      </Button>
    </div>

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl flex items-center gap-2">
          <IconHelp class="size-7 text-primary" />
          Pusat Bantuan & FAQ
        </h1>
        <p class="text-sm text-muted-foreground mt-1">
          Panduan lengkap alur pengajuan persuratan mahasiswa dan informasi kontak layanan akademik.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <Button variant="outline" size="sm" as-child>
          <NuxtLink to="/pengajuan/lacak">
            <IconSearch class="size-4 mr-1.5" />
            Lacak Berkas
          </NuxtLink>
        </Button>
        <Button size="sm" as-child>
          <NuxtLink to="/pengajuan">
            <IconPlus class="size-4 mr-1.5" />
            Ajukan Surat
          </NuxtLink>
        </Button>
      </div>
    </div>

    <!-- Kontak Helpdesk & Layanan Cepat -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <Card class="border shadow-xs">
        <CardContent class="p-5 flex flex-col gap-2">
          <div class="size-9 rounded-lg bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
            <IconMessageCircle class="size-5" />
          </div>
          <span class="font-semibold text-sm text-foreground">WhatsApp Helpdesk</span>
          <p class="text-xs text-muted-foreground">Layanan tanya jawab pesan instan operasional kampus.</p>
          <span class="font-mono text-xs font-semibold text-primary mt-1">+62 812-3456-7890</span>
        </CardContent>
      </Card>

      <Card class="border shadow-xs">
        <CardContent class="p-5 flex flex-col gap-2">
          <div class="size-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
            <IconMail class="size-5" />
          </div>
          <span class="font-semibold text-sm text-foreground">Email Administrasi</span>
          <p class="text-xs text-muted-foreground">Konsultasi persuratan resmi dan kendala dokumen.</p>
          <span class="text-xs font-semibold text-primary mt-1">baak@student.ac.id</span>
        </CardContent>
      </Card>

      <Card class="border shadow-xs">
        <CardContent class="p-5 flex flex-col gap-2">
          <div class="size-9 rounded-lg bg-amber-100 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center">
            <IconMapPin class="size-5" />
          </div>
          <span class="font-semibold text-sm text-foreground">Loket Pelayanan BAAK</span>
          <p class="text-xs text-muted-foreground">Gedung Pelayanan Terpadu Lt. 1, R. 102.</p>
          <span class="text-xs font-semibold text-muted-foreground mt-1">Senin - Jumat, 08.00 - 16.00 WIB</span>
        </CardContent>
      </Card>
    </div>

    <!-- FAQ Accordion / List -->
    <Card class="border shadow-xs">
      <CardHeader>
        <CardTitle class="text-lg font-bold flex items-center gap-2">
          <IconQuestionMark class="size-5 text-primary" />
          Pertanyaan yang Sering Diajukan (FAQ)
        </CardTitle>
        <CardDescription>
          Temukan jawaban instan mengenai syarat, estimasi waktu, dan solusi perbaikan berkas.
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div
          v-for="(faq, idx) in faqs"
          :key="idx"
          class="rounded-lg border p-4 bg-card hover:bg-muted/30 transition-colors space-y-1.5"
        >
          <h3 class="font-semibold text-sm text-foreground flex items-start gap-2">
            <span class="text-primary font-bold">Q{{ idx + 1 }}.</span>
            <span>{{ faq.q }}</span>
          </h3>
          <p class="text-xs sm:text-sm text-muted-foreground pl-6 leading-relaxed">
            {{ faq.a }}
          </p>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
