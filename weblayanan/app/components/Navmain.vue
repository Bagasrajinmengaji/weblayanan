<script setup lang="ts">
import type { Component } from "vue";

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
