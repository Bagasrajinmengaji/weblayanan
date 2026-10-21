<script setup lang="ts">
import {
  IconAlertCircle,
  IconArrowLeft,
  IconBuilding,
  IconCalendar,
  IconCheck,
  IconClock,
  IconDownload,
  IconExternalLink,
  IconFileAlert,
  IconFileCheck,
  IconFileText,
  IconHelpCircle,
  IconInfoCircle,
  IconMail,
  IconPaperclip,
  IconPhone,
  IconRefresh,
  IconSearch,
  IconSend,
  IconUpload,
  IconUser,
  IconX,
} from "@tabler/icons-vue"

import { Badge } from "~/components/ui/badge"
import { Button } from "~/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "~/components/ui/card"
import { Input } from "~/components/ui/input"
import { Label } from "~/components/ui/label"

useSeoMeta({
  title: "Pelacakan Status Pengajuan - Portal Mahasiswa",
  description: "Pantau perkembangan dan status verifikasi berkas permohonan surat administrasi akademik secara real-time.",
})

const route = useRoute()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase || "http://localhost:8000/api"

const nomorInput = ref("")
const isLoading = ref(false)
const errorText = ref<string | null>(null)
const pengajuanData = ref<any | null>(null)

// State form mini unggah perbaikan
const isRevisionFormOpen = ref(false)
const revisionFile = ref<File | null>(null)
const revisionNote = ref("")
const revisionError = ref<string | null>(null)
const isSubmittingRevision = ref(false)
const revisionSuccessMsg = ref<string | null>(null)

// Format tanggal ramah pengguna
function formatDate(dateStr: string) {
  if (!dateStr) return "-"
  return new Date(dateStr).toLocaleString("id-ID", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  })
}

async function lacakPengajuan(nomor?: string) {
  const code = (nomor || nomorInput.value || "").trim()
  if (!code) {
    errorText.value = "Masukkan nomor pelacakan terlebih dahulu."
    return
  }

  isLoading.value = true
  errorText.value = null
  pengajuanData.value = null
  isRevisionFormOpen.value = false
  revisionFile.value = null
  revisionNote.value = ""
  revisionError.value = null
  revisionSuccessMsg.value = null

  try {
    const res = await $fetch<any>(`${apiBase}/pengajuan/lacak/${encodeURIComponent(code)}`)
    if (res.status === "success" && res.data) {
      pengajuanData.value = res.data
    } else {
      errorText.value = res.message || "Nomor pelacakan tidak ditemukan."
    }
  } catch (err: any) {
    errorText.value = err?.data?.message || err?.message || "Nomor pelacakan tidak ditemukan. Pastikan format nomor pelacakan sudah benar."
  } finally {
    isLoading.value = false
  }
}

function handleRevisionFileChange(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    const file = target.files[0]
    if (!file) return

    revisionError.value = null
    const ext = "." + file.name.split(".").pop()?.toLowerCase()
    const allowed = [".pdf", ".jpg", ".jpeg", ".png"]

    if (!allowed.includes(ext)) {
      revisionError.value = `Format file "${file.name}" tidak didukung. Harap unggah PDF, JPG, atau PNG.`
      target.value = ""
      return
    }

    if (file.size > 10 * 1024 * 1024) {
      revisionError.value = `Ukuran file (${(file.size / (1024 * 1024)).toFixed(1)} MB) melebihi batas 10MB.`
      target.value = ""
      return
    }

    revisionFile.value = file
  }
}

async function submitRevision() {
  if (!revisionFile.value) {
    revisionError.value = "Silakan pilih berkas perbaikan terlebih dahulu."
    return
  }

  isSubmittingRevision.value = true
  revisionError.value = null

  try {
    const formData = new FormData()
    formData.append("berkas_perbaikan", revisionFile.value)
    if (revisionNote.value.trim()) {
      formData.append("catatan_pemohon", revisionNote.value.trim())
    }

    const res = await $fetch<any>(`${apiBase}/pengajuan/${encodeURIComponent(pengajuanData.value.nomor_pelacakan)}/unggah-revisi`, {
      method: "POST",
      body: formData,
    })

    if (res.status === "success") {
      pengajuanData.value = res.data
      revisionSuccessMsg.value = res.message || "Berkas perbaikan berhasil dikirim ke BAAK."
      isRevisionFormOpen.value = false
      revisionFile.value = null
      revisionNote.value = ""
    } else {
      revisionError.value = res.message || "Gagal mengunggah berkas perbaikan."
    }
  } catch (err: any) {
    revisionError.value = err?.data?.message || err?.message || "Terjadi kesalahan saat mengunggah berkas."
  } finally {
    isSubmittingRevision.value = false
  }
}

function triggerDownloadSurat() {
  // Simulasi download dokumen resmi
  const namaFile = `Surat_${pengajuanData.value?.layanan?.nama_layanan?.replace(/\s+/g, '_') || 'Layanan'}_${pengajuanData.value?.nomor_pelacakan}.pdf`
  const link = document.createElement("a")
  link.href = "#"
  link.setAttribute("download", namaFile)
  alert(`Memulai unduhan dokumen resmi digital:\n${namaFile}\n\nDokumen dilengkapi tanda tangan digital dan QR-Code verifikasi akademik.`)
}

// Inisialisasi otomatis jika ada query param ?nomor=
onMounted(() => {
  if (route.query.nomor) {
    nomorInput.value = String(route.query.nomor)
    lacakPengajuan(nomorInput.value)
  }
})
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 lg:px-6 w-full flex flex-col gap-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-3">
      <Button variant="ghost" size="sm" as-child class="gap-1.5 -ml-2 text-muted-foreground hover:text-foreground">
        <NuxtLink to="/dashboard">
          <IconArrowLeft class="size-4" />
          Kembali ke Dashboard
        </NuxtLink>
      </Button>
    </div>

    <!-- Search Section -->
    <Card class="border shadow-xs">
      <CardHeader>
        <CardTitle class="text-xl sm:text-2xl font-bold flex items-center gap-2">
          <IconSearch class="size-6 text-primary" />
          Lacak Status Pengajuan Layanan
        </CardTitle>
        <CardDescription>
          Masukkan nomor registrasi pelacakan (contoh: <code class="font-mono font-semibold">REQ-20261016-1002</code>) untuk memeriksa progres verifikasi dokumen Anda.
        </CardDescription>
      </CardHeader>

      <CardContent>
        <form @submit.prevent="lacakPengajuan()" class="flex flex-col sm:flex-row gap-3">
          <div class="relative flex-1">
            <Input
              v-model="nomorInput"
              placeholder="Masukkan nomor pelacakan (REQ-YYYYMMDD-XXXX)..."
              class="font-mono text-base uppercase pl-3 pr-8"
              required
            />
            <button
              v-if="nomorInput"
              type="button"
              class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
              @click="nomorInput = ''; pengajuanData = null; errorText = null"
            >
              <IconX class="size-4" />
            </button>
          </div>
          <Button type="submit" :disabled="isLoading" class="gap-2 shrink-0 min-w-32.5">
            <span v-if="isLoading" class="size-4 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></span>
            <IconSearch v-else class="size-4" />
            <span>{{ isLoading ? 'Mencari...' : 'Lacak Status' }}</span>
          </Button>
        </form>

        <!-- Bantuan Lupa Kode Pelacakan (Revisi #2.3) -->
        <div class="mt-2.5 flex items-center gap-1.5 text-xs text-muted-foreground">
          <IconHelpCircle class="size-3.5 text-primary shrink-0" />
          <span>Lupa nomor pelacakan? Cari berdasarkan Nama atau NIM di menu</span>
          <NuxtLink to="/pengajuan/riwayat" class="font-medium text-primary hover:underline">
            Riwayat Pengajuan &rarr;
          </NuxtLink>
        </div>

        <!-- Pesan Error / Tidak Ditemukan -->
        <div v-if="errorText" class="mt-4 rounded-lg border border-destructive/30 bg-destructive/5 p-4 text-sm text-destructive flex items-start gap-2.5">
          <IconAlertCircle class="size-5 shrink-0 mt-0.5" />
          <div>
            <span class="font-semibold">Pencarian Gagal</span>
            <p class="mt-0.5 text-xs text-muted-foreground">{{ errorText }}</p>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Hasil Pelacakan -->
    <div v-if="pengajuanData" class="space-y-6">
      <!-- Alert Sukses Unggah Revisi -->
      <div v-if="revisionSuccessMsg" class="rounded-xl border border-emerald-300 bg-emerald-50/80 dark:bg-emerald-950/20 p-4 text-sm text-emerald-900 dark:text-emerald-200 flex items-center gap-2.5">
        <IconCheck class="size-5 text-emerald-600 shrink-0" />
        <span>{{ revisionSuccessMsg }}</span>
      </div>

      <!-- Status Card Utama -->
      <Card class="border shadow-xs overflow-hidden">
        <div
          class="h-2 w-full"
          :class="{
            'bg-amber-500': pengajuanData.status === 'Diproses',
            'bg-slate-400': pengajuanData.status === 'Pending',
            'bg-orange-600': pengajuanData.status === 'Perlu Perbaikan',
            'bg-destructive': pengajuanData.status === 'Ditolak',
            'bg-emerald-500': pengajuanData.status === 'Disetujui',
          }"
        ></div>

        <CardContent class="p-6">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b">
            <div>
              <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Nomor Registrasi</span>
              <h2 class="text-2xl font-bold font-mono text-primary mt-0.5">
                {{ pengajuanData.nomor_pelacakan }}
              </h2>
              <p class="text-sm font-medium text-foreground mt-1">
                {{ pengajuanData.layanan?.nama_layanan || 'Layanan Akademik' }}
              </p>
            </div>

            <!-- Badge Status -->
            <div class="flex items-center gap-2">
              <Badge
                v-if="pengajuanData.status === 'Disetujui'"
                class="px-3 py-1.5 text-sm gap-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold"
              >
                <IconFileCheck class="size-4" />
                Disetujui / Selesai
              </Badge>
              <Badge
                v-else-if="pengajuanData.status === 'Diproses'"
                class="px-3 py-1.5 text-sm gap-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold"
              >
                <IconClock class="size-4" />
                Sedang Diproses
              </Badge>
              <Badge
                v-else-if="pengajuanData.status === 'Perlu Perbaikan'"
                class="px-3 py-1.5 text-sm gap-1.5 bg-orange-600 hover:bg-orange-700 text-white font-semibold"
              >
                <IconFileAlert class="size-4" />
                Perlu Perbaikan
              </Badge>
              <Badge
                v-else-if="pengajuanData.status === 'Ditolak'"
                class="px-3 py-1.5 text-sm gap-1.5 bg-destructive hover:bg-destructive/90 text-white font-semibold"
              >
                <IconX class="size-4" />
                Ditolak
              </Badge>
              <Badge
                v-else
                variant="secondary"
                class="px-3 py-1.5 text-sm gap-1.5 font-semibold"
              >
                <IconClock class="size-4" />
                Menunggu Antrean (Pending)
              </Badge>
            </div>
          </div>

          <!-- Section Kasus 1: Status 'Disetujui' (Revisi #2.2 - Kejelasan Aksi Penyelesaian) -->
          <div
            v-if="pengajuanData.status === 'Disetujui'"
            class="mt-6 rounded-xl border border-emerald-300 bg-emerald-50/60 dark:bg-emerald-950/20 p-5 space-y-4"
          >
            <div class="flex items-start gap-3">
              <div class="size-9 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center shrink-0">
                <IconCheck class="size-5" />
              </div>
              <div class="flex-1">
                <h3 class="font-bold text-base text-emerald-950 dark:text-emerald-100">
                  Permohonan Surat Telah Disetujui & Diterbitkan!
                </h3>
                <p class="text-xs sm:text-sm text-emerald-800 dark:text-emerald-300 mt-0.5">
                  Dokumen resmi Anda telah diverifikasi oleh BAAK dan ditandatangani secara elektronik (BSrE/TTE).
                </p>
              </div>
            </div>

            <!-- Tombol Utama Download PDF Digital -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
              <Button size="lg" class="gap-2 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm" @click="triggerDownloadSurat">
                <IconDownload class="size-5" />
                Unduh Surat Digital (PDF)
              </Button>
            </div>

            <!-- Informasi Lokasi Pengambilan Fisik Dokumen Cetak -->
            <div class="rounded-lg border border-emerald-200 dark:border-emerald-800 bg-white dark:bg-card p-3.5 text-xs text-muted-foreground flex items-start gap-3 mt-3">
              <IconBuilding class="size-4.5 text-primary shrink-0 mt-0.5" />
              <div>
                <p class="font-semibold text-foreground">
                  Atau ambil dokumen cetak asli bertanda tangan basah di loket:
                </p>
                <p class="mt-0.5">
                  <strong>Loket BAAK Ruang 102 (Gedung Rektorat Lt. 1)</strong> &bull; Jadwal Layanan: <strong>Senin &ndash; Jumat, 08:00 &ndash; 15:00 WIB</strong>
                </p>
                <p class="text-[11px] text-muted-foreground mt-0.5 italic">
                  *Harap membawa Kartu Tanda Mahasiswa (KTM) asli dan menunjukkan nomor pelacakan ini kepada petugas loket.
                </p>
              </div>
            </div>
          </div>

          <!-- Section Kasus 2: Status 'Perlu Perbaikan' (Revisi #2.1 - Callout & Form Inline Unggah Ulang) -->
          <div
            v-if="pengajuanData.status === 'Perlu Perbaikan'"
            class="mt-6 rounded-xl border border-orange-300 bg-orange-50/80 dark:bg-orange-950/25 p-5 space-y-4"
          >
            <div class="flex items-start gap-3">
              <div class="size-9 rounded-full bg-orange-100 dark:bg-orange-900/40 text-orange-600 flex items-center justify-center shrink-0">
                <IconFileAlert class="size-5" />
              </div>
              <div class="flex-1">
                <h3 class="font-bold text-base text-orange-950 dark:text-orange-100">
                  Berkas Memerlukan Perbaikan / Revisi
                </h3>
                <p class="text-xs sm:text-sm text-orange-800 dark:text-orange-300 mt-0.5">
                  Petugas administrasi menemukan ketidaksesuaian pada dokumen yang diunggah. Mohon periksa catatan di bawah ini.
                </p>
              </div>
            </div>

            <!-- Callout Box Catatan Revisi dari BAAK -->
            <div class="rounded-lg border border-orange-200 dark:border-orange-800 bg-white dark:bg-card p-4 text-xs sm:text-sm shadow-xs">
              <div class="font-semibold text-orange-700 dark:text-orange-400 flex items-center gap-1.5">
                <IconInfoCircle class="size-4" />
                Catatan & Arahan Revisi Petugas BAAK:
              </div>
              <p class="mt-1.5 leading-relaxed text-foreground whitespace-pre-line font-medium">
                {{ pengajuanData.catatan_revisi || 'Harap periksa kembali dokumen yang Anda lampirkan dan unggah versi yang terbaru/jelas.' }}
              </p>
            </div>

            <!-- Tombol Toggle Form Mini Inline Unggah Berkas -->
            <div v-if="!isRevisionFormOpen">
              <Button class="gap-2 bg-orange-600 hover:bg-orange-700 text-white" @click="isRevisionFormOpen = true">
                <IconUpload class="size-4" />
                Unggah Berkas Perbaikan Sekarang
              </Button>
            </div>

            <!-- Form Mini Inline Unggah Berkas Perbaikan -->
            <div v-else class="rounded-xl border bg-card p-4 sm:p-5 space-y-4 shadow-sm animate-in fade-in duration-200">
              <div class="flex items-center justify-between border-b pb-2">
                <h4 class="font-semibold text-sm text-foreground flex items-center gap-2">
                  <IconUpload class="size-4 text-primary" />
                  Form Unggah Berkas Revisi
                </h4>
                <Button variant="ghost" size="sm" class="h-7 text-xs" @click="isRevisionFormOpen = false">
                  Batal
                </Button>
              </div>

              <div class="space-y-3">
                <div class="space-y-1.5">
                  <Label class="text-xs font-semibold">Pilih Berkas Baru (.pdf, .jpg, .png max 10MB) <span class="text-destructive">*</span></Label>
                  <input
                    type="file"
                    accept=".pdf,.jpg,.jpeg,.png"
                    @change="handleRevisionFileChange"
                    class="block w-full text-xs text-muted-foreground file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-primary-foreground hover:file:bg-primary/90 cursor-pointer border rounded-md p-1"
                  />
                  <div v-if="revisionFile" class="flex items-center gap-2 text-xs text-emerald-600 font-medium pt-1">
                    <IconPaperclip class="size-3.5" />
                    <span>File siap diunggah: {{ revisionFile.name }} ({{ (revisionFile.size / 1024 / 1024).toFixed(2) }} MB)</span>
                  </div>
                </div>

                <div class="space-y-1.5">
                  <Label class="text-xs font-semibold">Catatan Respon Mahasiswa (Opsional)</Label>
                  <textarea
                    v-model="revisionNote"
                    rows="2"
                    placeholder="Contoh: Saya sudah mengunggah ulang scan KRS yang sudah ditandatangani Dosen PA."
                    class="flex min-h-16 w-full rounded-md border border-input bg-transparent px-3 py-1.5 text-xs shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                  ></textarea>
                </div>

                <p v-if="revisionError" class="text-xs text-destructive flex items-center gap-1">
                  <IconAlertCircle class="size-3.5" />
                  <span>{{ revisionError }}</span>
                </p>

                <div class="flex items-center justify-end gap-2 pt-2">
                  <Button variant="outline" size="sm" class="text-xs" @click="isRevisionFormOpen = false">
                    Tutup
                  </Button>
                  <Button
                    size="sm"
                    class="gap-1.5 text-xs bg-orange-600 hover:bg-orange-700 text-white"
                    :disabled="isSubmittingRevision || !revisionFile"
                    @click="submitRevision"
                  >
                    <span v-if="isSubmittingRevision" class="size-3.5 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    <IconSend v-else class="size-3.5" />
                    <span>{{ isSubmittingRevision ? 'Mengirim...' : 'Kirim Berkas Perbaikan' }}</span>
                  </Button>
                </div>
              </div>
            </div>
          </div>

          <!-- Alert Box Catatan Petugas untuk Status Lainnya -->
          <div
            v-if="pengajuanData.catatan_revisi && pengajuanData.status !== 'Perlu Perbaikan' && pengajuanData.status !== 'Disetujui'"
            class="mt-6 rounded-lg p-4 text-sm"
            :class="{
              'border border-destructive/30 bg-destructive/5 text-destructive': pengajuanData.status === 'Ditolak',
              'border bg-muted/40 text-muted-foreground': pengajuanData.status === 'Pending' || pengajuanData.status === 'Diproses',
            }"
          >
            <div class="font-semibold flex items-center gap-1.5">
              <IconFileAlert class="size-4.5" />
              Catatan & Keterangan Petugas BAAK:
            </div>
            <p class="mt-1 leading-relaxed whitespace-pre-line">{{ pengajuanData.catatan_revisi }}</p>
          </div>

          <!-- Detail Informasi Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Kolom 1: Pemohon -->
            <div class="space-y-3">
              <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
                <IconUser class="size-4 text-primary" />
                Informasi Pemohon
              </h3>
              <div class="rounded-lg border bg-card p-4 space-y-2 text-xs sm:text-sm">
                <div class="flex justify-between py-1 border-b">
                  <span class="text-muted-foreground">Nama Mahasiswa</span>
                  <span class="font-semibold text-foreground">{{ pengajuanData.nama_mahasiswa }}</span>
                </div>
                <div class="flex justify-between py-1 border-b">
                  <span class="text-muted-foreground">NIM</span>
                  <span class="font-mono font-semibold text-foreground">{{ pengajuanData.nim }}</span>
                </div>
                <div class="flex justify-between py-1 border-b">
                  <span class="text-muted-foreground">Email</span>
                  <span class="text-foreground">{{ pengajuanData.email }}</span>
                </div>
                <div class="flex justify-between py-1">
                  <span class="text-muted-foreground">Nomor WhatsApp</span>
                  <span class="text-foreground">{{ pengajuanData.no_hp }}</span>
                </div>
              </div>
            </div>

            <!-- Kolom 2: Layanan & Waktu -->
            <div class="space-y-3">
              <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
                <IconCalendar class="size-4 text-primary" />
                Riwayat Pengajuan
              </h3>
              <div class="rounded-lg border bg-card p-4 space-y-2 text-xs sm:text-sm">
                <div class="flex justify-between py-1 border-b">
                  <span class="text-muted-foreground">Tanggal Pengajuan</span>
                  <span class="text-foreground">{{ formatDate(pengajuanData.created_at) }}</span>
                </div>
                <div class="flex justify-between py-1 border-b">
                  <span class="text-muted-foreground">Pembaruan Terakhir</span>
                  <span class="text-foreground">{{ formatDate(pengajuanData.updated_at) }}</span>
                </div>
                <div class="flex justify-between py-1">
                  <span class="text-muted-foreground">Layanan ID</span>
                  <span class="font-mono text-foreground">#{{ pengajuanData.layanan_id }}</span>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
