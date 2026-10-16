<script setup lang="ts">
import type { Component } from "vue"

import {
  IconDots,
  IconFolder,
  IconShare3,
  IconTrash,
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
              <span class="sr-only">Lainnya</span>
            </SidebarMenuAction>
          </DropdownMenuTrigger>
          <DropdownMenuContent
            class="w-28 rounded-lg"
            :side="isMobile ? 'bottom' : 'right'"
            :align="isMobile ? 'end' : 'start'"
          >
            <DropdownMenuItem>
              <IconFolder class="mr-2 h-4 w-4" />
              <span>Buka</span>
            </DropdownMenuItem>
            <DropdownMenuItem>
              <IconShare3 class="mr-2 h-4 w-4" />
              <span>Bagikan</span>
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem variant="destructive">
              <IconTrash class="mr-2 h-4 w-4" />
              <span>Hapus</span>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </SidebarMenuItem>
      <SidebarMenuItem>
        <SidebarMenuButton class="text-sidebar-foreground/70">
          <IconDots class="text-sidebar-foreground/70" />
          <span>Lainnya</span>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>