<script setup lang="ts">
import {
  IconAlertCircle,
  IconArrowLeft,
  IconCheck,
  IconClock,
  IconFileAlert,
  IconFileCheck,
  IconFileText,
  IconFilter,
  IconPlus,
  IconRefresh,
  IconSearch,
  IconX,
} from "@tabler/icons-vue"

import { Badge } from "~/components/ui/badge"
import { Button } from "~/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "~/components/ui/card"
import { Input } from "~/components/ui/input"
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
  title: "Riwayat Pengajuan Layanan - Portal Mahasiswa",
  description: "Daftar riwayat seluruh permohonan surat administrasi akademik mahasiswa.",
})

const config = useRuntimeConfig()
const apiBase = config.public.apiBase || "http://localhost:8000/api"

const searchQuery = ref("")
const selectedStatus = ref("all")

// Fetch riwayat pengajuan secara non-blocking (Lazy)
const {
  data: riwayatResponse,
  status: statusRiwayat,
  error: errorRiwayat,
  refresh: refreshRiwayat,
} = useLazyFetch<any>(`${apiBase}/pengajuan/riwayat`, {
  default: () => ({ data: [] }),
})

const isLoading = computed(() => statusRiwayat.value === "pending")

const allRiwayat = computed(() => {
  return riwayatResponse.value?.data || []
})

const isRefreshing = ref(false)

async function handleRefresh() {
  if (isRefreshing.value) return
  isRefreshing.value = true
  try {
    await refreshRiwayat()
  } catch (err) {
    console.error("Gagal menyegarkan riwayat:", err)
  } finally {
    setTimeout(() => {
      isRefreshing.value = false
    }, 600)
  }
}

// Filter data di sisi frontend untuk respon instan
const filteredRiwayat = computed(() => {
  return allRiwayat.value.filter((item: any) => {
    // Filter status
    if (selectedStatus.value !== "all" && item.status !== selectedStatus.value) {
      return false
    }

    // Filter teks pencarian
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const matchNomor = item.nomor_pelacakan?.toLowerCase().includes(q)
      const matchNama = item.nama_mahasiswa?.toLowerCase().includes(q)
      const matchNim = item.nim?.toLowerCase().includes(q)
      const matchLayanan = item.layanan?.nama_layanan?.toLowerCase().includes(q)
      return matchNomor || matchNama || matchNim || matchLayanan
    }

    return true
  })
})

function formatDate(dateStr: string) {
  if (!dateStr) return "-"
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  })
}
</script>

<template>
  <div class="max-w-6xl mx-auto px-4 lg:px-6 w-full min-w-0 flex flex-col gap-6 overflow-x-hidden">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2">
          <Button variant="ghost" size="sm" as-child class="gap-1.5 -ml-2 text-muted-foreground hover:text-foreground">
            <NuxtLink to="/">
              <IconArrowLeft class="size-4" />
              Kembali
            </NuxtLink>
          </Button>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl mt-1">
          Riwayat Pengajuan Mahasiswa
        </h1>
        <p class="text-sm text-muted-foreground">
          Pantau seluruh catatan berkas permohonan yang telah diajukan ke bagian akademik.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <Button
          variant="outline"
          size="sm"
          @click="handleRefresh"
          :disabled="isRefreshing || isLoading"
        >
          <IconRefresh
            class="size-4 mr-1.5"
            :class="{ 'animate-spin': isRefreshing || isLoading }"
          />
          {{ isRefreshing ? 'Menyegarkan...' : 'Segarkan' }}
        </Button>
        <Button size="sm" as-child>
          <NuxtLink to="/pengajuan">
            <IconPlus class="size-4 mr-1.5" />
            Ajukan Baru
          </NuxtLink>
        </Button>
      </div>
    </div>

    <!-- Filter & Toolbar Bar -->
    <Card class="border shadow-xs">
      <CardContent class="p-4">
        <div class="flex flex-col sm:flex-row items-center gap-3">
          <!-- Input Search -->
          <div class="relative flex-1 w-full">
            <IconSearch class="size-4 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none" />
            <Input
              v-model="searchQuery"
              placeholder="Cari berdasarkan nomor pelacakan, nama, NIM, atau nama layanan..."
              class="pl-9 pr-8"
            />
            <button
              v-if="searchQuery"
              type="button"
              class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
              @click="searchQuery = ''"
            >
              <IconX class="size-4" />
            </button>
          </div>

          <!-- Dropdown Filter Status -->
          <div class="w-full sm:w-55">
            <Select v-model="selectedStatus">
              <SelectTrigger>
                <div class="flex items-center gap-2">
                  <IconFilter class="size-4 text-muted-foreground" />
                  <SelectValue placeholder="Semua Status" />
                </div>
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Status</SelectItem>
                <SelectItem value="Pending">Pending (Antrean)</SelectItem>
                <SelectItem value="Diproses">Sedang Diproses</SelectItem>
                <SelectItem value="Perlu Perbaikan">Perlu Perbaikan</SelectItem>
                <SelectItem value="Disetujui">Disetujui / Selesai</SelectItem>
                <SelectItem value="Ditolak">Ditolak</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Error State -->
    <div v-if="errorRiwayat" class="rounded-xl border border-destructive/30 bg-destructive/5 p-5 text-destructive flex items-start gap-3">
      <IconAlertCircle class="size-5 shrink-0 mt-0.5" />
      <div class="flex-1">
        <h3 class="font-semibold text-base">Gagal memuat riwayat pengajuan</h3>
        <p class="text-sm text-muted-foreground mt-1">Terjadi kendala saat mengambil data riwayat pengajuan dari server.</p>
        <Button
          variant="outline"
          size="sm"
          class="mt-3 border-destructive/40 text-destructive hover:bg-destructive/10"
          @click="handleRefresh"
          :disabled="isRefreshing"
        >
          <IconRefresh
            class="size-4 mr-1.5"
            :class="{ 'animate-spin': isRefreshing }"
          />
          {{ isRefreshing ? 'Mencoba...' : 'Coba Lagi' }}
        </Button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else-if="isLoading" class="py-12 text-center text-muted-foreground flex flex-col items-center justify-center gap-2">
      <div class="size-8 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
      <p class="text-sm font-medium">Memuat data riwayat...</p>
    </div>

    <!-- Empty State -->
    <Card v-else-if="filteredRiwayat.length === 0" class="border shadow-xs text-center py-12 px-4">
      <CardContent class="flex flex-col items-center gap-3">
        <div class="size-12 rounded-full bg-muted flex items-center justify-center text-muted-foreground">
          <IconFileText class="size-6" />
        </div>
        <h3 class="text-lg font-semibold">Tidak Ada Pengajuan Ditemukan</h3>
        <p class="text-sm text-muted-foreground max-w-sm">
          {{ searchQuery || selectedStatus !== 'all' ? 'Tidak ada data pengajuan yang cocok dengan filter pencarian.' : 'Belum ada berkas pengajuan layanan yang tersimpan di sistem.' }}
        </p>
        <Button v-if="searchQuery || selectedStatus !== 'all'" variant="outline" size="sm" @click="searchQuery = ''; selectedStatus = 'all'">
          Reset Filter
        </Button>
        <Button v-else size="sm" as-child class="mt-2">
          <NuxtLink to="/pengajuan">
            <IconPlus class="size-4 mr-1.5" />
            Ajukan Surat Sekarang
          </NuxtLink>
        </Button>
      </CardContent>
    </Card>

    <!-- Tabel Data Riwayat -->
    <div v-else class="overflow-hidden rounded-lg border bg-card shadow-xs">
      <Table>
        <TableHeader class="bg-muted/50">
          <TableRow>
            <TableHead class="font-semibold">Nomor Pelacakan</TableHead>
            <TableHead class="font-semibold">Mahasiswa</TableHead>
            <TableHead class="font-semibold">Layanan</TableHead>
            <TableHead class="font-semibold">Tanggal</TableHead>
            <TableHead class="font-semibold">Status</TableHead>
            <TableHead class="font-semibold text-right">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="item in filteredRiwayat" :key="item.id" class="hover:bg-muted/30">
            <!-- Nomor Pelacakan -->
            <TableCell>
              <NuxtLink
                :to="`/pengajuan/lacak?nomor=${encodeURIComponent(item.nomor_pelacakan)}`"
                class="font-mono text-sm font-semibold text-primary hover:underline flex items-center gap-1.5"
              >
                {{ item.nomor_pelacakan }}
              </NuxtLink>
            </TableCell>

            <!-- Identitas Mahasiswa -->
            <TableCell>
              <div class="flex flex-col">
                <span class="font-medium text-foreground text-sm">{{ item.nama_mahasiswa }}</span>
                <span class="font-mono text-xs text-muted-foreground">NIM: {{ item.nim }}</span>
              </div>
            </TableCell>

            <!-- Nama Layanan -->
            <TableCell>
              <span class="text-sm text-foreground line-clamp-1">
                {{ item.layanan?.nama_layanan || '-' }}
              </span>
            </TableCell>

            <!-- Tanggal -->
            <TableCell>
              <span class="text-xs text-muted-foreground whitespace-nowrap">
                {{ formatDate(item.created_at) }}
              </span>
            </TableCell>

            <!-- Status Badge -->
            <TableCell>
              <Badge
                v-if="item.status === 'Disetujui'"
                class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium text-xs gap-1"
              >
                <IconFileCheck class="size-3" />
                Disetujui
              </Badge>
              <Badge
                v-else-if="item.status === 'Diproses'"
                class="bg-amber-500 hover:bg-amber-600 text-white font-medium text-xs gap-1"
              >
                <IconClock class="size-3" />
                Diproses
              </Badge>
              <Badge
                v-else-if="item.status === 'Perlu Perbaikan'"
                class="bg-orange-600 hover:bg-orange-700 text-white font-medium text-xs gap-1"
              >
                <IconFileAlert class="size-3" />
                Perlu Perbaikan
              </Badge>
              <Badge
                v-else-if="item.status === 'Ditolak'"
                class="bg-destructive hover:bg-destructive/90 text-white font-medium text-xs gap-1"
              >
                <IconX class="size-3" />
                Ditolak
              </Badge>
              <Badge
                v-else
                variant="secondary"
                class="font-medium text-xs gap-1"
              >
                <IconClock class="size-3" />
                Pending
              </Badge>
            </TableCell>

            <!-- Aksi -->
            <TableCell class="text-right">
              <Button variant="ghost" size="sm" as-child class="h-8 gap-1.5 text-xs">
                <NuxtLink :to="`/pengajuan/lacak?nomor=${encodeURIComponent(item.nomor_pelacakan)}`">
                  <IconSearch class="size-3.5" />
                  Lacak
                </NuxtLink>
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </div>
</template>
