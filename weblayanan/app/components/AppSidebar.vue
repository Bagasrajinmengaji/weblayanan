<script setup lang="ts">
import {
  IconBuildingBank,
  IconDashboard,
  IconFileText,
  IconHelp,
  IconHistory,
  IconSearch,
  IconSettings,
  IconShieldCheck,
} from "@tabler/icons-vue";

// Menggunakan auto-import Nuxt 3 untuk komponen lokal
// dan alias path ~/components/ui/sidebar untuk Shadcn
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "~/components/ui/sidebar";

// Data Menu Khusus Portal Layanan Mahasiswa
const data = {
  user: {
    name: "Bagas Pramono",
    email: "mahasiswa@umpku.ac.id",
    avatar: "https://github.com/shadcn.png",
  },
  navMain: [
    {
      title: "Dashboard Layanan",
      url: "/",
      icon: IconDashboard,
    },
    {
      title: "Katalog Semua Layanan",
      url: "/layanan",
      icon: IconFileText,
    },
    {
      title: "Cek Status / Lacak",
      url: "/pengajuan/lacak",
      icon: IconSearch,
    },
    {
      title: "Riwayat Pengajuan",
      url: "/pengajuan/riwayat",
      icon: IconHistory,
    },
  ],
  navSecondary: [
    {
      title: "Panel Admin BAAK",
      url: "/admin",
      icon: IconShieldCheck,
    },
    {
      title: "Pengaturan",
      url: "/pengaturan",
      icon: IconSettings,
    },
    {
      title: "Bantuan & FAQ",
      url: "/bantuan",
      icon: IconHelp,
    },
  ],
  documents: [
    {
      id: 1,
      name: "Surat Aktif Kuliah",
      url: "/pengajuan?layanan_id=1",
      icon: IconFileText,
    },
    {
      id: 2,
      name: "Pengantar Penelitian",
      url: "/pengajuan?layanan_id=2",
      icon: IconFileText,
    },
    {
      id: 3,
      name: "Legalisir Ijazah",
      url: "/pengajuan?layanan_id=3",
      icon: IconFileText,
    },
  ],
};
</script>

<template>
  <Sidebar collapsible="offcanvas">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton
            as-child
            class="data-[slot=sidebar-menu-button]:p-1.5!"
          >
            <NuxtLink to="/">
              <div
                class="flex aspect-square size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground"
              >
                <IconBuildingBank class="size-5" />
              </div>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-bold">Portal Layanan</span>
                <span class="truncate text-xs text-muted-foreground"
                  >Kemahasiswaan</span
                >
              </div>
            </NuxtLink>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <!-- Auto-imported components dari app/components/ -->
      <Navmain :items="data.navMain" />
      <NavDocuments :items="data.documents" />
      <NavSecondary :items="data.navSecondary" class="mt-auto" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser :user="data.user" />
    </SidebarFooter>
  </Sidebar>
</template>
