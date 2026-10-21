<script setup lang="ts">
import {
  IconAlertTriangle,
  IconCreditCard,
  IconDotsVertical,
  IconLogin,
  IconLogout,
  IconNotification,
  IconShieldCheck,
  IconUser,
  IconUserCircle,
} from "@tabler/icons-vue"

import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from "~/components/ui/avatar"
import { Badge } from "~/components/ui/badge"
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

interface UserData {
  name: string
  email: string
  avatar: string
  role?: string
}

defineProps<{
  user: UserData
}>()

const router = useRouter()
const { isMobile } = useSidebar()
const { logout: authLogout, isAuthenticated, isAdmin } = useAuth()

// Dialog konfirmasi logout state
const isLogoutDialogOpen = ref(false)
const isLoggingOut = ref(false)

function openLogoutDialog() {
  isLogoutDialogOpen.value = true
}

async function confirmLogout() {
  isLoggingOut.value = true
  try {
    await authLogout()
    await new Promise((resolve) => setTimeout(resolve, 400))
    isLogoutDialogOpen.value = false

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
      <!-- STATE 1: BELUM LOGIN -->
      <SidebarMenuItem v-if="!isAuthenticated">
        <SidebarMenuButton
          as-child
          size="lg"
          class="border border-border/70 bg-card hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer rounded-lg"
        >
          <NuxtLink to="/login" class="flex items-center gap-2.5 w-full">
            <div class="size-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0">
              <IconLogin class="size-4" />
            </div>
            <div class="grid flex-1 text-left text-sm leading-tight min-w-0">
              <span class="truncate font-semibold text-foreground">Masuk / Login</span>
              <span class="text-muted-foreground truncate text-xs">SSO Google / Petugas</span>
            </div>
          </NuxtLink>
        </SidebarMenuButton>
      </SidebarMenuItem>

      <!-- STATE 2: SUDAH LOGIN -->
      <SidebarMenuItem v-else>
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <SidebarMenuButton
              size="lg"
              class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
            >
              <Avatar class="h-8 w-8 rounded-lg">
                <AvatarImage :src="user.avatar" :alt="user.name" />
                <AvatarFallback class="rounded-lg bg-primary/10 text-primary text-xs font-bold">
                  {{ user.name.slice(0, 2).toUpperCase() }}
                </AvatarFallback>
              </Avatar>
              <div class="grid flex-1 text-left text-sm leading-tight min-w-0">
                <div class="flex items-center gap-1.5">
                  <span class="truncate font-medium">{{ user.name }}</span>
                  <Badge v-if="isAdmin" variant="outline" class="text-[9px] px-1 py-0 h-4 border-primary/30 text-primary">
                    BAAK
                  </Badge>
                </div>
                <span class="text-muted-foreground truncate text-xs">
                  {{ user.email }}
                </span>
              </div>
              <IconDotsVertical class="ml-auto size-4 shrink-0" />
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
                  <AvatarFallback class="rounded-lg bg-primary/10 text-primary text-xs font-bold">
                    {{ user.name.slice(0, 2).toUpperCase() }}
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
              <DropdownMenuItem v-if="isAdmin" as-child class="cursor-pointer">
                <NuxtLink to="/admin" class="flex items-center w-full font-semibold text-primary">
                  <IconShieldCheck class="mr-2 h-4 w-4" />
                  <span>Panel Verifikasi BAAK</span>
                </NuxtLink>
              </DropdownMenuItem>
              <DropdownMenuItem as-child class="cursor-pointer">
                <NuxtLink to="/pengaturan" class="flex items-center w-full">
                  <IconUserCircle class="mr-2 h-4 w-4" />
                  <span>Profil & Pengaturan</span>
                </NuxtLink>
              </DropdownMenuItem>
              <DropdownMenuItem as-child class="cursor-pointer">
                <NuxtLink to="/pengajuan/riwayat" class="flex items-center w-full">
                  <IconCreditCard class="mr-2 h-4 w-4" />
                  <span>Riwayat Pengajuan Saya</span>
                </NuxtLink>
              </DropdownMenuItem>
              <DropdownMenuItem as-child class="cursor-pointer">
                <NuxtLink to="/pengajuan/lacak" class="flex items-center w-full">
                  <IconNotification class="mr-2 h-4 w-4" />
                  <span>Lacak Berkas Pengajuan</span>
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
            Apakah Anda yakin ingin keluar dari portal layanan akademik? Sesi aktif Anda pada perangkat ini akan diakhiri.
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