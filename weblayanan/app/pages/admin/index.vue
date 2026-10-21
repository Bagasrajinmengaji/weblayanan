<script setup lang="ts">
import {
  IconAlertCircle,
  IconAlertTriangle,
  IconBrandWhatsapp,
  IconCheck,
  IconClock,
  IconDownload,
  IconExternalLink,
  IconFile,
  IconFileCheck,
  IconFileText,
  IconFilter,
  IconMail,
  IconRefresh,
  IconSearch,
  IconShieldCheck,
  IconUser,
  IconX,
} from "@tabler/icons-vue"

import { Badge } from "~/components/ui/badge"
import { Button } from "~/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "~/components/ui/card"
import {
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "~/components/ui/dialog"
import { Input } from "~/components/ui/input"
import { Label } from "~/components/ui/label"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "~/components/ui/select"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "~/components/ui/table"

useSeoMeta({
  title: "Panel Admin BAAK - Verifikasi Pengajuan Surat Mahasiswa",
  description: "Dashboard tata usaha & administrasi akademik untuk verifikasi, revisi, dan penerbitan surat mahasiswa.",
})

const config = useRuntimeConfig()
const apiBase = config.public.apiBase || "http://localhost:8000/api"

// Fetch data secara lazy non-blocking
const {
  data: statsResponse,
  refresh: refreshStats,
} = useLazyFetch<any>(`${apiBase}/pengajuan/stats`, {
  default: () => ({ data: {} }),
})

const {
  data: riwayatResponse,
  status: statusRiwayat,
  refresh: refreshRiwayat,
} = useLazyFetch<any>(`${apiBase}/pengajuan/riwayat`, {
  default: () => ({ data: [] }),
})

const { data: layananResponse } = useLazyFetch<any>(`${apiBase}/layanan`, {
  default: () => ({ data: [] }),
})

const isLoading = computed(() => statusRiwayat.value === "pending")
const stats = computed(() => statsResponse.value?.data || {})
const allSubmissions = computed<any[]>(() => riwayatResponse.value?.data || [])
const layananList = computed<any[]>(() => layananResponse.value?.data || [])

// State filter & pencarian
const searchQuery = ref("")
const selectedStatus = ref("all")
const selectedLayanan = ref("all")
const isRefreshing = ref(false)

// Filter data lokal reaktif
const filteredSubmissions = computed(() => {
  return allSubmissions.value.filter((item) => {
    // Filter status
    if (selectedStatus.value !== "all" && item.status !== selectedStatus.value) {
      return false
    }

    // Filter jenis layanan
    if (selectedLayanan.value !== "all" && String(item.layanan_id) !== String(selectedLayanan.value)) {
      return false
    }

    // Filter kata kunci pencarian
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const matchTrack = item.nomor_pelacakan?.toLowerCase().includes(q)
      const matchName = item.nama_mahasiswa?.toLowerCase().includes(q)
      const matchNim = item.nim?.toLowerCase().includes(q)
      const matchLayanan = item.layanan?.nama_layanan?.toLowerCase().includes(q)
      if (!matchTrack && !matchName && !matchNim && !matchLayanan) {
        return false
      }
    }

    return true
  })
})

// Hitung badge status
const statusCounts = computed(() => {
  const items = allSubmissions.value
  return {
    all: items.length,
    pending: items.filter((i) => i.status === "Pending").length,
    diproses: items.filter((i) => i.status === "Diproses").length,
    revisi: items.filter((i) => i.status === "Perlu Perbaikan").length,
    disetujui: items.filter((i) => i.status === "Disetujui").length,
    ditolak: items.filter((i) => i.status === "Ditolak").length,
  }
})

// Fungsi Refresh All
async function handleRefresh() {
  if (isRefreshing.value) return
  isRefreshing.value = true
  try {
    await Promise.all([refreshStats(), refreshRiwayat()])
  } finally {
    setTimeout(() => {
      isRefreshing.value = false
    }, 500)
  }
}

// Modal Verifikasi State
const isReviewModalOpen = ref(false)
const selectedItem = ref<any | null>(null)
const updateForm = reactive({
  status: "Diproses",
  catatan_revisi: "",
})
const isUpdating = ref(false)
const updateError = ref<string | null>(null)
const updateSuccess = ref<string | null>(null)

function openReviewModal(item: any) {
  selectedItem.value = item
  updateForm.status = item.status === "Pending" ? "Diproses" : item.status
  updateForm.catatan_revisi = item.catatan_revisi || ""
  updateError.value = null
  updateSuccess.value = null
  isReviewModalOpen.value = true
}

// Template catatan cepat untuk admin
const quickTemplates = [
  "Berkas persyaratan lengkap dan valid. Permohonan segera diproses.",
  "Scan KTM buram atau tidak terbaca. Mohon unggah ulang foto/scan KTM yang jelas.",
  "Bukti pembayaran UKT belum sesuai semester berjalan. Silakan unggah bukti semester aktif.",
  "Surat telah selesai diproses dan disetujui. Surat dapat diunduh atau diambil di loket BAAK.",
  "Permohonan ditolak karena masa studi tidak aktif atau berkas tidak memenuhi syarat akademik.",
]

function applyTemplate(tpl: string) {
  updateForm.catatan_revisi = tpl
}

// Kirim perubahan status ke backend: PUT /api/pengajuan/{id}/status
async function submitStatusUpdate() {
  if (!selectedItem.value) return
  isUpdating.value = true
  updateError.value = null
  updateSuccess.value = null

  try {
    const res = await $fetch<any>(`${apiBase}/pengajuan/${selectedItem.value.id}/status`, {
      method: "PUT",
      body: {
        status: updateForm.status,
        catatan_revisi: updateForm.catatan_revisi,
      },
    })

    if (res.status === "success") {
      updateSuccess.value = "Status pengajuan berhasil diperbarui!"
      await Promise.all([refreshRiwayat(), refreshStats()])
      setTimeout(() => {
        isReviewModalOpen.value = false
      }, 1000)
    }
  } catch (err: any) {
    updateError.value = err.data?.message || "Gagal memperbarui status pengajuan. Silakan coba lagi."
  } finally {
    isUpdating.value = false
  }
}

// Format tanggal
function formatDate(dateStr: string) {
  if (!dateStr) return "-"
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  })
}

// Format nomor WhatsApp untuk direct chat
function getWaLink(noHp: string, nama: string, nomor: string) {
  if (!noHp) return "#"
  let clean = noHp.replace(/\D/g, "")
  if (clean.startsWith("0")) clean = "62" + clean.slice(1)
  const text = encodeURIComponent(`Halo ${nama}, kami dari BAAK terkait pengajuan surat akademik Anda (${nomor}).`)
  return `https://wa.me/${clean}?text=${text}`
}

// Ekspor ke CSV
function exportToCsv() {
  if (!filteredSubmissions.value.length) return

  const headers = ["Nomor Pelacakan", "Nama Mahasiswa", "NIM", "Layanan", "No HP", "Email", "Status", "Tanggal Pengajuan", "Catatan"]
  const rows = filteredSubmissions.value.map((item) => [
    item.nomor_pelacakan,
    `"${item.nama_mahasiswa}"`,
    `'${item.nim}`,
    `"${item.layanan?.nama_layanan || '-'}"`,
    `'${item.no_hp || '-'}`,
    item.email || "-",
    item.status,
    formatDate(item.created_at),
    `"${(item.catatan_revisi || '').replace(/"/g, '""')}"`,
  ])

  const csvContent = "data:text/csv;charset=utf-8," + [headers.join(","), ...rows.map((e) => e.join(","))].join("\n")
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement("a")
  link.setAttribute("href", encodedUri)
  link.setAttribute("download", `Rekap_Pengajuan_BAAK_${new Date().toISOString().slice(0, 10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}
</script>

<template>
  <div class="flex flex-col gap-6 min-w-0 max-w-full overflow-x-hidden">
    <!-- Header Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-4 lg:px-6">
      <div>
        <div class="flex items-center gap-2">
          <Badge variant="outline" class="bg-primary/10 text-primary border-primary/20 gap-1.5 px-2.5 py-0.5 text-xs font-semibold">
            <IconShieldCheck class="size-3.5" />
            Panel Khusus Staf BAAK
          </Badge>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl mt-1.5">
          Verifikasi & Layanan Persuratan
        </h1>
        <p class="text-sm text-muted-foreground mt-0.5">
          Kelola persetujuan, permintaan perbaikan berkas, dan penerbitan surat akademik mahasiswa.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <Button
          variant="outline"
          size="sm"
          @click="handleRefresh"
          :disabled="isRefreshing || isLoading"
        >
          <IconRefresh class="size-4 mr-1.5" :class="{ 'animate-spin': isRefreshing || isLoading }" />
          {{ isRefreshing ? 'Memuat...' : 'Segarkan Data' }}
        </Button>

        <Button
          variant="secondary"
          size="sm"
          @click="exportToCsv"
          :disabled="!filteredSubmissions.length"
        >
          <IconDownload class="size-4 mr-1.5" />
          Ekspor CSV
        </Button>
      </div>
    </div>

    <!-- Metrik Cepat Admin -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 px-4 lg:px-6">
      <!-- Card 1: Pending (Butuh Tindakan) -->
      <Card class="border-amber-500/30 bg-amber-500/5 col-span-2 sm:col-span-1 shadow-xs">
        <CardContent class="p-4 flex flex-col justify-between h-full">
          <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-amber-700 dark:text-amber-400">Perlu Verifikasi</span>
            <span class="flex size-2 rounded-full bg-amber-500 animate-pulse"></span>
          </div>
          <div class="mt-2 flex items-baseline gap-1.5">
            <span class="text-2xl sm:text-3xl font-bold text-amber-900 dark:text-amber-100 tabular-nums">
              {{ statusCounts.pending }}
            </span>
            <span class="text-xs text-amber-700/80 dark:text-amber-300">Antrean Baru</span>
          </div>
        </CardContent>
      </Card>

      <!-- Card 2: Diproses -->
      <Card class="border-blue-500/30 bg-blue-500/5 shadow-xs">
        <CardContent class="p-4 flex flex-col justify-between h-full">
          <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Sedang Diproses</span>
          <div class="mt-2 flex items-baseline gap-1.5">
            <span class="text-2xl sm:text-3xl font-bold text-blue-900 dark:text-blue-100 tabular-nums">
              {{ statusCounts.diproses }}
            </span>
            <span class="text-xs text-blue-700/80 dark:text-blue-300">Pemeriksaan</span>
          </div>
        </CardContent>
      </Card>

      <!-- Card 3: Perlu Revisi -->
      <Card class="border-orange-500/30 bg-orange-500/5 shadow-xs">
        <CardContent class="p-4 flex flex-col justify-between h-full">
          <span class="text-xs font-medium text-orange-700 dark:text-orange-400">Menunggu Revisi</span>
          <div class="mt-2 flex items-baseline gap-1.5">
            <span class="text-2xl sm:text-3xl font-bold text-orange-900 dark:text-orange-100 tabular-nums">
              {{ statusCounts.revisi }}
            </span>
            <span class="text-xs text-orange-700/80 dark:text-orange-300">Mahasiswa</span>
          </div>
        </CardContent>
      </Card>

      <!-- Card 4: Selesai / Terbit -->
      <Card class="border-emerald-500/30 bg-emerald-500/5 shadow-xs">
        <CardContent class="p-4 flex flex-col justify-between h-full">
          <span class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Selesai Terbit</span>
          <div class="mt-2 flex items-baseline gap-1.5">
            <span class="text-2xl sm:text-3xl font-bold text-emerald-900 dark:text-emerald-100 tabular-nums">
              {{ statusCounts.disetujui }}
            </span>
            <span class="text-xs text-emerald-700/80 dark:text-emerald-300">Surat Sah</span>
          </div>
        </CardContent>
      </Card>

      <!-- Card 5: Total Masuk -->
      <Card class="border shadow-xs">
        <CardContent class="p-4 flex flex-col justify-between h-full">
          <span class="text-xs font-medium text-muted-foreground">Total Pengajuan</span>
          <div class="mt-2 flex items-baseline gap-1.5">
            <span class="text-2xl sm:text-3xl font-bold text-foreground tabular-nums">
              {{ statusCounts.all }}
            </span>
            <span class="text-xs text-muted-foreground">Berkas</span>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Filter & Master Table Card -->
    <div class="px-4 lg:px-6">
      <Card class="border shadow-xs overflow-hidden">
        <CardHeader class="pb-3 border-b">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Tab Filter Status -->
            <div class="flex flex-wrap items-center gap-1.5">
              <Button
                variant="ghost"
                size="sm"
                class="h-8 text-xs font-medium"
                :class="selectedStatus === 'all' ? 'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground' : 'text-muted-foreground'"
                @click="selectedStatus = 'all'"
              >
                Semua ({{ statusCounts.all }})
              </Button>
              <Button
                variant="ghost"
                size="sm"
                class="h-8 text-xs font-medium gap-1.5"
                :class="selectedStatus === 'Pending' ? 'bg-amber-600 text-white hover:bg-amber-700 hover:text-white' : 'text-amber-600 hover:bg-amber-50'"
                @click="selectedStatus = 'Pending'"
              >
                <span class="size-1.5 rounded-full bg-amber-400"></span>
                Pending ({{ statusCounts.pending }})
              </Button>
              <Button
                variant="ghost"
                size="sm"
                class="h-8 text-xs font-medium"
                :class="selectedStatus === 'Diproses' ? 'bg-blue-600 text-white hover:bg-blue-700 hover:text-white' : 'text-blue-600 hover:bg-blue-50'"
                @click="selectedStatus = 'Diproses'"
              >
                Diproses ({{ statusCounts.diproses }})
              </Button>
              <Button
                variant="ghost"
                size="sm"
                class="h-8 text-xs font-medium"
                :class="selectedStatus === 'Perlu Perbaikan' ? 'bg-orange-600 text-white hover:bg-orange-700 hover:text-white' : 'text-orange-600 hover:bg-orange-50'"
                @click="selectedStatus = 'Perlu Perbaikan'"
              >
                Perlu Revisi ({{ statusCounts.revisi }})
              </Button>
              <Button
                variant="ghost"
                size="sm"
                class="h-8 text-xs font-medium"
                :class="selectedStatus === 'Disetujui' ? 'bg-emerald-600 text-white hover:bg-emerald-700 hover:text-white' : 'text-emerald-600 hover:bg-emerald-50'"
                @click="selectedStatus = 'Disetujui'"
              >
                Disetujui ({{ statusCounts.disetujui }})
              </Button>
            </div>

            <!-- Pencarian & Filter Layanan -->
            <div class="flex flex-wrap items-center gap-2">
              <div class="relative w-full sm:w-64">
                <IconSearch class="absolute left-2.5 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                <Input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Cari NIM, Nama, atau REQ..."
                  class="pl-8.5 h-8.5 text-xs"
                />
              </div>

              <Select v-model="selectedLayanan">
                <SelectTrigger class="w-[180px] h-8.5 text-xs">
                  <SelectValue placeholder="Pilih Jenis Layanan" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Semua Layanan</SelectItem>
                  <SelectItem
                    v-for="layanan in layananList"
                    :key="layanan.id"
                    :value="String(layanan.id)"
                  >
                    {{ layanan.nama_layanan }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </CardHeader>

        <!-- Tabel Pengajuan -->
        <CardContent class="p-0">
          <div v-if="isLoading" class="py-16 text-center text-muted-foreground flex flex-col items-center justify-center gap-2">
            <div class="size-8 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
            <p class="text-sm font-medium">Memuat data pengajuan dari server...</p>
          </div>

          <div v-else-if="filteredSubmissions.length === 0" class="py-16 text-center text-muted-foreground flex flex-col items-center justify-center gap-2">
            <IconFileText class="size-10 text-muted-foreground/60" />
            <p class="text-sm font-semibold text-foreground">Tidak Ada Berkas Ditemukan</p>
            <p class="text-xs text-muted-foreground max-w-sm">
              Tidak ada data pengajuan yang cocok dengan kriteria filter atau pencarian Anda.
            </p>
          </div>

          <div v-else class="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow class="bg-muted/30">
                  <TableHead class="text-xs font-semibold">No. Pelacakan</TableHead>
                  <TableHead class="text-xs font-semibold">Mahasiswa</TableHead>
                  <TableHead class="text-xs font-semibold">Jenis Layanan</TableHead>
                  <TableHead class="text-xs font-semibold">Lampiran</TableHead>
                  <TableHead class="text-xs font-semibold">Tgl Pengajuan</TableHead>
                  <TableHead class="text-xs font-semibold">Status</TableHead>
                  <TableHead class="text-xs font-semibold text-right">Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow
                  v-for="item in filteredSubmissions"
                  :key="item.id"
                  class="hover:bg-muted/20 transition-colors"
                >
                  <!-- No Pelacakan -->
                  <TableCell class="font-mono text-xs font-semibold">
                    <span class="rounded bg-muted px-1.5 py-0.5 text-foreground">
                      {{ item.nomor_pelacakan }}
                    </span>
                  </TableCell>

                  <!-- Mahasiswa -->
                  <TableCell>
                    <div class="flex flex-col">
                      <span class="text-xs font-semibold text-foreground">{{ item.nama_mahasiswa }}</span>
                      <span class="text-[11px] font-mono text-muted-foreground">{{ item.nim }}</span>
                    </div>
                  </TableCell>

                  <!-- Jenis Layanan -->
                  <TableCell class="text-xs font-medium text-foreground max-w-[200px] truncate">
                    {{ item.layanan?.nama_layanan || '-' }}
                  </TableCell>

                  <!-- Lampiran Berkas -->
                  <TableCell class="text-xs text-muted-foreground">
                    <span v-if="Array.isArray(item.berkas_persyaratan)" class="flex items-center gap-1">
                      <IconFile class="size-3.5 text-primary" />
                      {{ item.berkas_persyaratan.length }} berkas
                    </span>
                    <span v-else>-</span>
                  </TableCell>

                  <!-- Tanggal -->
                  <TableCell class="text-xs text-muted-foreground whitespace-nowrap">
                    {{ formatDate(item.created_at) }}
                  </TableCell>

                  <!-- Status Badge -->
                  <TableCell>
                    <Badge
                      v-if="item.status === 'Pending'"
                      variant="outline"
                      class="bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30 text-[11px]"
                    >
                      Pending
                    </Badge>
                    <Badge
                      v-else-if="item.status === 'Diproses'"
                      variant="outline"
                      class="bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/30 text-[11px]"
                    >
                      Diproses
                    </Badge>
                    <Badge
                      v-else-if="item.status === 'Perlu Perbaikan'"
                      variant="outline"
                      class="bg-orange-500/10 text-orange-700 dark:text-orange-400 border-orange-500/30 text-[11px]"
                    >
                      Perlu Revisi
                    </Badge>
                    <Badge
                      v-else-if="item.status === 'Disetujui'"
                      variant="outline"
                      class="bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30 text-[11px]"
                    >
                      Disetujui
                    </Badge>
                    <Badge
                      v-else-if="item.status === 'Ditolak'"
                      variant="outline"
                      class="bg-destructive/10 text-destructive border-destructive/30 text-[11px]"
                    >
                      Ditolak
                    </Badge>
                  </TableCell>

                  <!-- Tombol Aksi -->
                  <TableCell class="text-right">
                    <Button
                      size="sm"
                      variant="outline"
                      class="h-7 text-xs gap-1 border-primary/30 text-primary hover:bg-primary/10"
                      @click="openReviewModal(item)"
                    >
                      <span>Tinjau</span>
                    </Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Modal Review & Verifikasi Pengajuan (LazyDialog) -->
    <LazyDialog v-if="isReviewModalOpen" :open="isReviewModalOpen" @update:open="isReviewModalOpen = $event">
      <DialogContent class="sm:max-w-xl max-h-[88vh] flex flex-col p-5 sm:p-6 gap-3">
        <DialogHeader class="shrink-0 space-y-1">
          <div class="flex items-center justify-between">
            <DialogTitle class="text-base sm:text-lg font-bold flex items-center gap-2">
              <IconFileCheck class="size-5 text-primary" />
              Verifikasi Permohonan Surat
            </DialogTitle>
            <Badge variant="outline" class="font-mono text-xs">
              {{ selectedItem?.nomor_pelacakan }}
            </Badge>
          </div>
          <DialogDescription class="text-xs text-muted-foreground">
            Periksa kelengkapan identitas dan lampiran persyaratan, lalu tentukan persetujuan berkas.
          </DialogDescription>
        </DialogHeader>

        <!-- Body Form Verifikasi (Scrollable) -->
        <div class="space-y-4 py-1 text-sm overflow-y-auto pr-1 flex-1 min-h-0">
          <!-- Alert feedback jika berhasil/gagal -->
          <div v-if="updateSuccess" class="rounded-lg bg-emerald-500/10 border border-emerald-500/30 p-2.5 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2">
            <IconCheck class="size-4 shrink-0" />
            <span>{{ updateSuccess }}</span>
          </div>
          <div v-if="updateError" class="rounded-lg bg-destructive/10 border border-destructive/30 p-2.5 text-destructive text-xs flex items-center gap-2">
            <IconAlertCircle class="size-4 shrink-0" />
            <span>{{ updateError }}</span>
          </div>

          <!-- Bagian 1: Data Identitas Mahasiswa -->
          <div class="rounded-lg border bg-muted/40 p-3 space-y-1.5">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground border-b pb-1">
              Data Mahasiswa Pemohon
            </div>
            <div class="grid grid-cols-3 gap-y-1 gap-x-2 text-xs">
              <span class="text-muted-foreground">Nama:</span>
              <span class="col-span-2 font-medium text-foreground">{{ selectedItem?.nama_mahasiswa }}</span>

              <span class="text-muted-foreground">NIM:</span>
              <span class="col-span-2 font-mono font-medium text-foreground">{{ selectedItem?.nim }}</span>

              <span class="text-muted-foreground">Layanan:</span>
              <span class="col-span-2 font-medium text-primary">{{ selectedItem?.layanan?.nama_layanan || '-' }}</span>

              <span class="text-muted-foreground">WhatsApp:</span>
              <div class="col-span-2 flex items-center gap-2 font-medium">
                <span>{{ selectedItem?.no_hp || '-' }}</span>
                <a
                  v-if="selectedItem?.no_hp"
                  :href="getWaLink(selectedItem.no_hp, selectedItem.nama_mahasiswa, selectedItem.nomor_pelacakan)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1 text-[11px] text-emerald-600 hover:underline"
                >
                  <IconBrandWhatsapp class="size-3.5" />
                  Chat WhatsApp
                </a>
              </div>

              <span class="text-muted-foreground">Email:</span>
              <span class="col-span-2 font-medium text-foreground">{{ selectedItem?.email || '-' }}</span>

              <span class="text-muted-foreground">Tgl Pengajuan:</span>
              <span class="col-span-2 text-muted-foreground">{{ formatDate(selectedItem?.created_at) }}</span>
            </div>
          </div>

          <!-- Bagian 2: Lampiran Dokumen Mahasiswa -->
          <div class="rounded-lg border p-3 space-y-2">
            <div class="flex items-center justify-between border-b pb-1">
              <span class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
                Dokumen Persyaratan yang Diunggah
              </span>
              <Badge variant="secondary" class="text-[10px]">
                {{ Array.isArray(selectedItem?.berkas_persyaratan) ? selectedItem.berkas_persyaratan.length : 0 }} Berkas
              </Badge>
            </div>

            <div v-if="Array.isArray(selectedItem?.berkas_persyaratan) && selectedItem.berkas_persyaratan.length > 0" class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
              <div
                v-for="(file, idx) in selectedItem.berkas_persyaratan"
                :key="idx"
                class="flex items-center justify-between text-xs p-2 rounded-md bg-muted/30 border border-border/50"
              >
                <div class="flex items-center gap-2 min-w-0 flex-1">
                  <IconFile class="size-4 text-primary shrink-0" />
                  <div class="min-w-0">
                    <p class="font-medium text-foreground truncate">{{ file.file_name || `Dokumen Lampiran ${Number(idx) + 1}` }}</p>
                    <p v-if="file.is_revision" class="text-[10px] text-orange-600 font-medium">Berkas Hasil Perbaikan</p>
                  </div>
                </div>

                <a
                  v-if="file.file_url"
                  :href="file.file_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1 text-xs text-primary font-medium hover:underline shrink-0 pl-2"
                >
                  <span>Buka File</span>
                  <IconExternalLink class="size-3" />
                </a>
                <span v-else class="text-[11px] text-muted-foreground shrink-0 pl-2">Tersimpan</span>
              </div>
            </div>
            <div v-else class="text-xs text-muted-foreground py-2 text-center">
              Tidak ada file terunggah untuk permohonan ini.
            </div>
          </div>

          <!-- Bagian 3: Formulir Ubah Status & Beri Catatan -->
          <div class="rounded-lg border p-3 space-y-3 bg-muted/10">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground border-b pb-1">
              Keputusan Verifikasi BAAK
            </div>

            <!-- Pilih Status Baru -->
            <div class="space-y-1.5">
              <Label class="text-xs font-semibold">Tentukan Status Baru:</Label>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  class="text-xs h-8 justify-center gap-1"
                  :class="updateForm.status === 'Disetujui' ? 'border-emerald-600 bg-emerald-600 text-white hover:bg-emerald-700 hover:text-white' : 'hover:border-emerald-500'"
                  @click="updateForm.status = 'Disetujui'"
                >
                  <IconCheck class="size-3.5" />
                  Disetujui
                </Button>

                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  class="text-xs h-8 justify-center gap-1"
                  :class="updateForm.status === 'Diproses' ? 'border-blue-600 bg-blue-600 text-white hover:bg-blue-700 hover:text-white' : 'hover:border-blue-500'"
                  @click="updateForm.status = 'Diproses'"
                >
                  <IconClock class="size-3.5" />
                  Diproses
                </Button>

                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  class="text-xs h-8 justify-center gap-1"
                  :class="updateForm.status === 'Perlu Perbaikan' ? 'border-orange-600 bg-orange-600 text-white hover:bg-orange-700 hover:text-white' : 'hover:border-orange-500'"
                  @click="updateForm.status = 'Perlu Perbaikan'"
                >
                  <IconAlertTriangle class="size-3.5" />
                  Perlu Revisi
                </Button>

                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  class="text-xs h-8 justify-center gap-1"
                  :class="updateForm.status === 'Ditolak' ? 'border-destructive bg-destructive text-destructive-foreground hover:bg-destructive/90 hover:text-destructive-foreground' : 'hover:border-destructive'"
                  @click="updateForm.status = 'Ditolak'"
                >
                  <IconX class="size-3.5" />
                  Ditolak
                </Button>
              </div>
            </div>

            <!-- Catatan Revisi / Alasan -->
            <div class="space-y-1.5 pt-1">
              <div class="flex items-center justify-between">
                <Label class="text-xs font-semibold">Catatan / Alasan untuk Mahasiswa:</Label>
                <span class="text-[10px] text-muted-foreground">Muncul di halaman pelacakan mahasiswa</span>
              </div>

              <!-- Quick Template Buttons -->
              <div class="flex flex-wrap gap-1 text-[10px]">
                <button
                  type="button"
                  class="rounded bg-muted px-1.5 py-0.5 text-muted-foreground hover:text-foreground hover:bg-muted/80"
                  @click="applyTemplate('Berkas lengkap & valid. Surat resmi telah disetujui.')"
                >
                  + Berkas Valid
                </button>
                <button
                  type="button"
                  class="rounded bg-muted px-1.5 py-0.5 text-muted-foreground hover:text-foreground hover:bg-muted/80"
                  @click="applyTemplate('Scan KTM buram/tidak terbaca. Mohon unggah ulang scan KTM yang jelas.')"
                >
                  + KTM Buram
                </button>
                <button
                  type="button"
                  class="rounded bg-muted px-1.5 py-0.5 text-muted-foreground hover:text-foreground hover:bg-muted/80"
                  @click="applyTemplate('Bukti bayar UKT belum sesuai semester berjalan.')"
                >
                  + Bukti UKT Salah
                </button>
              </div>

              <textarea
                v-model="updateForm.catatan_revisi"
                rows="3"
                class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-xs shadow-xs placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring"
                placeholder="Tuliskan catatan perbaikan atau instruksi pengambilan surat..."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Footer Modal -->
        <DialogFooter class="shrink-0 pt-2 border-t flex flex-col-reverse sm:flex-row gap-2">
          <Button
            type="button"
            variant="outline"
            size="sm"
            @click="isReviewModalOpen = false"
            :disabled="isUpdating"
          >
            Tutup
          </Button>

          <Button
            type="button"
            size="sm"
            class="bg-primary text-primary-foreground hover:bg-primary/90 gap-1.5"
            :disabled="isUpdating"
            @click="submitStatusUpdate"
          >
            <span v-if="isUpdating" class="size-3.5 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></span>
            <IconCheck v-else class="size-3.5" />
            <span>{{ isUpdating ? 'Menyimpan...' : 'Simpan Status & Beritahu' }}</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </LazyDialog>
  </div>
</template>
