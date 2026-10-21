<script setup lang="ts">
import {
  IconAlertCircle,
  IconArrowLeft,
  IconCheck,
  IconEye,
  IconEyeOff,
  IconId,
  IconLock,
  IconMail,
  IconUser,
} from "@tabler/icons-vue"

import { Button } from "~/components/ui/button"
import { Input } from "~/components/ui/input"

definePageMeta({
  layout: false,
})

useSeoMeta({
  title: "Create an account - Portal Layanan Mahasiswa",
  description: "Daftar akun mahasiswa untuk layanan persuratan akademik.",
})

const router = useRouter()
const { register, loginWithGoogle, isAuthenticated, isAdmin } = useAuth()

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

    successMessage.value = "Akun berhasil dibuat! Mengalihkan..."
    setTimeout(() => {
      router.push("/dashboard")
    }, 600)
  } catch (err: any) {
    if (err.data?.errors) {
      const firstErr = Object.values(err.data.errors)[0]
      errorMessage.value = Array.isArray(firstErr) ? (firstErr[0] as string) : String(firstErr)
    } else {
      errorMessage.value = err.data?.message || err.message || "Gagal membuat akun. Periksa kembali data Anda."
    }
  } finally {
    isLoading.value = false
  }
}

async function handleGoogleSSO() {
  if (isLoading.value) return
  isLoading.value = true
  errorMessage.value = null
  try {
    await loginWithGoogle({
      email: "mahasiswa.baru@students.itspku.ac.id",
      name: "Mahasiswa Baru",
      google_id: "google_reg_" + Date.now(),
    })
    router.push("/dashboard")
  } catch (err: any) {
    if (err.data?.errors) {
      const firstErr = Object.values(err.data.errors)[0]
      errorMessage.value = Array.isArray(firstErr) ? (firstErr[0] as string) : String(firstErr)
    } else {
      errorMessage.value = err.data?.message || err.message || "Gagal mendaftar via Google."
    }
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  if (isAuthenticated.value) {
    router.push(isAdmin.value ? "/admin" : "/dashboard")
  }
})
</script>

<template>
  <div class="min-h-screen bg-background text-foreground flex flex-col justify-center items-center px-4 py-8">
    <div class="w-full max-w-[380px] flex flex-col items-center">
      <!-- Tombol Kembali ke Landing Page -->
      <div class="w-full flex justify-start mb-2">
        <Button variant="ghost" size="sm" as-child class="text-xs text-muted-foreground hover:text-foreground -ml-2 gap-1">
          <NuxtLink to="/">
            <IconArrowLeft class="size-3.5" />
            Kembali ke Beranda
          </NuxtLink>
        </Button>
      </div>

      <!-- Judul -->
      <h1 class="text-3xl font-bold tracking-tight text-foreground mb-6">
        Create an account
      </h1>

      <!-- Alert Pesan Sukses / Error -->
      <div v-if="successMessage" class="w-full mb-4 rounded-lg bg-emerald-500/10 border border-emerald-500/30 p-3 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2">
        <IconCheck class="size-4 shrink-0" />
        <span>{{ successMessage }}</span>
      </div>

      <div v-if="errorMessage" class="w-full mb-4 rounded-lg bg-destructive/10 border border-destructive/30 p-3 text-destructive text-xs flex items-center gap-2">
        <IconAlertCircle class="size-4 shrink-0" />
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Tombol SSO Google -->
      <button
        type="button"
        class="w-full h-11 rounded-lg bg-[#3874f6] hover:bg-[#2b64e0] active:bg-[#2353be] text-white font-medium text-sm flex items-center justify-start px-2 shadow-sm transition-colors duration-150 disabled:opacity-70 disabled:cursor-not-allowed"
        :disabled="isLoading"
        @click="handleGoogleSSO"
      >
        <div class="size-8 rounded-md bg-white flex items-center justify-center mr-3 shrink-0 shadow-xs">
          <svg class="size-4.5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
          </svg>
        </div>
        <span class="flex-1 text-center pr-8 font-medium">Continue with Google</span>
      </button>

      <!-- Garis Pemisah OR -->
      <div class="relative w-full my-6 flex items-center justify-center">
        <span class="absolute inset-x-0 h-px bg-border"></span>
        <span class="relative bg-background px-3 text-xs text-muted-foreground font-medium">
          OR
        </span>
      </div>

      <!-- Form Registrasi -->
      <form @submit.prevent="handleRegister" class="w-full space-y-3">
        <!-- Nama -->
        <div class="relative">
          <IconUser class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground" />
          <Input
            id="reg-name"
            v-model="form.name"
            type="text"
            required
            placeholder="Full name"
            class="pl-10 h-11 text-sm rounded-lg border-border bg-background"
          />
        </div>

        <!-- NIM -->
        <div class="relative">
          <IconId class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground" />
          <Input
            id="reg-nim"
            v-model="form.nim"
            type="text"
            required
            placeholder="NIM (Nomor Induk Mahasiswa)"
            class="pl-10 h-11 text-sm font-mono rounded-lg border-border bg-background"
          />
        </div>

        <!-- Email -->
        <div class="relative">
          <IconMail class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground" />
          <Input
            id="reg-email"
            v-model="form.email"
            type="email"
            required
            placeholder="Email mahasiswa (nim@students...)"
            class="pl-10 h-11 text-sm rounded-lg border-border bg-background"
          />
        </div>

        <!-- Password -->
        <div class="relative">
          <IconLock class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground" />
          <Input
            id="reg-pass"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            required
            placeholder="Password (min 6 karakter)"
            class="pl-10 pr-10 h-11 text-sm rounded-lg border-border bg-background"
          />
          <button
            type="button"
            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
            @click="showPassword = !showPassword"
          >
            <IconEyeOff v-if="showPassword" class="size-4" />
            <IconEye v-else class="size-4" />
          </button>
        </div>

        <!-- Confirm Password -->
        <div class="relative">
          <IconLock class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground" />
          <Input
            id="reg-pass-confirm"
            v-model="form.password_confirmation"
            :type="showPassword ? 'text' : 'password'"
            required
            placeholder="Confirm password"
            class="pl-10 h-11 text-sm rounded-lg border-border bg-background"
          />
        </div>

        <!-- Tombol Submit -->
        <button
          type="submit"
          class="w-full h-11 rounded-lg bg-[#0062cc] hover:bg-[#0052ad] active:bg-[#00428d] text-white font-medium text-sm shadow-sm transition-colors duration-150 flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed mt-2"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="size-4 animate-spin rounded-full border-2 border-white border-t-transparent mr-2"></span>
          <span>Create account</span>
        </button>
      </form>

      <!-- Link Login di Bawah -->
      <div class="w-full text-center mt-8 pt-4 border-t border-border/60">
        <p class="text-sm text-muted-foreground">
          Already have an account?
          <NuxtLink to="/login" class="text-[#0066cc] dark:text-[#388bfd] font-semibold hover:underline ml-1">
            Log in
          </NuxtLink>
        </p>
      </div>
    </div>
  </div>
</template>
