<script setup lang="ts">
import {
  IconAlertCircle,
  IconPlus,
  IconRefresh,
  IconSearch,
} from "@tabler/icons-vue"

import DataTable from "~/components/DataTable.vue"
import SectionCards from "~/components/SectionCards.vue"
import { Button } from "~/components/ui/button"

useSeoMeta({
  title: "Dashboard Layanan Mahasiswa - Portal Akademik",
  description: "Daftar layanan administrasi persuratan dan pengajuan berkas akademik mahasiswa secara online.",
})

const config = useRuntimeConfig()
const apiBase = config.public.apiBase || "http://localhost:8000/api"

// Ambil data layanan secara non-blocking (Lazy) agar warmup & transisi halaman instan
const {
  data: layananResponse,
  status: statusLayanan,
  error: errorLayanan,
  refresh: refreshLayanan,
} = useLazyFetch<any>(`${apiBase}/layanan`, {
  default: () => ({ data: [] }),
})

const loadingLayanan = computed(() => statusLayanan.value === "pending")

// Ambil data statistik dari Laravel API secara lazy
const {
  data: statsResponse,
  refresh: refreshStats,
} = useLazyFetch<any>(`${apiBase}/pengajuan/stats`, {
  default: () => ({ data: {} }),
})

const layananList = computed(() => {
  return layananResponse.value?.data || []
})

const statsData = computed(() => {
  return statsResponse.value?.data || {}
})

const isRefreshing = ref(false)

async function refreshAll() {
  if (isRefreshing.value) return
  isRefreshing.value = true
  try {
    await Promise.all([refreshLayanan(), refreshStats()])
  } catch (err) {
    console.error("Gagal memuat ulang data:", err)
  } finally {
    setTimeout(() => {
      isRefreshing.value = false
    }, 600)
  }
}
</script>

<template>
  <div class="flex flex-col gap-6 min-w-0 max-w-full overflow-x-hidden">
    <!-- Header Bagian Atas -->
    <div class="flex flex-col justify-between gap-4 px-4 sm:flex-row sm:items-center lg:px-6">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
          Portal Layanan Mahasiswa
        </h1>
        <p class="text-sm text-muted-foreground mt-1">
          Ajukan permohonan surat akademik, riset, dan legalisir dokumen secara mandiri dan cepat.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <Button
          variant="outline"
          size="sm"
          @click="refreshAll"
          :disabled="isRefreshing || loadingLayanan"
        >
          <IconRefresh
            class="size-4 mr-1.5"
            :class="{ 'animate-spin': isRefreshing || loadingLayanan }"
          />
          {{ isRefreshing ? 'Memuat...' : 'Muat Ulang' }}
        </Button>
        <Button variant="secondary" size="sm" as-child>
          <NuxtLink to="/pengajuan/lacak">
            <IconSearch class="size-4 mr-1.5" />
            Lacak Status
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

    <!-- Metrik Statistik Cards -->
    <SectionCards :stats="statsData" />

    <!-- Error State -->
    <div v-if="errorLayanan" class="px-4 lg:px-6">
      <div class="rounded-xl border border-destructive/30 bg-destructive/5 p-4 text-destructive flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
          <IconAlertCircle class="size-5 shrink-0" />
          <p class="text-sm font-medium">Gagal memuat layanan akademik.</p>
        </div>
        <Button
          variant="outline"
          size="sm"
          class="border-destructive/40 text-destructive hover:bg-destructive/10 h-8 text-xs shrink-0 self-start sm:self-auto"
          @click="refreshAll"
          :disabled="isRefreshing"
        >
          <IconRefresh
            class="size-3.5 mr-1.5"
            :class="{ 'animate-spin': isRefreshing }"
          />
          {{ isRefreshing ? 'Mencoba...' : 'Coba Lagi' }}
        </Button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else-if="loadingLayanan && !layananList.length" class="px-4 lg:px-6 py-8 text-center text-muted-foreground flex flex-col items-center justify-center gap-2">
      <div class="size-8 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
      <p class="text-sm font-medium">Memuat daftar layanan akademik...</p>
    </div>

    <!-- Data Table Layanan -->
    <div v-else>
      <LazyDataTable :data="layananList" />
    </div>
  </div>
</template>
