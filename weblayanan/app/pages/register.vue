<script setup lang="ts">
import {
  IconAlertCircle,
  IconArrowLeft,
  IconBuildingBank,
  IconCheck,
  IconEye,
  IconEyeOff,
  IconId,
  IconLock,
  IconMail,
  IconUser,
  IconUserPlus,
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
  title: "Registrasi Akun Mahasiswa - Portal Layanan",
  description: "Daftar akun mahasiswa untuk mengajukan surat dan memantau status persuratan akademik online.",
})

const router = useRouter()
const { register, loginWithGoogle } = useAuth()

const form = reactive({
  name: "",
  nim: "",
  email: "",
  password: "",
  password_confirmation: "",
})

const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref<string | null>(null)
const successMessage = ref<string | null>(null)

async function handleRegister() {
  if (form.password !== form.password_confirmation) {
    errorMessage.value = "Konfirmasi kata sandi tidak cocok."
    return
  }

  if (isLoading.value) return
  isLoading.value = true
  errorMessage.value = null
  successMessage.value = null

  try {
    const res = await register({
      name: form.name,
      nim: form.nim,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
    })

    successMessage.value = "Pendaftaran berhasil! Mengalihkan ke beranda..."
    setTimeout(() => {
      router.push("/")
    }, 800)
  } catch (err: any) {
    errorMessage.value = err.data?.message || err.message || "Gagal melakukan pendaftaran. Periksa kembali isian formulir."
  } finally {
    isLoading.value = false
  }
}

async function handleGoogleSSO() {
  if (isLoading.value) return
  isLoading.value = true
  try {
    await loginWithGoogle({
      email: "mahasiswa.baru@students.itspku.ac.id",
      name: "Mahasiswa Baru",
      google_id: "google_reg_" + Date.now(),
    })
    router.push("/")
  } catch (err: any) {
    errorMessage.value = err.data?.message || "Gagal mendaftar via SSO Google."
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center p-4 bg-muted/20">
    <div class="w-full max-w-md flex flex-col gap-4">
      <div>
        <Button variant="ghost" size="sm" as-child class="gap-1.5 -ml-2 text-muted-foreground hover:text-foreground">
          <NuxtLink to="/login">
            <IconArrowLeft class="size-4" />
            Kembali ke Login
          </NuxtLink>
        </Button>
      </div>

      <Card class="border shadow-lg overflow-hidden bg-card">
        <div class="h-1.5 w-full bg-primary"></div>
        <CardHeader class="pb-3 text-center">
          <div class="size-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-1.5">
            <IconBuildingBank class="size-5" />
          </div>
          <CardTitle class="text-xl font-bold">Daftar Akun Mahasiswa</CardTitle>
          <CardDescription class="text-xs">
            Buat akun untuk mengajukan surat akademik dan mengakses riwayat berkas.
          </CardDescription>
        </CardHeader>

        <CardContent class="space-y-3.5">
          <!-- Tombol Opsi Cepat SSO Google -->
          <Button
            type="button"
            variant="outline"
            class="w-full h-10 border-border font-medium text-xs gap-2.5 shadow-xs bg-background hover:bg-muted/80"
            :disabled="isLoading"
            @click="handleGoogleSSO"
          >
            <svg class="size-4 shrink-0" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            <span>Daftar Instan via SSO Google</span>
          </Button>

          <div class="relative flex items-center justify-center">
            <span class="absolute inset-x-0 h-px bg-border"></span>
            <span class="relative bg-card px-2 text-[10px] uppercase text-muted-foreground font-semibold">
              atau isi data manual
            </span>
          </div>

          <div v-if="successMessage" class="rounded-lg bg-emerald-500/10 border border-emerald-500/30 p-2.5 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2">
            <IconCheck class="size-4 shrink-0" />
            <span>{{ successMessage }}</span>
          </div>

          <div v-if="errorMessage" class="rounded-lg bg-destructive/10 border border-destructive/30 p-2.5 text-destructive text-xs flex items-center gap-2">
            <IconAlertCircle class="size-4 shrink-0" />
            <span>{{ errorMessage }}</span>
          </div>

          <form @submit.prevent="handleRegister" class="space-y-3">
            <div class="space-y-1">
              <Label for="reg-name" class="text-xs">Nama Lengkap</Label>
              <div class="relative">
                <IconUser class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                <Input
                  id="reg-name"
                  v-model="form.name"
                  type="text"
                  required
                  placeholder="Nama Lengkap Anda"
                  class="pl-9 text-xs h-8.5"
                />
              </div>
            </div>

            <div class="space-y-1">
              <Label for="reg-nim" class="text-xs">NIM (Nomor Induk Mahasiswa)</Label>
              <div class="relative">
                <IconId class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                <Input
                  id="reg-nim"
                  v-model="form.nim"
                  type="text"
                  required
                  placeholder="Contoh: 1202407009"
                  class="pl-9 text-xs h-8.5 font-mono"
                />
              </div>
            </div>

            <div class="space-y-1">
              <Label for="reg-email" class="text-xs">Email Kampus / Aktif</Label>
              <div class="relative">
                <IconMail class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                <Input
                  id="reg-email"
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="nim@students.itspku.ac.id"
                  class="pl-9 text-xs h-8.5"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div class="space-y-1">
                <Label for="reg-pass" class="text-xs">Kata Sandi</Label>
                <div class="relative">
                  <Input
                    id="reg-pass"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    placeholder="Min 6 karakter"
                    class="text-xs h-8.5 pr-8"
                  />
                  <button
                    type="button"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground"
                    @click="showPassword = !showPassword"
                  >
                    <IconEyeOff v-if="showPassword" class="size-3.5" />
                    <IconEye v-else class="size-3.5" />
                  </button>
                </div>
              </div>

              <div class="space-y-1">
                <Label for="reg-pass-conf" class="text-xs">Ulangi Sandi</Label>
                <Input
                  id="reg-pass-conf"
                  v-model="form.password_confirmation"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  placeholder="Konfirmasi"
                  class="text-xs h-8.5"
                />
              </div>
            </div>

            <Button
              type="submit"
              class="w-full text-xs h-9 font-semibold gap-1.5 bg-primary hover:bg-primary/90 mt-2"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="size-3.5 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></span>
              <IconUserPlus v-else class="size-4" />
              <span>{{ isLoading ? 'Mendaftarkan...' : 'Daftar Akun Mahasiswa' }}</span>
            </Button>
          </form>
        </CardContent>

        <CardFooter class="pt-0 pb-4 text-center justify-center">
          <p class="text-xs text-muted-foreground">
            Sudah memiliki akun?
            <NuxtLink to="/login" class="text-primary font-medium hover:underline ml-1">
              Masuk di sini
            </NuxtLink>
          </p>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
