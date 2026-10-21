<script setup lang="ts">
import {
  IconArrowLeft,
  IconBell,
  IconCheck,
  IconDeviceFloppy,
  IconSettings,
  IconUser,
} from "@tabler/icons-vue"

import { Button } from "~/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "~/components/ui/card"
import { Checkbox } from "~/components/ui/checkbox"
import { Input } from "~/components/ui/input"
import { Label } from "~/components/ui/label"

useSeoMeta({
  title: "Pengaturan Akun Mahasiswa - Portal Layanan",
  description: "Kelola profil mahasiswa dan preferensi notifikasi surat akademik.",
})

const form = reactive({
  name: "Bagas Pramono",
  nim: "2201011045",
  email: "mahasiswa@pku.ac.id",
  phone: "081234567890",
  notifyWhatsapp: true,
  notifyEmail: true,
})

const isSaving = ref(false)
const saveSuccess = ref(false)

function saveSettings() {
  isSaving.value = true
  saveSuccess.value = false

  setTimeout(() => {
    isSaving.value = false
    saveSuccess.value = true
    setTimeout(() => {
      saveSuccess.value = false
    }, 3000)
  }, 600)
}
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 lg:px-6 w-full flex flex-col gap-6">
    <!-- Breadcrumb & Navigasi -->
    <div class="flex items-center gap-3">
      <Button variant="ghost" size="sm" as-child class="gap-1.5 -ml-2 text-muted-foreground hover:text-foreground">
        <NuxtLink to="/dashboard">
          <IconArrowLeft class="size-4" />
          Kembali ke Dashboard
        </NuxtLink>
      </Button>
    </div>

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl flex items-center gap-2">
          <IconSettings class="size-7 text-primary" />
          Pengaturan Portal
        </h1>
        <p class="text-sm text-muted-foreground mt-1">
          Kelola informasi identitas mahasiswa dan preferensi pemberitahuan status berkas.
        </p>
      </div>

      <!-- Tombol Simpan -->
      <Button @click="saveSettings" :disabled="isSaving" class="gap-2 shrink-0">
        <IconCheck v-if="saveSuccess" class="size-4 text-emerald-400" />
        <IconDeviceFloppy v-else class="size-4" />
        <span>{{ isSaving ? 'Menyimpan...' : saveSuccess ? 'Tersimpan!' : 'Simpan Pengaturan' }}</span>
      </Button>
    </div>

    <!-- Alert Notifikasi Berhasil Simpan -->
    <div v-if="saveSuccess" class="rounded-lg border border-emerald-300 bg-emerald-50/80 dark:bg-emerald-950/20 p-4 text-sm text-emerald-800 dark:text-emerald-200 flex items-center gap-2">
      <IconCheck class="size-5 shrink-0" />
      <span>Pengaturan profil dan preferensi notifikasi berhasil diperbarui.</span>
    </div>

    <!-- Bagian 1: Data Profil Mahasiswa -->
    <Card class="border shadow-xs">
      <CardHeader>
        <CardTitle class="text-lg font-bold flex items-center gap-2">
          <IconUser class="size-5 text-primary" />
          Profil Mahasiswa
        </CardTitle>
        <CardDescription>
          Data ini akan otomatis terisi saat Anda mengajukan surat administrasi baru.
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="name" class="text-sm font-semibold">Nama Lengkap</Label>
            <Input id="name" v-model="form.name" placeholder="Nama Mahasiswa" />
          </div>
          <div class="space-y-2">
            <Label for="nim" class="text-sm font-semibold">NIM</Label>
            <Input id="nim" v-model="form.nim" placeholder="Nomor Induk Mahasiswa" />
          </div>
          <div class="space-y-2">
            <Label for="email" class="text-sm font-semibold">Email Kampus</Label>
            <Input id="email" type="email" v-model="form.email" placeholder="email@student.ac.id" />
          </div>
          <div class="space-y-2">
            <Label for="phone" class="text-sm font-semibold">Nomor WhatsApp</Label>
            <Input id="phone" v-model="form.phone" placeholder="08xxxxxxxxxx" />
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Bagian 2: Preferensi Notifikasi -->
    <Card class="border shadow-xs">
      <CardHeader>
        <CardTitle class="text-lg font-bold flex items-center gap-2">
          <IconBell class="size-5 text-primary" />
          Preferensi Pemberitahuan Status
        </CardTitle>
        <CardDescription>
          Tentukan saluran notifikasi saat berkas Anda mengalami perubahan status verifikasi.
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="flex items-start space-x-3 rounded-lg border p-3 bg-muted/20">
          <Checkbox id="notify-wa" v-model="form.notifyWhatsapp" class="mt-0.5" />
          <div class="space-y-0.5">
            <Label for="notify-wa" class="text-sm font-medium cursor-pointer">
              Notifikasi WhatsApp Otomatis
            </Label>
            <p class="text-xs text-muted-foreground">
              Terima pesan WhatsApp instan saat permohonan disetujui, ditolak, atau memerlukan revisi berkas.
            </p>
          </div>
        </div>

        <div class="flex items-start space-x-3 rounded-lg border p-3 bg-muted/20">
          <Checkbox id="notify-email" v-model="form.notifyEmail" class="mt-0.5" />
          <div class="space-y-0.5">
            <Label for="notify-email" class="text-sm font-medium cursor-pointer">
              Notifikasi Email & Lampiran Dokumen
            </Label>
            <p class="text-xs text-muted-foreground">
              Terima salinan surat digital bertanda tangan elektronik (TTE) resmi langsung ke kotak masuk email.
            </p>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
