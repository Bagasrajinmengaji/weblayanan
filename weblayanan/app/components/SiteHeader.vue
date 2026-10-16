<script setup lang="ts">
import { IconBell, IconHelpCircle } from "@tabler/icons-vue"

import { Button } from "~/components/ui/button"
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
        <Button variant="ghost" size="icon" class="size-8">
          <IconBell class="size-4" />
          <span class="sr-only">Notifikasi</span>
        </Button>
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