<script setup lang="ts">
import type { Component } from "vue"

import {
  IconDots,
  IconEye,
  IconFileText,
  IconFolder,
  IconSend,
} from "@tabler/icons-vue"

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "~/components/ui/dropdown-menu"
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuAction,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from "~/components/ui/sidebar"

interface Document {
  id?: number | string
  name: string
  url: string
  icon?: Component
}

defineProps<{
  items: Document[]
}>()

const { isMobile } = useSidebar()
</script>

<template>
  <SidebarGroup class="group-data-[collapsible=icon]:hidden">
    <SidebarGroupLabel>Dokumen & Layanan</SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in items" :key="item.name">
        <SidebarMenuButton as-child>
          <NuxtLink :to="item.url">
            <component :is="item.icon" v-if="item.icon" />
            <span>{{ item.name }}</span>
          </NuxtLink>
        </SidebarMenuButton>
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <SidebarMenuAction
              show-on-hover
              class="data-[state=open]:bg-accent rounded-sm"
            >
              <IconDots />
              <span class="sr-only">Menu Cepat</span>
            </SidebarMenuAction>
          </DropdownMenuTrigger>
          <DropdownMenuContent
            class="w-44 rounded-lg"
            :side="isMobile ? 'bottom' : 'right'"
            :align="isMobile ? 'end' : 'start'"
          >
            <DropdownMenuItem as-child>
              <NuxtLink :to="item.url" class="cursor-pointer flex items-center">
                <IconSend class="mr-2 h-4 w-4 text-primary" />
                <span>Ajukan Surat</span>
              </NuxtLink>
            </DropdownMenuItem>
            <DropdownMenuItem v-if="item.id" as-child>
              <NuxtLink :to="`/layanan/${item.id}`" class="cursor-pointer flex items-center">
                <IconEye class="mr-2 h-4 w-4 text-muted-foreground" />
                <span>Lihat Detail Syarat</span>
              </NuxtLink>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </SidebarMenuItem>

      <!-- Menu Lainnya & Katalog Seluruh Layanan -->
      <SidebarMenuItem>
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <SidebarMenuButton class="text-sidebar-foreground/80 hover:text-sidebar-foreground cursor-pointer">
              <IconDots class="text-sidebar-foreground/70" />
              <span>Lainnya</span>
            </SidebarMenuButton>
          </DropdownMenuTrigger>
          <DropdownMenuContent
            class="w-60 rounded-lg p-1.5"
            :side="isMobile ? 'bottom' : 'right'"
            :align="isMobile ? 'end' : 'start'"
          >
            <DropdownMenuItem as-child>
              <NuxtLink to="/pengajuan?layanan_id=4" class="cursor-pointer flex items-center">
                <IconFileText class="mr-2 h-4 w-4 text-primary" />
                <span>Surat Bebas Pustaka</span>
              </NuxtLink>
            </DropdownMenuItem>
            <DropdownMenuItem as-child>
              <NuxtLink to="/pengajuan?layanan_id=5" class="cursor-pointer flex items-center">
                <IconFileText class="mr-2 h-4 w-4 text-primary" />
                <span>Pengajuan Cuti Akademik</span>
              </NuxtLink>
            </DropdownMenuItem>
            <DropdownMenuItem as-child>
              <NuxtLink to="/pengajuan?layanan_id=6" class="cursor-pointer flex items-center">
                <IconFileText class="mr-2 h-4 w-4 text-primary" />
                <span>Rekomendasi Beasiswa</span>
              </NuxtLink>
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem as-child>
              <NuxtLink to="/layanan" class="cursor-pointer flex items-center font-medium text-primary">
                <IconFolder class="mr-2 h-4 w-4" />
                <span>Lihat Seluruh Layanan (Katalog)</span>
              </NuxtLink>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>