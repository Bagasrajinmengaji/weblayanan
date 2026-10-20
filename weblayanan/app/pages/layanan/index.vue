<script setup lang="ts">
import {
  IconAlertCircle,
  IconArrowLeft,
  IconCheck,
  IconFileText,
  IconInfoCircle,
  IconPlus,
  IconRefresh,
  IconSearch,
  IconSend,
  IconX,
} from "@tabler/icons-vue"

import { Badge } from "~/components/ui/badge"
import { Button } from "~/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "~/components/ui/card"
import { Input } from "~/components/ui/input"

useSeoMeta({
  title: "Katalog Seluruh Layanan Akademik - Portal Mahasiswa",
  description: "Daftar lengkap seluruh layanan administrasi persuratan dan persyaratan pengajuan berkas akademik mahasiswa.",
})

const config = useRuntimeConfig()
const apiBase = config.public.apiBase || "http://localhost:8000/api"

const searchQuery = ref("")
const isRefreshing = ref(false)

// Fetch daftar semua layanan
const {
  data: layananResponse,
  pending: isLoading,
  error: errorLayanan,
  refresh: refreshLayanan,
} = await useFetch<any>(`${apiBase}/layanan`)

const layananList = computed(() => {
  return (layananResponse.value?.data || []) as any[]
})

// Filter pencarian
const filteredLayanan = computed(() => {
  if (!searchQuery.value.trim()) return layananList.value
  const q = searchQuery.value.toLowerCase().trim()
  return layananList.value.filter((item: any) => {
    const matchName = item.nama_layanan?.toLowerCase().includes(q)
    const matchDesc = item.deskripsi?.toLowerCase().includes(q)
    const matchReq = Array.isArray(item.persyaratan) && item.persyaratan.some((r: string) => r.toLowerCase().includes(q))
    return matchName || matchDesc || matchReq
  })
})

async function handleRefresh() {
  if (isRefreshing.value) return
  isRefreshing.value = true
  try {
    await refreshLayanan()
  } finally {
    setTimeout(() => {
      isRefreshing.value = false
    }, 600)
  }
}
</script>

<template>
  <div class="max-w-6xl mx-auto px-4 lg:px-6 w-full flex flex-col gap-6">
    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2">
          <Button variant="ghost" size="sm" as-child class="gap-1.5 -ml-2 text-muted-foreground hover:text-foreground">
            <NuxtLink to="/">
              <IconArrowLeft class="size-4" />
              Kembali ke Dashboard
            </NuxtLink>
          </Button>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl mt-1">
          Katalog Seluruh Layanan Akademik
        </h1>
        <p class="text-sm text-muted-foreground mt-1">
          Pilih salah satu layanan administrasi kemahasiswaan di bawah ini untuk melihat persyaratan dan mengajukan permohonan secara online.
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
          {{ isRefreshing ? 'Memuat...' : 'Segarkan' }}
        </Button>
        <Button size="sm" as-child>
          <NuxtLink to="/pengajuan">
            <IconPlus class="size-4 mr-1.5" />
            Ajukan Surat
          </NuxtLink>
        </Button>
      </div>
    </div>

    <!-- Toolbar Pencarian -->
    <Card class="border shadow-xs">
      <CardContent class="p-4">
        <div class="relative w-full">
          <IconSearch class="size-4 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none" />
          <Input
            v-model="searchQuery"
            placeholder="Cari berdasarkan nama surat, kata kunci deskripsi, atau jenis berkas persyaratan..."
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
      </CardContent>
    </Card>

    <!-- Error State -->
    <div v-if="errorLayanan" class="rounded-xl border border-destructive/30 bg-destructive/5 p-5 text-destructive flex items-start gap-3">
      <IconAlertCircle class="size-5 shrink-0 mt-0.5" />
      <div class="flex-1">
        <h3 class="font-semibold text-base">Gagal memuat katalog layanan</h3>
        <p class="text-sm text-muted-foreground mt-1">
          Terjadi kendala saat mengambil data layanan dari backend. Silakan coba lagi.
        </p>
        <Button
          variant="outline"
          size="sm"
          class="mt-3 border-destructive/40 text-destructive hover:bg-destructive/10"
          @click="handleRefresh"
          :disabled="isRefreshing"
        >
          <IconRefresh class="size-4 mr-1.5" :class="{ 'animate-spin': isRefreshing }" />
          Coba Lagi
        </Button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else-if="isLoading" class="py-16 text-center text-muted-foreground flex flex-col items-center justify-center gap-2">
      <div class="size-8 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
      <p class="text-sm font-medium">Memuat katalog seluruh layanan...</p>
    </div>

    <!-- Empty State -->
    <Card v-else-if="filteredLayanan.length === 0" class="border shadow-xs text-center py-12 px-4">
      <CardContent class="flex flex-col items-center gap-3">
        <div class="size-12 rounded-full bg-muted flex items-center justify-center text-muted-foreground">
          <IconFileText class="size-6" />
        </div>
        <h3 class="text-lg font-semibold">Layanan Tidak Ditemukan</h3>
        <p class="text-sm text-muted-foreground max-w-sm">
          Tidak ada layanan administrasi yang cocok dengan kata kunci "{{ searchQuery }}".
        </p>
        <Button variant="outline" size="sm" @click="searchQuery = ''">
          Reset Pencarian
        </Button>
      </CardContent>
    </Card>

    <!-- Grid Kartu Seluruh Layanan -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <Card
        v-for="item in filteredLayanan"
        :key="item.id"
        class="border shadow-xs flex flex-col justify-between hover:border-primary/50 hover:shadow-sm transition-all duration-200"
      >
        <CardHeader class="pb-3">
          <div class="flex items-start justify-between gap-3">
            <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
              <IconFileText class="size-5" />
            </div>
            <Badge variant="outline" class="font-mono text-xs">
              ID: #{{ item.id }}
            </Badge>
          </div>
          <CardTitle class="text-lg font-bold text-foreground mt-3 leading-snug">
            {{ item.nama_layanan }}
          </CardTitle>
          <CardDescription class="text-xs text-muted-foreground line-clamp-3 mt-1.5 leading-relaxed">
            {{ item.deskripsi }}
          </CardDescription>
        </CardHeader>

        <CardContent class="py-2 flex-1">
          <div class="space-y-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              Persyaratan Dokumen:
            </span>
            <ul class="space-y-1.5">
              <li
                v-for="(req, idx) in (item.persyaratan || [])"
                :key="idx"
                class="flex items-start gap-2 text-xs text-foreground/90"
              >
                <IconCheck class="size-3.5 text-emerald-600 shrink-0 mt-0.5" />
                <span>{{ req }}</span>
              </li>
            </ul>
          </div>
        </CardContent>

        <CardFooter class="pt-4 border-t flex items-center justify-between gap-2 bg-muted/20">
          <Button variant="ghost" size="sm" as-child class="text-xs gap-1 text-muted-foreground hover:text-foreground">
            <NuxtLink :to="`/layanan/${item.id}`">
              <IconInfoCircle class="size-3.5" />
              Detail
            </NuxtLink>
          </Button>
          <Button size="sm" as-child class="gap-1.5">
            <NuxtLink :to="`/pengajuan?layanan_id=${item.id}`">
              <IconSend class="size-3.5" />
              Ajukan Sekarang
            </NuxtLink>
          </Button>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
