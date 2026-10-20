<script setup lang="ts">
import {
  IconBell,
  IconCheck,
  IconClock,
  IconFileAlert,
  IconHelpCircle,
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
              <span class="absolute top-1.5 right-1.5 size-2 rounded-full bg-primary"></span>
              <span class="sr-only">Notifikasi</span>
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end" class="w-80 p-2">
            <DropdownMenuLabel class="flex items-center justify-between px-2 py-1.5">
              <span class="font-semibold text-sm">Pemberitahuan</span>
              <span class="text-xs text-muted-foreground">Sistem Akademik</span>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem as-child class="p-2 cursor-pointer">
              <NuxtLink to="/pengajuan/riwayat" class="flex items-start gap-2.5">
                <div class="size-7 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                  <IconCheck class="size-3.5" />
                </div>
                <div class="flex-1 space-y-0.5 text-xs">
                  <p class="font-medium text-foreground">Surat Aktif Kuliah Disetujui</p>
                  <p class="text-muted-foreground text-[11px]">Berkas Anda telah ditandatangani dan siap diambil/diunduh.</p>
                </div>
              </NuxtLink>
            </DropdownMenuItem>
            <DropdownMenuItem as-child class="p-2 cursor-pointer">
              <NuxtLink to="/pengajuan/riwayat" class="flex items-start gap-2.5">
                <div class="size-7 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                  <IconClock class="size-3.5" />
                </div>
                <div class="flex-1 space-y-0.5 text-xs">
                  <p class="font-medium text-foreground">Pengantar Penelitian Sedang Diproses</p>
                  <p class="text-muted-foreground text-[11px]">Sedang dalam verifikasi oleh Bagian Tata Usaha Fakultas.</p>
                </div>
              </NuxtLink>
            </DropdownMenuItem>
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