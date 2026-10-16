<script setup lang="ts">
import { SidebarProvider, SidebarInset } from "~/components/ui/sidebar"
import AppSidebar from "~/components/AppSidebar.vue"
import SiteHeader from "~/components/SiteHeader.vue"
</script>

<template>
  <SidebarProvider>
    <AppSidebar />
    <SidebarInset>
      <SiteHeader />
      <div class="flex flex-1 flex-col gap-4 py-4 md:gap-6 md:py-6">
        <slot />
      </div>
    </SidebarInset>
  </SidebarProvider>
</template>

