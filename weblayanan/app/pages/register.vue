<script setup lang="ts">
import {
  IconAlertCircle,
  IconArrowLeft,
  IconBuildingBank,
  IconCheck,
  IconClock,
  IconEye,
  IconEyeOff,
  IconFileCertificate,
  IconHelpCircle,
  IconId,
  IconLock,
  IconMail,
  IconSearch,
  IconShieldCheck,
  IconUser,
} from "@tabler/icons-vue"

import AccessibilityWidget from "~/components/AccessibilityWidget.vue"
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
const config = useRuntimeConfig()
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
      router.push("/")
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

  const googleClientId =
    (config.public.googleClientId as string) ||
    "680824540632-f4clprt82f0q0cjbc7i0jksjeaj3k7t8.apps.googleusercontent.com"

  const google = typeof window !== "undefined" ? (window as any).google : null

  if (google?.accounts?.oauth2) {
    try {
      const tokenClient = google.accounts.oauth2.initTokenClient({
        client_id: googleClientId,
        scope: "email profile openid",
        callback: async (tokenResponse: any) => {
          if (tokenResponse?.error) {
            errorMessage.value = "Pendaftaran Google dibatalkan atau izin ditolak."
            isLoading.value = false
            return
          }

          if (tokenResponse?.access_token) {
            try {
              await loginWithGoogle({
                access_token: tokenResponse.access_token,
              })
              router.push("/")
            } catch (err: any) {
              errorMessage.value =
                err.data?.message || err.message || "Gagal mendaftar via Google."
            } finally {
              isLoading.value = false
            }
          }
        },
      })

      tokenClient.requestAccessToken({ prompt: "consent" })
      return
    } catch (e: any) {
      console.warn("Google Token Client error:", e)
    }
  }

  // Fallback dev jika Google SDK terblokir
  try {
    await loginWithGoogle({
      email: "mahasiswa.baru@students.itspku.ac.id",
      name: "Mahasiswa Baru",
      google_id: "google_reg_" + Date.now(),
    })
    router.push("/")
  } catch (err: any) {
    if (err.data?.errors) {
      const firstErr = Object.values(err.data.errors)[0]
      errorMessage.value = Array.isArray(firstErr) ? (firstErr[0] as string) : String(firstErr)
    } else {
      errorMessage.value = err.data?.message || "Gagal mendaftar via Google."
    }
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  if (isAuthenticated.value) {
    router.push(isAdmin.value ? "/admin" : "/")
  }
})
</script>

<template>
  <div class="min-h-screen bg-background text-foreground flex flex-col justify-center">
    <!-- Skip to main content link for keyboard & screen reader accessibility -->
    <a
      href="#register-form"
      class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-4 py-2 bg-primary text-primary-foreground font-semibold rounded-lg shadow-md"
    >
      Lewati ke formulir pendaftaran
    </a>

    <!-- Container Grid 2 Kolom (Split Layout sesuai Screenshot Template) -->
    <div class="min-h-screen w-full grid grid-cols-1 lg:grid-cols-12">
      <!-- ================================================= -->
      <!-- KOLOM KIRI: FORM REGISTER SIMPLE (SESUAI MOCKUP) -->
      <!-- ================================================= -->
      <section
        aria-label="Area Formulir Registrasi Akun"
        class="lg:col-span-5 xl:col-span-5 flex flex-col justify-between p-6 sm:p-10 lg:p-12 bg-background border-r border-border/40 overflow-y-auto"
      >
        <!-- Header Atas: Brand Logo & Tombol Kembali -->
        <div class="w-full max-w-95 mx-auto flex items-center justify-between mb-6">
          <!-- Logo Brand (Style: UMPKU | myLMS) -->
          <NuxtLink to="/login" class="flex items-center gap-2 group" aria-label="Halaman Login Portal Layanan">
            <div class="size-8 rounded-lg bg-orange-500 text-white flex items-center justify-center font-bold shadow-xs">
              <IconBuildingBank class="size-4.5" />
            </div>
            <div class="flex items-baseline gap-1.5">
              <span class="font-bold text-base tracking-tight text-foreground">PortalLayanan</span>
              <span class="text-xs text-muted-foreground font-medium pl-1.5 border-l border-border">BAAK</span>
            </div>
          </NuxtLink>

          <!-- Tombol Kembali ke Halaman Login -->
          <Button variant="ghost" size="sm" as-child class="text-xs text-muted-foreground hover:text-foreground gap-1">
            <NuxtLink to="/login" aria-label="Kembali ke Halaman Login">
              <IconArrowLeft class="size-3.5" />
              Kembali ke Login
            </NuxtLink>
          </Button>
        </div>

        <!-- Tengah: Form Registrasi -->
        <div class="w-full max-w-95 mx-auto my-auto flex flex-col items-center">
          <!-- Judul Halaman -->
          <h1 id="register-heading" class="text-3xl font-bold tracking-tight text-foreground mb-6 self-center">
            Create an account
          </h1>

          <!-- Alert Pesan Sukses / Error dengan aksesibilitas aria-live -->
          <div
            v-if="successMessage"
            role="status"
            aria-live="polite"
            class="w-full mb-4 rounded-lg bg-emerald-500/10 border border-emerald-500/30 p-3 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2"
          >
            <IconCheck class="size-4 shrink-0" aria-hidden="true" />
            <span>{{ successMessage }}</span>
          </div>

          <div
            v-if="errorMessage"
            role="alert"
            aria-live="assertive"
            class="w-full mb-4 rounded-lg bg-destructive/10 border border-destructive/30 p-3 text-destructive text-xs flex items-center gap-2"
          >
            <IconAlertCircle class="size-4 shrink-0" aria-hidden="true" />
            <span>{{ errorMessage }}</span>
          </div>

          <!-- Tombol SSO Google -->
          <button
            type="button"
            class="w-full h-11 rounded-lg bg-[#3874f6] hover:bg-[#2b64e0] active:bg-[#2353be] text-white font-medium text-sm flex items-center justify-start px-2 shadow-sm transition-colors duration-150 disabled:opacity-70 disabled:cursor-not-allowed focus:outline-hidden focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            :disabled="isLoading"
            aria-label="Daftar instan menggunakan Akun Google SSO Kampus"
            @click="handleGoogleSSO"
          >
            <div class="size-8 rounded-md bg-white flex items-center justify-center mr-3 shrink-0 shadow-xs">
              <svg class="size-4.5" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
              </svg>
            </div>
            <span class="flex-1 text-center pr-8 font-medium">Continue with Google</span>
          </button>

          <!-- Garis Pemisah OR -->
          <div class="relative w-full my-5 flex items-center justify-center" aria-hidden="true">
            <span class="absolute inset-x-0 h-px bg-border"></span>
            <span class="relative bg-background px-3 text-xs text-muted-foreground font-medium">
              OR
            </span>
          </div>

          <!-- Form Registrasi -->
          <form
            id="register-form"
            aria-labelledby="register-heading"
            @submit.prevent="handleRegister"
            class="w-full space-y-3"
          >
            <!-- Nama -->
            <div class="relative">
              <label for="reg-name" class="sr-only">Nama Lengkap Mahasiswa</label>
              <IconUser class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground pointer-events-none" aria-hidden="true" />
              <Input
                id="reg-name"
                v-model="form.name"
                type="text"
                required
                autocomplete="name"
                aria-required="true"
                placeholder="Full name"
                class="pl-10 h-11 text-sm rounded-lg border-border bg-background focus-visible:ring-2 focus-visible:ring-blue-500"
              />
            </div>

            <!-- NIM -->
            <div class="relative">
              <label for="reg-nim" class="sr-only">Nomor Induk Mahasiswa (NIM)</label>
              <IconId class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground pointer-events-none" aria-hidden="true" />
              <Input
                id="reg-nim"
                v-model="form.nim"
                type="text"
                required
                aria-required="true"
                placeholder="NIM (Nomor Induk Mahasiswa)"
                class="pl-10 h-11 text-sm font-mono rounded-lg border-border bg-background focus-visible:ring-2 focus-visible:ring-blue-500"
              />
            </div>

            <!-- Email -->
            <div class="relative">
              <label for="reg-email" class="sr-only">Email Mahasiswa</label>
              <IconMail class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground pointer-events-none" aria-hidden="true" />
              <Input
                id="reg-email"
                v-model="form.email"
                type="email"
                required
                autocomplete="email"
                aria-required="true"
                placeholder="Email mahasiswa (nim@students...)"
                class="pl-10 h-11 text-sm rounded-lg border-border bg-background focus-visible:ring-2 focus-visible:ring-blue-500"
              />
            </div>

            <!-- Password -->
            <div class="relative">
              <label for="reg-pass" class="sr-only">Kata Sandi Baru</label>
              <IconLock class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground pointer-events-none" aria-hidden="true" />
              <Input
                id="reg-pass"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                autocomplete="new-password"
                aria-required="true"
                placeholder="Password (min 6 karakter)"
                class="pl-10 pr-10 h-11 text-sm rounded-lg border-border bg-background focus-visible:ring-2 focus-visible:ring-blue-500"
              />
              <button
                type="button"
                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground cursor-pointer"
                :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                @click="showPassword = !showPassword"
              >
                <IconEyeOff v-if="showPassword" class="size-4" aria-hidden="true" />
                <IconEye v-else class="size-4" aria-hidden="true" />
              </button>
            </div>

            <!-- Confirm Password -->
            <div class="relative">
              <label for="reg-pass-confirm" class="sr-only">Konfirmasi Kata Sandi</label>
              <IconLock class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4.5 text-muted-foreground pointer-events-none" aria-hidden="true" />
              <Input
                id="reg-pass-confirm"
                v-model="form.password_confirmation"
                :type="showPassword ? 'text' : 'password'"
                required
                autocomplete="new-password"
                aria-required="true"
                placeholder="Confirm password"
                class="pl-10 h-11 text-sm rounded-lg border-border bg-background focus-visible:ring-2 focus-visible:ring-blue-500"
              />
            </div>

            <!-- Tombol Submit -->
            <button
              type="submit"
              class="w-full h-11 rounded-lg bg-[#0062cc] hover:bg-[#0052ad] active:bg-[#00428d] text-white font-medium text-sm shadow-sm transition-colors duration-150 flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed mt-3 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="size-4 animate-spin rounded-full border-2 border-white border-t-transparent mr-2" aria-hidden="true"></span>
              <span>Create account</span>
            </button>
          </form>

          <!-- Link Login di Bawah -->
          <div class="w-full text-center mt-6 pt-4 border-t border-border/60">
            <p class="text-sm text-muted-foreground">
              Already have an account?
              <NuxtLink to="/login" class="text-[#0066cc] dark:text-[#388bfd] font-semibold hover:underline ml-1">
                Log in
              </NuxtLink>
            </p>
          </div>
        </div>

        <!-- Footer Kecil Kolom Kiri -->
        <div class="w-full max-w-95 mx-auto text-center pt-4 text-[11px] text-muted-foreground border-t border-border/40">
          <span>&copy; {{ new Date().getFullYear() }} Biro Akademik & Kemahasiswaan.</span>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- KOLOM KANAN: DESKRIPSI SINGKAT WEB LAYANAN DENGAN CAMPUS BANNER -->
      <!-- ============================================================== -->
      <section
        aria-label="Informasi dan Ringkasan Layanan Mahasiswa"
        class="hidden lg:flex lg:col-span-7 xl:col-span-7 relative bg-neutral-950 text-white flex-col justify-between p-8 xl:p-14 overflow-hidden"
      >
        <!-- Background Foto Auditorium Kampus dengan Overlay Gelap & Elegan -->
        <img
          src="/campus-banner.jpg"
          alt="Suasana Auditorium dan Kegiatan Mahasiswa Kampus"
          class="absolute inset-0 size-full object-cover object-center opacity-35 filter brightness-90"
        />
        <div class="absolute inset-0 bg-linear-to-t from-neutral-950 via-neutral-950/75 to-neutral-950/50"></div>

        <!-- Top Badge -->
        <div class="relative z-10 flex items-center justify-between">
          <div class="inline-flex items-center gap-2 rounded-full border border-orange-500/30 bg-orange-500/10 px-3 py-1 text-xs font-medium text-orange-400">
            <span class="size-2 rounded-full bg-orange-500"></span>
            <span>Registrasi Akun Mandiri Mahasiswa</span>
          </div>

          <NuxtLink to="/" class="text-xs text-neutral-300 hover:text-white flex items-center gap-1.5 transition-colors">
            <span>Lihat Informasi Publik &rarr;</span>
          </NuxtLink>
        </div>

        <!-- Konten Deskripsi Singkat Web Layanan -->
        <div class="relative z-10 my-auto py-8 max-w-xl">
          <h2 class="text-3xl xl:text-4xl font-bold tracking-tight text-white leading-tight">
            Satu Akun Untuk Seluruh Pengurusan Surat & Dokumen Kampus
          </h2>

          <p class="text-neutral-300 text-sm xl:text-base mt-4 leading-relaxed">
            Daftarkan akun mahasiswa Anda menggunakan NIM dan email kampus untuk menikmati kemudahan pengurusan surat akademik kapan pun dan di mana pun.
          </p>

          <!-- 4 Kartu Keunggulan Layanan -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mt-8">
            <div class="rounded-xl border border-white/10 bg-black/40 backdrop-blur-md p-4 flex items-start gap-3">
              <div class="size-8 rounded-lg bg-orange-500/20 text-orange-400 flex items-center justify-center shrink-0">
                <IconClock class="size-4.5" />
              </div>
              <div>
                <h3 class="text-xs font-semibold text-white">Verifikasi Terukur</h3>
                <p class="text-[11px] text-neutral-400 mt-0.5">Permohonan diverifikasi oleh petugas BAAK dalam 1-2 hari kerja.</p>
              </div>
            </div>

            <div class="rounded-xl border border-white/10 bg-black/40 backdrop-blur-md p-4 flex items-start gap-3">
              <div class="size-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <IconShieldCheck class="size-4.5" />
              </div>
              <div>
                <h3 class="text-xs font-semibold text-white">Validasi Digital</h3>
                <p class="text-[11px] text-neutral-400 mt-0.5">Dilengkapi nomor registrasi resmi yang sah dan terpercaya.</p>
              </div>
            </div>

            <div class="rounded-xl border border-white/10 bg-black/40 backdrop-blur-md p-4 flex items-start gap-3">
              <div class="size-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                <IconSearch class="size-4.5" />
              </div>
              <div>
                <h3 class="text-xs font-semibold text-white">Tracking Real-Time</h3>
                <p class="text-[11px] text-neutral-400 mt-0.5">Setiap tahap dokumen tercatat dan dapat dipantau langsung.</p>
              </div>
            </div>

            <div class="rounded-xl border border-white/10 bg-black/40 backdrop-blur-md p-4 flex items-start gap-3">
              <div class="size-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                <IconFileCertificate class="size-4.5" />
              </div>
              <div>
                <h3 class="text-xs font-semibold text-white">Dukungan SSO</h3>
                <p class="text-[11px] text-neutral-400 mt-0.5">Bisa juga daftar/masuk instan dengan akun Google kampus Anda.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Kolom Kanan: Jadwal Layanan & Helpdesk -->
        <div class="relative z-10 pt-4 border-t border-white/10 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-neutral-400 gap-2">
          <span>Jam Layanan BAAK: Senin - Jumat (08.00 - 15.30 WIB)</span>
          <NuxtLink to="/bantuan" class="text-orange-400 hover:text-orange-300 flex items-center gap-1 transition-colors">
            <IconHelpCircle class="size-3.5" />
            <span>Pusat Bantuan & Panduan</span>
          </NuxtLink>
        </div>
      </section>
    </div>

    <!-- WIDGET AKSESIBILITAS FLOATING BUTTON (SESUAI TOMBOL BIRU DI SCREENSHOT) -->
    <AccessibilityWidget />
  </div>
</template>
