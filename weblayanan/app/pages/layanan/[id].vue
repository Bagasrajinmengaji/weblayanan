<script setup lang="ts">
import {
  IconAlertCircle,
  IconArrowLeft,
  IconCalendar,
  IconCheck,
  IconClock,
  IconFileCertificate,
  IconFileCheck,
  IconFileText,
  IconHelpCircle,
  IconSend,
  IconShieldCheck,
} from "@tabler/icons-vue"

import { Badge } from "~/components/ui/badge"
import { Button } from "~/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "~/components/ui/card"

const route = useRoute()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase || "http://localhost:8000/api"

const {
  data: layananResponse,
  status: statusLayanan,
  error: errorLayanan,
} = useLazyFetch<any>(`${apiBase}/layanan/${route.params.id}`)

const isLoading = computed(() => statusLayanan.value === "pending")

const layanan = computed(() => {
  return layananResponse.value?.data || null
})

useSeoMeta({
  title: computed(() =>
    layanan.value?.nama_layanan
      ? `${layanan.value.nama_layanan} - Portal Layanan Mahasiswa`
      : "Detail Layanan - Portal Mahasiswa"
  ),
  description: computed(() =>
    layanan.value?.deskripsi || "Informasi persyaratan dan ketentuan pengajuan surat akademik."
  ),
})
</script>

<template>
  <div class="max-w-5xl mx-auto px-4 lg:px-6 w-full flex flex-col gap-6">
    <!-- Breadcrumb & Navigasi -->
    <div class="flex items-center gap-3">
      <Button variant="ghost" size="sm" as-child class="gap-1.5 -ml-2 text-muted-foreground hover:text-foreground">
        <NuxtLink to="/layanan">
          <IconArrowLeft class="size-4" />
          Kembali ke Katalog Layanan
        </NuxtLink>
      </Button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="py-16 text-center text-muted-foreground flex flex-col items-center justify-center gap-2">
      <div class="size-8 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
      <p class="text-sm font-medium">Memuat detail layanan...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="errorLayanan || !layanan" class="rounded-xl border border-destructive/30 bg-destructive/5 p-6 text-destructive flex items-start gap-3">
      <IconAlertCircle class="size-6 shrink-0 mt-0.5" />
      <div>
        <h3 class="font-semibold text-lg">Layanan Tidak Ditemukan</h3>
        <p class="text-sm text-muted-foreground mt-1">
          Layanan administrasi yang Anda cari tidak tersedia atau ID tidak valid.
        </p>
        <Button variant="outline" size="sm" as-child class="mt-4 border-destructive/40 text-destructive">
          <NuxtLink to="/layanan">
            Lihat Semua Layanan
          </NuxtLink>
        </Button>
      </div>
    </div>

    <!-- Detail Layanan Content -->
    <div v-else class="space-y-6">
      <!-- Hero Card Layanan -->
      <Card class="border shadow-xs overflow-hidden">
        <div class="h-2 w-full bg-primary"></div>
        <CardContent class="p-6 sm:p-8">
          <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-3 flex-1">
              <div class="flex items-center gap-2">
                <Badge variant="secondary" class="font-mono text-xs">
                  LAYANAN #{{ layanan.id }}
                </Badge>
                <Badge variant="outline" class="text-xs text-emerald-600 border-emerald-300">
                  <IconCheck class="size-3 mr-1" />
                  Layanan Aktif
                </Badge>
              </div>

              <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">
                {{ layanan.nama_layanan }}
              </h1>

              <p class="text-muted-foreground text-sm sm:text-base leading-relaxed">
                {{ layanan.deskripsi }}
              </p>
            </div>

            <!-- Tombol Aksi Cepat Desktop -->
            <div class="shrink-0 flex flex-col gap-2 w-full md:w-auto">
              <Button size="lg" as-child class="gap-2 shadow-xs">
                <NuxtLink :to="`/pengajuan?layanan_id=${layanan.id}`">
                  <IconSend class="size-4" />
                  Ajukan Permohonan
                </NuxtLink>
              </Button>
              <span class="text-xs text-muted-foreground text-center">
                Proses mandiri online 24/7
              </span>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Grid 2 Kolom: Persyaratan & Prosedur -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Dokumen Persyaratan & Tata Cara (2 span) -->
        <div class="md:col-span-2 space-y-6">
          <!-- Card Persyaratan -->
          <Card class="border shadow-xs">
            <CardHeader class="pb-3">
              <CardTitle class="text-lg font-bold flex items-center gap-2">
                <IconFileCheck class="size-5 text-primary" />
                Daftar Dokumen Persyaratan
              </CardTitle>
              <CardDescription>
                Pastikan Anda telah menyiapkan scan/foto dokumen asli berikut sebelum mengajukan.
              </CardDescription>
            </CardHeader>
            <CardContent>
              <ul class="space-y-3">
                <li
                  v-for="(req, idx) in (layanan.persyaratan || [])"
                  :key="idx"
                  class="flex items-start gap-3 p-3 rounded-lg border bg-muted/20"
                >
                  <div class="size-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                    {{ Number(idx) + 1 }}
                  </div>
                  <div class="flex-1">
                    <p class="font-medium text-sm text-foreground">{{ req }}</p>
                    <p class="text-xs text-muted-foreground mt-0.5">Format file yang diperbolehkan: PDF, JPG, atau PNG (Maksimum 10 MB per file).</p>
                  </div>
                </li>
              </ul>
            </CardContent>
          </Card>

          <!-- Card Alur & Tahapan Pelayanan -->
          <Card class="border shadow-xs">
            <CardHeader class="pb-3">
              <CardTitle class="text-lg font-bold flex items-center gap-2">
                <IconClock class="size-5 text-primary" />
                Alur & Prosedur Verifikasi
              </CardTitle>
              <CardDescription>
                Tahapan penerbitan dokumen resmi dari pengajuan hingga pengambilan.
              </CardDescription>
            </CardHeader>
            <CardContent>
              <ol class="relative border-l border-muted-foreground/30 ml-3 space-y-5">
                <li class="ml-6">
                  <span class="absolute -left-3 flex items-center justify-center size-6 rounded-full bg-primary text-primary-foreground text-xs font-bold">1</span>
                  <h4 class="font-semibold text-sm text-foreground">Pengisian Formulir & Unggah Dokumen</h4>
                  <p class="text-xs text-muted-foreground mt-0.5">Isi identitas NIM dan unggah berkas persyaratan sesuai ketentuan.</p>
                </li>
                <li class="ml-6">
                  <span class="absolute -left-3 flex items-center justify-center size-6 rounded-full bg-muted border text-foreground text-xs font-bold">2</span>
                  <h4 class="font-semibold text-sm text-foreground">Verifikasi Berkas oleh BAAK</h4>
                  <p class="text-xs text-muted-foreground mt-0.5">Petugas memeriksa keabsahan dan kelengkapan dokumen yang diunggah.</p>
                </li>
                <li class="ml-6">
                  <span class="absolute -left-3 flex items-center justify-center size-6 rounded-full bg-muted border text-foreground text-xs font-bold">3</span>
                  <h4 class="font-semibold text-sm text-foreground">Penandatanganan & Pengesahan</h4>
                  <p class="text-xs text-muted-foreground mt-0.5">Surat resmi diproses dan ditandatangani oleh pejabat dekanat/rektorat.</p>
                </li>
                <li class="ml-6">
                  <span class="absolute -left-3 flex items-center justify-center size-6 rounded-full bg-emerald-600 text-white text-xs font-bold">4</span>
                  <h4 class="font-semibold text-sm text-foreground">Penerbitan Surat (Selesai)</h4>
                  <p class="text-xs text-muted-foreground mt-0.5">Surat resmi digital diterbitkan atau siap diambil di loket pelayanan kampus.</p>
                </li>
              </ol>
            </CardContent>
          </Card>
        </div>

        <!-- Kolom Kanan: Rincian SLA & Bantuan (1 span) -->
        <div class="space-y-6">
          <Card class="border shadow-xs">
            <CardHeader class="pb-3">
              <CardTitle class="text-base font-bold flex items-center gap-2">
                <IconShieldCheck class="size-4.5 text-primary" />
                Informasi Pelayanan
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-xs sm:text-sm">
              <div class="flex justify-between py-2 border-b">
                <span class="text-muted-foreground">Estimasi Waktu</span>
                <span class="font-semibold text-foreground">1 - 3 Hari Kerja</span>
              </div>
              <div class="flex justify-between py-2 border-b">
                <span class="text-muted-foreground">Biaya Layanan</span>
                <span class="font-semibold text-emerald-600">Gratis (Rp 0)</span>
              </div>
              <div class="flex justify-between py-2 border-b">
                <span class="text-muted-foreground">Format Surat</span>
                <span class="font-semibold text-foreground">Digital & Fisik</span>
              </div>
              <div class="flex justify-between py-2">
                <span class="text-muted-foreground">Keabsahan</span>
                <span class="font-semibold text-foreground">TTE / Stempel Resmi</span>
              </div>
            </CardContent>
          </Card>

          <!-- Bantuan & Konsultasi -->
          <Card class="border shadow-xs bg-muted/20">
            <CardContent class="p-5 space-y-3">
              <div class="flex items-center gap-2 font-semibold text-sm">
                <IconHelpCircle class="size-4 text-primary" />
                Butuh Bantuan?
              </div>
              <p class="text-xs text-muted-foreground leading-relaxed">
                Jika ada persyaratan yang kurang jelas, silakan buka menu Pusat Bantuan atau hubungi loket BAAK universitas.
              </p>
              <Button variant="outline" size="sm" as-child class="w-full text-xs">
                <NuxtLink to="/bantuan">
                  Buka Pusat Bantuan & FAQ
                </NuxtLink>
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </div>
</template>
