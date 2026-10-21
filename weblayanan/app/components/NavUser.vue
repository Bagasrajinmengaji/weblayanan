<script setup lang="ts">
import {
  IconAlertTriangle,
  IconCreditCard,
  IconDotsVertical,
  IconLogout,
  IconNotification,
  IconUserCircle,
} from "@tabler/icons-vue"

import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from "~/components/ui/avatar"
import { Button } from "~/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "~/components/ui/dialog"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "~/components/ui/dropdown-menu"
import {
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from "~/components/ui/sidebar"

interface User {
  name: string
  email: string
  avatar: string
}

defineProps<{
  user: User
}>()

const router = useRouter()
const { isMobile } = useSidebar()

// Dialog konfirmasi logout state
const isLogoutDialogOpen = ref(false)
const isLoggingOut = ref(false)

function openLogoutDialog() {
  isLogoutDialogOpen.value = true
}

async function confirmLogout() {
  isLoggingOut.value = true
  try {
    // 1. Bersihkan seluruh penyimpanan client
    if (import.meta.client) {
      localStorage.removeItem("auth_token")
      localStorage.removeItem("user_data")
      localStorage.removeItem("token")
      sessionStorage.clear()
    }

    // 2. Beri sedikit jeda animasi
    await new Promise((resolve) => setTimeout(resolve, 500))

    isLogoutDialogOpen.value = false

    // 3. Arahkan ke halaman utama beranda
    if (import.meta.client) {
      window.location.href = "/"
    } else {
      await router.push("/")
    }
  } finally {
    isLoggingOut.value = false
  }
}
</script>

<template>
  <div>
    <SidebarMenu>
      <SidebarMenuItem>
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <SidebarMenuButton
              size="lg"
              class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
            >
              <Avatar class="h-8 w-8 rounded-lg grayscale">
                <AvatarImage :src="user.avatar" :alt="user.name" />
                <AvatarFallback class="rounded-lg">
                  MHS
                </AvatarFallback>
              </Avatar>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-medium">{{ user.name }}</span>
                <span class="text-muted-foreground truncate text-xs">
                  {{ user.email }}
                </span>
              </div>
              <IconDotsVertical class="ml-auto size-4" />
            </SidebarMenuButton>
          </DropdownMenuTrigger>
          <DropdownMenuContent
            class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
            :side="isMobile ? 'bottom' : 'right'"
            :side-offset="4"
            align="end"
          >
            <DropdownMenuLabel class="p-0 font-normal">
              <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                <Avatar class="h-8 w-8 rounded-lg">
                  <AvatarImage :src="user.avatar" :alt="user.name" />
                  <AvatarFallback class="rounded-lg">
                    MHS
                  </AvatarFallback>
                </Avatar>
                <div class="grid flex-1 text-left text-sm leading-tight">
                  <span class="truncate font-medium">{{ user.name }}</span>
                  <span class="text-muted-foreground truncate text-xs">
                    {{ user.email }}
                  </span>
                </div>
              </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
              <DropdownMenuItem as-child class="cursor-pointer">
                <NuxtLink to="/pengaturan" class="flex items-center w-full">
                  <IconUserCircle class="mr-2 h-4 w-4" />
                  <span>Profil & Pengaturan</span>
                </NuxtLink>
              </DropdownMenuItem>
              <DropdownMenuItem as-child class="cursor-pointer">
                <NuxtLink to="/pengajuan/riwayat" class="flex items-center w-full">
                  <IconCreditCard class="mr-2 h-4 w-4" />
                  <span>Riwayat Pengajuan</span>
                </NuxtLink>
              </DropdownMenuItem>
              <DropdownMenuItem as-child class="cursor-pointer">
                <NuxtLink to="/pengajuan/lacak" class="flex items-center w-full">
                  <IconNotification class="mr-2 h-4 w-4" />
                  <span>Lacak Berkas</span>
                </NuxtLink>
              </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem
              class="text-destructive focus:bg-destructive/10 focus:text-destructive cursor-pointer"
              @select.prevent="openLogoutDialog"
            >
              <IconLogout class="mr-2 h-4 w-4" />
              <span>Keluar Sesi</span>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </SidebarMenuItem>
    </SidebarMenu>

    <!-- Dialog Konfirmasi Logout -->
    <Dialog :open="isLogoutDialogOpen" @update:open="isLogoutDialogOpen = $event">
      <DialogContent class="sm:max-w-md p-6 flex flex-col gap-4">
        <DialogHeader class="space-y-2">
          <div class="size-11 rounded-full bg-destructive/10 text-destructive flex items-center justify-center">
            <IconAlertTriangle class="size-6" />
          </div>
          <DialogTitle class="text-lg font-bold">
            Konfirmasi Keluar Sesi
          </DialogTitle>
          <DialogDescription class="text-sm text-muted-foreground">
            Apakah Anda yakin ingin keluar dari portal layanan mahasiswa? Sesi aktif Anda pada perangkat ini akan diakhiri.
          </DialogDescription>
        </DialogHeader>

        <DialogFooter class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
          <Button
            type="button"
            variant="outline"
            @click="isLogoutDialogOpen = false"
            :disabled="isLoggingOut"
          >
            Batal
          </Button>

          <Button
            type="button"
            variant="destructive"
            class="gap-1.5"
            :disabled="isLoggingOut"
            @click="confirmLogout"
          >
            <span v-if="isLoggingOut" class="size-4 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></span>
            <IconLogout v-else class="size-4" />
            <span>{{ isLoggingOut ? 'Mengakhiri Sesi...' : 'Ya, Keluar' }}</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>