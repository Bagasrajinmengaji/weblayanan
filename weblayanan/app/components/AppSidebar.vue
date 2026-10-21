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

import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "~/components/ui/sidebar";

const { user, isAdmin } = useAuth();

const currentUser = computed(() => {
  if (user.value) {
    return {
      name: user.value.name,
      email: user.value.email,
      avatar: user.value.avatar || (user.value.role === "admin" ? "https://api.dicebear.com/7.x/bottts/svg?seed=admin" : "https://github.com/shadcn.png"),
      role: user.value.role,
    };
  }
  return {
    name: "Tamu / Belum Masuk",
    email: "Klik untuk masuk",
    avatar: "https://api.dicebear.com/7.x/bottts/svg?seed=guest",
    role: "guest",
  };
});

// Sidebar Data Dinamis (Memisahkan Tampilan Admin vs Mahasiswa)
const sidebarData = computed(() => {
  // TAMPILAN KHUSUS ADMIN BAAK (Tanpa menu pengajuan surat mahasiswa)
  if (isAdmin.value) {
    return {
      header: {
        title: "Panel Administrator",
        subtitle: "BAAK Kampus",
        url: "/admin",
        icon: IconShieldCheck,
      },
      navMain: [
        {
          title: "Dashboard Verifikasi",
          url: "/admin",
          icon: IconShieldCheck,
        },
        {
          title: "Katalog Jenis Layanan",
          url: "/layanan",
          icon: IconFileText,
        },
        {
          title: "Cek Status / Lacak",
          url: "/pengajuan/lacak",
          icon: IconSearch,
        },
      ],
      documents: [], // Kosong untuk admin
      navSecondary: [
        {
          title: "Pengaturan Sistem",
          url: "/pengaturan",
          icon: IconSettings,
        },
        {
          title: "Bantuan & SOP",
          url: "/bantuan",
          icon: IconHelp,
        },
      ],
    };
  }

  // TAMPILAN KHUSUS MAHASISWA & TAMU
  return {
    header: {
      title: "Portal Layanan",
      subtitle: "Kemahasiswaan",
      url: "/",
      icon: IconBuildingBank,
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
    navSecondary: [
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
  };
});
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
            <NuxtLink :to="sidebarData.header.url">
              <div
                class="flex aspect-square size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground"
              >
                <component :is="sidebarData.header.icon" class="size-5" />
              </div>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-bold">{{ sidebarData.header.title }}</span>
                <span class="truncate text-xs text-muted-foreground"
                  >{{ sidebarData.header.subtitle }}</span
                >
              </div>
            </NuxtLink>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <!-- Auto-imported components dari app/components/ -->
      <Navmain :items="sidebarData.navMain" />
      <!-- Dokumen & Permohonan Layanan HANYA untuk Mahasiswa (Disembunyikan dari Admin) -->
      <NavDocuments v-if="!isAdmin && sidebarData.documents.length > 0" :items="sidebarData.documents" />
      <NavSecondary :items="sidebarData.navSecondary" class="mt-auto" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser :user="currentUser" />
    </SidebarFooter>
  </Sidebar>
</template>
