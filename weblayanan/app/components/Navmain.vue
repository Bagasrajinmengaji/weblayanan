<script setup lang="ts">
import type { Component } from "vue";
import { IconCirclePlusFilled } from "@tabler/icons-vue";

import {
  SidebarGroup,
  SidebarGroupContent,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "~/components/ui/sidebar";

interface NavItem {
  title: string;
  url: string;
  icon?: Component;
}

defineProps<{
  items: NavItem[];
}>();

const route = useRoute();
</script>

<template>
  <SidebarGroup>
    <SidebarGroupContent class="flex flex-col gap-2">
      <!-- Tombol Ajukan Surat Cepat -->
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton
            as-child
            tooltip="Ajukan Surat Baru"
            class="bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground active:bg-primary/90 active:text-primary-foreground duration-200 ease-linear shadow-xs"
          >
            <NuxtLink to="/pengajuan">
              <IconCirclePlusFilled class="size-4 shrink-0" />
              <span class="font-medium">Ajukan Surat Baru</span>
            </NuxtLink>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>

      <!-- Daftar Menu Utama -->
      <SidebarMenu>
        <SidebarMenuItem v-for="item in items" :key="item.title">
          <SidebarMenuButton
            as-child
            :tooltip="item.title"
            :is-active="route.path === item.url"
          >
            <NuxtLink :to="item.url">
              <component :is="item.icon" v-if="item.icon" class="size-4 shrink-0" />
              <span>{{ item.title }}</span>
            </NuxtLink>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarGroupContent>
  </SidebarGroup>
</template>
