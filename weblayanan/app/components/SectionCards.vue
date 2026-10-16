<script setup lang="ts">
import {
  IconChecklist,
  IconClock,
  IconFileAlert,
  IconFileCheck,
} from "@tabler/icons-vue"

import { Badge } from "~/components/ui/badge"
import {
  Card,
  CardAction,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "~/components/ui/card"

export interface StatsData {
  layanan_tersedia?: number
  sedang_diproses?: number
  perlu_perbaikan?: number
  selesai_terbit?: number
  pending?: number
  ditolak?: number
  total_pengajuan?: number
}

const props = defineProps<{
  stats?: StatsData
}>()
</script>

<template>
  <div class="*:data-[slot=card]:from-primary/5 *:data-[slot=card]:to-card dark:*:data-[slot=card]:bg-card grid grid-cols-1 gap-4 px-4 *:data-[slot=card]:bg-linear-to-t *:data-[slot=card]:shadow-xs lg:px-6 @xl/main:grid-cols-2 @5xl/main:grid-cols-4">
    <!-- Card 1: Total Jenis Layanan -->
    <Card class="@container/card">
      <CardHeader>
        <CardDescription>Layanan Tersedia</CardDescription>
        <CardTitle class="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
          {{ stats?.layanan_tersedia ?? 6 }} Layanan
        </CardTitle>
        <CardAction>
          <Badge variant="outline" class="text-primary">
            <IconChecklist class="size-3.5 mr-1" />
            Aktif
          </Badge>
        </CardAction>
      </CardHeader>
      <CardFooter class="flex-col items-start gap-1.5 text-sm">
        <div class="line-clamp-1 flex gap-2 font-medium">
          Siap Diajukan <IconChecklist class="size-4 text-primary" />
        </div>
        <div class="text-muted-foreground">
          Surat aktif, penelitian, legalisir, dll.
        </div>
      </CardFooter>
    </Card>

    <!-- Card 2: Pengajuan Menunggu / Diproses -->
    <Card class="@container/card">
      <CardHeader>
        <CardDescription>Sedang Diproses</CardDescription>
        <CardTitle class="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
          {{ stats?.sedang_diproses ?? 0 }} Pengajuan
        </CardTitle>
        <CardAction>
          <Badge variant="outline" class="text-amber-500 border-amber-200 bg-amber-50 dark:bg-amber-950/20">
            <IconClock class="size-3.5 mr-1" />
            Proses
          </Badge>
        </CardAction>
      </CardHeader>
      <CardFooter class="flex-col items-start gap-1.5 text-sm">
        <div class="line-clamp-1 flex gap-2 font-medium text-amber-600 dark:text-amber-400">
          Pemeriksaan Berkas <IconClock class="size-4" />
        </div>
        <div class="text-muted-foreground">
          Dalam peninjauan tata usaha
        </div>
      </CardFooter>
    </Card>

    <!-- Card 3: Perlu Perbaikan -->
    <Card class="@container/card">
      <CardHeader>
        <CardDescription>Perlu Perbaikan</CardDescription>
        <CardTitle class="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
          {{ stats?.perlu_perbaikan ?? 0 }} Berkas
        </CardTitle>
        <CardAction>
          <Badge variant="outline" class="text-destructive border-destructive/20 bg-destructive/5">
            <IconFileAlert class="size-3.5 mr-1" />
            Revisi
          </Badge>
        </CardAction>
      </CardHeader>
      <CardFooter class="flex-col items-start gap-1.5 text-sm">
        <div class="line-clamp-1 flex gap-2 font-medium text-destructive">
          Dikembalikan <IconFileAlert class="size-4" />
        </div>
        <div class="text-muted-foreground">
          Cek catatan perbaikan di pelacakan
        </div>
      </CardFooter>
    </Card>

    <!-- Card 4: Selesai / Disetujui -->
    <Card class="@container/card">
      <CardHeader>
        <CardDescription>Selesai Terbit</CardDescription>
        <CardTitle class="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
          {{ stats?.selesai_terbit ?? 0 }} Berkas
        </CardTitle>
        <CardAction>
          <Badge variant="outline" class="text-emerald-600 border-emerald-200 bg-emerald-50 dark:bg-emerald-950/20">
            <IconFileCheck class="size-3.5 mr-1" />
            Selesai
          </Badge>
        </CardAction>
      </CardHeader>
      <CardFooter class="flex-col items-start gap-1.5 text-sm">
        <div class="line-clamp-1 flex gap-2 font-medium text-emerald-600 dark:text-emerald-400">
          Dapat Diambil/Unduh <IconFileCheck class="size-4" />
        </div>
        <div class="text-muted-foreground">
          Surat resmi siap digunakan
        </div>
      </CardFooter>
    </Card>
  </div>
</template>