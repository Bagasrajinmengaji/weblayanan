<script setup lang="ts">
import {
  IconAlertCircle,
  IconArrowLeft,
  IconBuildingBank,
  IconCheck,
  IconEye,
  IconEyeOff,
  IconLock,
  IconMail,
  IconShieldCheck,
  IconUser,
} from "@tabler/icons-vue"

import { Button } from "~/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "~/components/ui/card"
import { Input } from "~/components/ui/input"
import { Label } from "~/components/ui/label"

useSeoMeta({
  title: "Masuk ke Sistem - Portal Layanan Mahasiswa",
  description: "Masuk ke portal layanan akademik mahasiswa melalui SSO Google atau login petugas BAAK.",
})

const route = useRoute()
const router = useRouter()
const { loginWithPassword, loginWithGoogle, isAuthenticated, isAdmin } = useAuth()

// Tab aktif: 'mahasiswa' (Google SSO) atau 'admin' (Password)
const activeTab = ref<"mahasiswa" | "admin">(
  route.query.role === "admin" || route.query.redirect?.toString().includes("admin")
    ? "admin"
    : "mahasiswa"
)

// Form Admin state
const adminForm = reactive({
  email: "admin@student.ac.id",
  password: "password123",
})
const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref<string | null>(null)
const successMessage = ref<string | null>(null)

// Handle Login Admin dengan Password
async function handleAdminLogin() {
  if (isLoading.value) return
  isLoading.value = true
  errorMessage.value = null
  successMessage.value = null

  try {
    const res = await loginWithPassword(adminForm.email, adminForm.password)
    successMessage.value = `Selamat datang, ${res.user.name}!`
    
    setTimeout(() => {
      const redirect = (route.query.redirect as string) || (res.user.role === "admin" ? "/admin" : "/")
      router.push(redirect)
    }, 600)
  } catch (err: any) {
    errorMessage.value = err.data?.message || err.message || "Gagal masuk. Periksa email dan password Anda."
  } finally {
    isLoading.value = false
  }
}

// Handle Login Mahasiswa via SSO Google
async function handleGoogleLogin(emailParam?: string, nameParam?: string) {
  if (isLoading.value) return
  isLoading.value = true
  errorMessage.value = null
  successMessage.value = null

  try {
    // Profil default atau akun kampus pilihan
    const targetEmail = emailParam || "1202407009@students.itspku.ac.id"
    const targetName = nameParam || "Bagas Pramono"

    const res = await loginWithGoogle({
      email: targetEmail,
      name: targetName,
      google_id: "google_" + Date.now(),
      avatar: "https://lh3.googleusercontent.com/a/ACg8ocISampleAvatar",
    })

    successMessage.value = `Berhasil masuk via Google sebagai ${res.user.name}!`
    
    setTimeout(() => {
      const redirect = (route.query.redirect as string) || "/"
      router.push(redirect)
    }, 600)
  } catch (err: any) {
    errorMessage.value = err.data?.message || err.message || "Gagal masuk via SSO Google."
  } finally {
    isLoading.value = false
  }
}

// Redirect jika sudah login
onMounted(() => {
  if (isAuthenticated.value) {
    router.push(isAdmin.value ? "/admin" : "/")
  }
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center p-4 bg-muted/20">
    <div class="w-full max-w-md flex flex-col gap-5">
      <!-- Tombol Kembali ke Beranda -->
      <div>
        <Button variant="ghost" size="sm" as-child class="gap-1.5 -ml-2 text-muted-foreground hover:text-foreground">
          <NuxtLink to="/">
            <IconArrowLeft class="size-4" />
            Kembali ke Beranda
          </NuxtLink>
        </Button>
      </div>

      <!-- Card Login Utama -->
      <Card class="border shadow-lg overflow-hidden bg-card">
        <div class="h-1.5 w-full bg-primary"></div>
        <CardHeader class="pb-4 text-center">
          <div class="size-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-2">
            <IconBuildingBank class="size-6" />
          </div>
          <CardTitle class="text-xl font-bold">Portal Layanan Akademik</CardTitle>
          <CardDescription class="text-xs">
            Masuk untuk mengajukan surat, memantau berkas, atau verifikasi permohonan.
          </CardDescription>

          <!-- Toggle Tab Mahasiswa / Admin -->
          <div class="grid grid-cols-2 gap-1 p-1 bg-muted rounded-lg mt-4 text-xs font-medium">
            <button
              type="button"
              class="py-2 rounded-md transition-all flex items-center justify-center gap-1.5"
              :class="activeTab === 'mahasiswa' ? 'bg-background text-foreground shadow-xs font-semibold' : 'text-muted-foreground hover:text-foreground'"
              @click="activeTab = 'mahasiswa'; errorMessage = null"
            >
              <IconUser class="size-3.5" />
              Mahasiswa (SSO)
            </button>
            <button
              type="button"
              class="py-2 rounded-md transition-all flex items-center justify-center gap-1.5"
              :class="activeTab === 'admin' ? 'bg-background text-foreground shadow-xs font-semibold' : 'text-muted-foreground hover:text-foreground'"
              @click="activeTab = 'admin'; errorMessage = null"
            >
              <IconShieldCheck class="size-3.5" />
              Petugas BAAK
            </button>
          </div>
        </CardHeader>

        <CardContent class="space-y-4">
          <!-- Alert Pesan Sukses / Error -->
          <div v-if="successMessage" class="rounded-lg bg-emerald-500/10 border border-emerald-500/30 p-3 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2">
            <IconCheck class="size-4 shrink-0" />
            <span>{{ successMessage }}</span>
          </div>

          <div v-if="errorMessage" class="rounded-lg bg-destructive/10 border border-destructive/30 p-3 text-destructive text-xs flex items-center gap-2">
            <IconAlertCircle class="size-4 shrink-0" />
            <span>{{ errorMessage }}</span>
          </div>

          <!-- ================= TAB MAHASISWA (GOOGLE SSO) ================= -->
          <div v-if="activeTab === 'mahasiswa'" class="space-y-4 pt-1">
            <div class="rounded-lg border bg-muted/30 p-4 text-center space-y-3">
              <p class="text-xs text-muted-foreground leading-relaxed">
                Mahasiswa aktif dapat masuk secara instan menggunakan <strong>Akun Google Kampus</strong> tanpa perlu registrasi kata sandi manual.
              </p>

              <!-- Tombol Resmi Google SSO -->
              <Button
                type="button"
                variant="outline"
                class="w-full h-11 border-border font-medium text-xs gap-3 shadow-xs hover:bg-muted/80 bg-background"
                :disabled="isLoading"
                @click="() => handleGoogleLogin()"
              >
                <!-- Official Google SVG Logo -->
                <svg class="size-4 shrink-0" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                <span>Masuk dengan Akun Google Mahasiswa</span>
              </Button>
            </div>

            <!-- Opsi Cepat / Pilihan Akun Mahasiswa Demo -->
            <div class="space-y-1.5 pt-1">
              <span class="text-[11px] font-semibold text-muted-foreground block text-center">
                Atau pilih simulasi akun mahasiswa:
              </span>
              <div class="grid grid-cols-1 gap-1.5">
                <button
                  type="button"
                  class="rounded-md border p-2 text-left text-xs hover:bg-muted/50 transition-colors flex items-center justify-between"
                  @click="handleGoogleLogin('1202407009@students.itspku.ac.id', 'Bagas Pramono')"
                >
                  <div>
                    <p class="font-medium text-foreground">Bagas Pramono</p>
                    <p class="text-[10px] font-mono text-muted-foreground">1202407009@students.itspku.ac.id</p>
                  </div>
                  <span class="text-[10px] text-primary font-medium">Pilih &rarr;</span>
                </button>
              </div>
            </div>

            <div class="text-center pt-2 border-t">
              <p class="text-xs text-muted-foreground">
                Belum punya akun Google kampus?
                <NuxtLink to="/register" class="text-primary font-medium hover:underline ml-1">
                  Daftar Manual
                </NuxtLink>
              </p>
            </div>
          </div>

          <!-- ================= TAB ADMIN BAAK (PASSWORD) ================= -->
          <form v-else-if="activeTab === 'admin'" @submit.prevent="handleAdminLogin" class="space-y-3.5 pt-1">
            <div class="space-y-1.5">
              <Label for="admin-email" class="text-xs">Email / Username Petugas</Label>
              <div class="relative">
                <IconMail class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                <Input
                  id="admin-email"
                  v-model="adminForm.email"
                  type="text"
                  required
                  placeholder="admin@student.ac.id"
                  class="pl-9 text-xs h-9"
                />
              </div>
            </div>

            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <Label for="admin-password" class="text-xs">Kata Sandi</Label>
              </div>
              <div class="relative">
                <IconLock class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                <Input
                  id="admin-password"
                  v-model="adminForm.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  placeholder="••••••••"
                  class="pl-9 pr-9 text-xs h-9"
                />
                <button
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                  @click="showPassword = !showPassword"
                >
                  <IconEyeOff v-if="showPassword" class="size-4" />
                  <IconEye v-else class="size-4" />
                </button>
              </div>
            </div>

            <!-- Petunjuk Akun BAAK Bawaan -->
            <div class="rounded-md border bg-muted/40 p-2.5 text-[11px] text-muted-foreground space-y-0.5">
              <span class="font-semibold text-foreground">Akun BAAK Default:</span>
              <p>Email: <code class="font-mono text-primary font-semibold">admin@student.ac.id</code></p>
              <p>Password: <code class="font-mono text-primary font-semibold">password123</code></p>
            </div>

            <Button
              type="submit"
              class="w-full text-xs h-9 font-semibold gap-1.5 bg-primary hover:bg-primary/90 mt-2"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="size-3.5 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></span>
              <IconShieldCheck v-else class="size-4" />
              <span>{{ isLoading ? 'Memverifikasi...' : 'Masuk sebagai Petugas BAAK' }}</span>
            </Button>
          </form>
        </CardContent>

        <CardFooter class="pt-0 pb-4 text-center justify-center">
          <p class="text-[11px] text-muted-foreground">
            Sistem Layanan Administrasi Terpadu Mahasiswa &copy; {{ new Date().getFullYear() }}
          </p>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
