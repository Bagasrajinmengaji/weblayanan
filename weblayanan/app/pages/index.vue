<script setup lang="ts">
import {
  IconArrowRight,
  IconChecklist,
  IconFileText,
  IconHelp,
  IconPlus,
  IconRefresh,
  IconSearch,
} from "@tabler/icons-vue";

import SectionCards from "~/components/SectionCards.vue";
import { Button } from "~/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "~/components/ui/card";

useSeoMeta({
  title: "Dashboard Layanan Mahasiswa - Portal Akademik",
  description:
    "Portal layanan administrasi persuratan dan pengajuan berkas akademik mahasiswa secara online.",
});

const config = useRuntimeConfig();
const apiBase = config.public.apiBase || "http://localhost:8000/api";

// Ambil data statistik dari Laravel API secara lazy
const { data: statsResponse, refresh: refreshStats } = useLazyFetch<any>(
  `${apiBase}/pengajuan/stats`,
  {
    default: () => ({ data: {} }),
  },
);

const statsData = computed(() => {
  return statsResponse.value?.data || {};
});

const isRefreshing = ref(false);

async function refreshAll() {
  if (isRefreshing.value) return;
  isRefreshing.value = true;
  try {
    await refreshStats();
  } catch (err) {
    console.error("Gagal memuat ulang data statistik:", err);
  } finally {
    setTimeout(() => {
      isRefreshing.value = false;
    }, 600);
  }
}
</script>

<template>
  <div class="flex flex-col gap-6 min-w-0 max-w-full overflow-x-hidden">
    <!-- Header Bagian Atas -->
    <div
      class="flex flex-col justify-between gap-4 px-4 sm:flex-row sm:items-center lg:px-6"
    >
      <div>
        <h1
          class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
        >
          Portal Layanan Mahasiswa
        </h1>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <Button
          variant="outline"
          size="sm"
          @click="refreshAll"
          :disabled="isRefreshing"
        >
          <IconRefresh
            class="size-4 mr-1.5"
            :class="{ 'animate-spin': isRefreshing }"
          />
          {{ isRefreshing ? "Memuat..." : "Muat Ulang" }}
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

    <!-- Panduan Alur (Clean Look) -->
    <div class="px-4 lg:px-6">
      <!-- Card Panduan Alur Pengajuan -->
      <Card class="w-full border shadow-xs">
        <CardHeader>
          <div class="flex items-center justify-between">
            <div>
              <CardTitle class="text-lg font-semibold">Alur Layanan Persuratan Mandiri</CardTitle>
            </div>
            <Button variant="ghost" size="sm" as-child class="hidden sm:flex text-primary hover:text-primary">
              <NuxtLink to="/layanan" class="gap-1.5">
                Katalog Layanan
                <IconArrowRight class="size-4" />
              </NuxtLink>
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-lg border bg-muted/40 p-4 flex flex-col justify-between gap-3">
              <div class="size-8 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-sm">
                1
              </div>
              <div>
                <h4 class="font-semibold text-sm text-foreground">Pilih Layanan</h4>
                <p class="text-xs text-muted-foreground mt-1 leading-relaxed">
                  Tentukan jenis surat atau permohonan yang dibutuhkan dari katalog akademik.
                </p>
              </div>
            </div>

            <div class="rounded-lg border bg-muted/40 p-4 flex flex-col justify-between gap-3">
              <div class="size-8 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-sm">
                2
              </div>
              <div>
                <h4 class="font-semibold text-sm text-foreground">Isi & Kirim Berkas</h4>
                <p class="text-xs text-muted-foreground mt-1 leading-relaxed">
                  Lengkapi identitas mahasiswa dan lampirkan scan berkas persyaratan online.
                </p>
              </div>
            </div>

            <div class="rounded-lg border bg-muted/40 p-4 flex flex-col justify-between gap-3">
              <div class="size-8 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-sm">
                3
              </div>
              <div>
                <h4 class="font-semibold text-sm text-foreground">Lacak & Ambil</h4>
                <p class="text-xs text-muted-foreground mt-1 leading-relaxed">
                  Gunakan nomor pelacakan unik untuk memantau proses verifikasi secara real-time.
                </p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
