<script setup lang="ts">
import {
  IconBell,
  IconCheck,
  IconClock,
  IconFileAlert,
  IconHelpCircle,
  IconInfoCircle,
} from "@tabler/icons-vue"

import { Button } from "~/components/ui/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "~/components/ui/dropdown-menu"
import { Separator } from "~/components/ui/separator"
import { SidebarTrigger } from "~/components/ui/sidebar"

const router = useRouter()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase || "http://localhost:8000/api"

// Ambil riwayat terbaru untuk notifikasi dinamis
const { data: riwayatData } = await useFetch<any>(`${apiBase}/pengajuan/riwayat`, {
  lazy: true,
  default: () => ({ data: [] }),
})

const notifications = computed(() => {
  const items = riwayatData.value?.data || []
  if (items.length > 0) {
    return items.slice(0, 3).map((item: any) => ({
      id: item.id,
      nomor: item.nomor_pelacakan,
      title: `${item.layanan?.nama_layanan || 'Pengajuan Surat'}: ${item.status}`,
      desc: item.catatan_revisi
        ? `Catatan: ${item.catatan_revisi.slice(0, 60)}...`
        : `Diajukan oleh ${item.nama_mahasiswa} (${item.nomor_pelacakan})`,
      status: item.status,
      time: new Date(item.created_at).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
      }),
    }))
  }

  return []
})

function navigateToLacak(nomor: string) {
  router.push(`/pengajuan/lacak?nomor=${encodeURIComponent(nomor)}`)
}
</script>

<template>
  <header class="flex h-(--header-height) shrink-0 items-center gap-2 border-b transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-(--header-height)">
    <div class="flex w-full items-center gap-1 px-4 lg:gap-2 lg:px-6">
      <SidebarTrigger class="-ml-1" />
      <Separator
        orientation="vertical"
        class="mx-2 data-[orientation=vertical]:h-4"
      />
      <h1 class="text-base font-medium">
        Portal Layanan Mahasiswa
      </h1>
      <div class="ml-auto flex items-center gap-2">
        <!-- Notifikasi Dropdown -->
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="relative size-8">
              <IconBell class="size-4" />
              <span v-if="notifications.length > 0" class="absolute top-1.5 right-1.5 size-2 rounded-full bg-primary"></span>
              <span class="sr-only">Notifikasi</span>
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end" class="w-84 p-2">
            <DropdownMenuLabel class="flex items-center justify-between px-2 py-1.5">
              <span class="font-semibold text-sm">Pemberitahuan</span>
              <span class="text-xs text-muted-foreground">Sistem Akademik</span>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <template v-if="notifications.length > 0">
              <DropdownMenuItem
                v-for="notif in notifications"
                :key="notif.id"
                class="p-2.5 cursor-pointer rounded-md hover:bg-accent/60 transition-colors"
                @click="navigateToLacak(notif.nomor)"
              >
                <div class="flex items-start gap-2.5 w-full">
                  <div
                    class="size-7 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                    :class="{
                      'bg-emerald-500/10 text-emerald-600': notif.status === 'Disetujui',
                      'bg-amber-500/10 text-amber-600': notif.status === 'Diproses',
                      'bg-rose-500/10 text-rose-600': notif.status === 'Perlu Perbaikan' || notif.status === 'Ditolak',
                      'bg-blue-500/10 text-blue-600': notif.status === 'Pending',
                    }"
                  >
                    <IconCheck v-if="notif.status === 'Disetujui'" class="size-3.5" />
                    <IconClock v-else-if="notif.status === 'Diproses'" class="size-3.5" />
                    <IconFileAlert v-else-if="notif.status === 'Perlu Perbaikan' || notif.status === 'Ditolak'" class="size-3.5" />
                    <IconInfoCircle v-else class="size-3.5" />
                  </div>
                  <div class="flex-1 space-y-0.5 text-xs min-w-0">
                    <div class="flex items-center justify-between gap-1">
                      <p class="font-medium text-foreground truncate">{{ notif.title }}</p>
                      <span class="text-[10px] text-muted-foreground shrink-0">{{ notif.time }}</span>
                    </div>
                    <p class="text-muted-foreground text-[11px] line-clamp-2">{{ notif.desc }}</p>
                    <p class="text-[10px] text-primary font-mono font-medium pt-0.5">Lacak: {{ notif.nomor }} &rarr;</p>
                  </div>
                </div>
              </DropdownMenuItem>
            </template>
            <div v-else class="py-6 px-4 text-center text-xs text-muted-foreground space-y-1">
              <IconBell class="size-6 mx-auto text-muted-foreground/50 mb-1" />
              <p class="font-medium text-foreground">Tidak Ada Notifikasi Baru</p>
              <p class="text-[11px]">Pengajuan baru yang Anda buat akan muncul di sini.</p>
            </div>
            <DropdownMenuSeparator />
            <DropdownMenuItem as-child class="p-2 text-center text-xs text-primary font-medium cursor-pointer justify-center">
              <NuxtLink to="/pengajuan/riwayat" class="w-full text-center">
                Lihat Semua Riwayat Pengajuan
              </NuxtLink>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>

        <Button variant="outline" size="sm" class="hidden sm:flex" as-child>
          <NuxtLink to="/pengajuan/lacak">
            <IconHelpCircle class="mr-1.5 size-4" />
            Lacak Pengajuan
          </NuxtLink>
        </Button>
      </div>
    </div>
  </header>
</template>