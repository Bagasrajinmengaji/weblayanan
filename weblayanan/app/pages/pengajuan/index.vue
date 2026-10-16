<script setup lang="ts">
import {
  IconAlertCircle,
  IconArrowLeft,
  IconCheck,
  IconCopy,
  IconFileText,
  IconPaperclip,
  IconSend,
  IconUpload,
} from "@tabler/icons-vue"

import { Badge } from "~/components/ui/badge"
import { Button } from "~/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "~/components/ui/card"
import { Input } from "~/components/ui/input"
import { Label } from "~/components/ui/label"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "~/components/ui/select"

useSeoMeta({
  title: "Formulir Pengajuan Layanan - Portal Mahasiswa",
  description: "Ajukan permohonan surat administrasi akademik dengan mengunggah berkas persyaratan.",
})

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase || "http://localhost:8000/api"

// Ambil daftar layanan untuk dropdown opsi
const { data: layananResponse } = await useFetch<any>(`${apiBase}/layanan`)
const layananList = computed(() => layananResponse.value?.data || [])

// Form state
const initialLayananId = route.query.layanan_id ? String(route.query.layanan_id) : ""
const form = reactive({
  layanan_id: initialLayananId,
  nama_mahasiswa: "",
  nim: "",
  email: "",
  no_hp: "",
  catatan: "",
})

// File state
const selectedFiles = ref<File[]>([])
const fileError = ref<string | null>(null)

// Submission states
const isSubmitting = ref(false)
const errorMessage = ref<string | null>(null)
const successData = ref<{
  nomor_pelacakan: string
  message: string
} | null>(null)

const isCopied = ref(false)

// Cari layanan terpilih untuk menampilkan daftar persyaratannya
const selectedLayanan = computed(() => {
  if (!form.layanan_id) return null
  return layananList.value.find((l: any) => String(l.id) === String(form.layanan_id))
})

// Inisialisasi jika query param berubah
watch(
  () => route.query.layanan_id,
  (newId) => {
    if (newId) form.layanan_id = String(newId)
  },
  { immediate: true }
)

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    fileError.value = null
    const files = Array.from(target.files)
    
    // Validasi ukuran max 10MB per file
    for (const f of files) {
      if (f.size > 10 * 1024 * 1024) {
        fileError.value = `Ukuran file "${f.name}" melebihi batas 10MB.`
        return
      }
    }
    selectedFiles.value = files
  }
}

function removeFile(index: number) {
  selectedFiles.value.splice(index, 1)
}

function copyTrackingCode(code: string) {
  navigator.clipboard.writeText(code)
  isCopied.value = true
  setTimeout(() => {
    isCopied.value = false
  }, 2000)
}

async function submitPengajuan() {
  errorMessage.value = null
  fileError.value = null

  if (!form.layanan_id) {
    errorMessage.value = "Silakan pilih jenis layanan terlebih dahulu."
    return
  }
  if (!form.nama_mahasiswa || !form.nim || !form.email || !form.no_hp) {
    errorMessage.value = "Mohon lengkapi seluruh kolom identitas mahasiswa."
    return
  }

  isSubmitting.value = true

  try {
    const formData = new FormData()
    formData.append("layanan_id", form.layanan_id)
    formData.append("nama_mahasiswa", form.nama_mahasiswa)
    formData.append("nim", form.nim)
    formData.append("email", form.email)
    formData.append("no_hp", form.no_hp)
    if (form.catatan) {
      formData.append("catatan_revisi", form.catatan)
    }

    // Lampirkan berkas persyaratan jika ada
    if (selectedFiles.value.length === 1 && selectedFiles.value[0]) {
      formData.append("berkas_persyaratan", selectedFiles.value[0])
    } else if (selectedFiles.value.length > 1) {
      selectedFiles.value.forEach((file, idx) => {
        if (file) {
          formData.append(`berkas_persyaratan[${idx}]`, file)
        }
      })
    }

    const response = await $fetch<any>(`${apiBase}/pengajuan`, {
      method: "POST",
      body: formData,
    })

    if (response.status === "success") {
      successData.value = {
        nomor_pelacakan: response.nomor_pelacakan,
        message: response.message || "Pengajuan layanan Anda telah berhasil dikirim ke bagian akademik.",
      }
    } else {
      errorMessage.value = response.message || "Gagal mengirim pengajuan."
    }
  } catch (err: any) {
    if (err?.data?.errors && typeof err.data.errors === "object") {
      const errorKeys = Object.keys(err.data.errors)
      if (errorKeys.length > 0) {
        const firstKey = errorKeys[0] as string
        const errorMessages = err.data.errors[firstKey]
        errorMessage.value = Array.isArray(errorMessages) ? errorMessages[0] : String(errorMessages)
      } else {
        errorMessage.value = err?.data?.message || "Validasi formulir gagal."
      }
    } else {
      errorMessage.value = err?.data?.message || err?.message || "Terjadi kesalahan saat mengirim pengajuan."
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 lg:px-6 w-full flex flex-col gap-6">
    <!-- Tombol Kembali & Breadcrumb -->
    <div class="flex items-center gap-3">
      <Button variant="ghost" size="sm" as-child class="gap-1.5 -ml-2 text-muted-foreground hover:text-foreground">
        <NuxtLink to="/">
          <IconArrowLeft class="size-4" />
          Kembali ke Dashboard
        </NuxtLink>
      </Button>
    </div>

    <!-- Alert Sukses Setelah Pengajuan Berhasil Dibuat -->
    <div v-if="successData" class="rounded-xl border border-emerald-300 bg-emerald-50/70 dark:bg-emerald-950/20 dark:border-emerald-800 p-6 shadow-sm">
      <div class="flex flex-col items-center text-center max-w-lg mx-auto gap-4 py-4">
        <div class="size-16 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center">
          <IconCheck class="size-8" />
        </div>
        <div>
          <h2 class="text-2xl font-bold text-foreground">Permohonan Berhasil Diajukan!</h2>
          <p class="text-sm text-muted-foreground mt-1.5">
            {{ successData.message }}
          </p>
        </div>

        <!-- Box Nomor Pelacakan -->
        <div class="w-full bg-card rounded-lg border p-4 flex flex-col items-center gap-2">
          <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Nomor Pelacakan Anda</span>
          <div class="flex items-center gap-2">
            <span class="font-mono text-xl sm:text-2xl font-bold text-primary">{{ successData.nomor_pelacakan }}</span>
            <Button
              variant="outline"
              size="icon"
              class="size-8 shrink-0"
              @click="copyTrackingCode(successData.nomor_pelacakan)"
              title="Salin Nomor Pelacakan"
            >
              <IconCheck v-if="isCopied" class="size-4 text-emerald-600" />
              <IconCopy v-else class="size-4" />
            </Button>
          </div>
          <span class="text-xs text-muted-foreground">Simpan nomor ini untuk memantau proses verifikasi berkas Anda.</span>
        </div>

        <!-- Aksi Lanjutan -->
        <div class="flex flex-wrap items-center justify-center gap-3 mt-2">
          <Button variant="default" as-child>
            <NuxtLink :to="`/pengajuan/lacak?nomor=${encodeURIComponent(successData.nomor_pelacakan)}`">
              Lacak Pengajuan Sekarang
            </NuxtLink>
          </Button>
          <Button variant="outline" as-child>
            <NuxtLink to="/pengajuan/riwayat">
              Lihat Riwayat
            </NuxtLink>
          </Button>
          <Button variant="ghost" @click="successData = null; form.nama_mahasiswa = ''; form.nim = ''; selectedFiles = []">
            Ajukan Surat Lain
          </Button>
        </div>
      </div>
    </div>

    <!-- Form Utama Pengajuan -->
    <Card v-else class="border shadow-xs">
      <CardHeader>
        <CardTitle class="text-xl sm:text-2xl font-bold">
          Formulir Permohonan Layanan Mahasiswa
        </CardTitle>
        <CardDescription>
          Isi data identitas mahasiswa dan unggah dokumen pendukung sesuai persyaratan layanan.
        </CardDescription>
      </CardHeader>

      <CardContent>
        <form @submit.prevent="submitPengajuan" class="space-y-6">
          <!-- Alert Error -->
          <div v-if="errorMessage" class="rounded-lg border border-destructive/30 bg-destructive/5 p-4 text-sm text-destructive flex items-center gap-2.5">
            <IconAlertCircle class="size-4 shrink-0" />
            <span>{{ errorMessage }}</span>
          </div>

          <!-- Section 1: Pilihan Layanan -->
          <div class="space-y-2">
            <Label for="layanan_id" class="text-sm font-semibold">
              Jenis Layanan <span class="text-destructive">*</span>
            </Label>
            <Select v-model="form.layanan_id">
              <SelectTrigger id="layanan_id" class="w-full">
                <SelectValue placeholder="-- Pilih Layanan Akademik --" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="layanan in layananList"
                  :key="layanan.id"
                  :value="String(layanan.id)"
                >
                  {{ layanan.nama_layanan }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Box Info Persyaratan Layanan Terpilih -->
          <div
            v-if="selectedLayanan"
            class="rounded-lg border border-primary/20 bg-primary/5 p-4 space-y-2 text-sm"
          >
            <div class="font-semibold text-primary flex items-center gap-1.5">
              <IconFileText class="size-4" />
              Persyaratan: {{ selectedLayanan.nama_layanan }}
            </div>
            <p class="text-xs text-muted-foreground">
              {{ selectedLayanan.deskripsi }}
            </p>
            <div class="mt-2 pt-2 border-t border-primary/10">
              <span class="text-xs font-semibold text-foreground">Dokumen yang wajib disiapkan:</span>
              <ul class="list-disc list-inside text-xs text-muted-foreground mt-1 space-y-0.5">
                <li v-for="(syarat, idx) in selectedLayanan.persyaratan" :key="idx">
                  {{ syarat }}
                </li>
              </ul>
            </div>
          </div>

          <!-- Section 2: Identitas Mahasiswa -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="nama_mahasiswa" class="text-sm font-semibold">
                Nama Lengkap Mahasiswa <span class="text-destructive">*</span>
              </Label>
              <Input
                id="nama_mahasiswa"
                v-model="form.nama_mahasiswa"
                placeholder="Contoh: Bagas Pramono"
                required
              />
            </div>

            <div class="space-y-2">
              <Label for="nim" class="text-sm font-semibold">
                NIM (Nomor Induk Mahasiswa) <span class="text-destructive">*</span>
              </Label>
              <Input
                id="nim"
                v-model="form.nim"
                placeholder="Contoh: 2201011045"
                required
              />
            </div>

            <div class="space-y-2">
              <Label for="email" class="text-sm font-semibold">
                Email Mahasiswa / Kampus <span class="text-destructive">*</span>
              </Label>
              <Input
                id="email"
                type="email"
                v-model="form.email"
                placeholder="Contoh: bagas@student.ac.id"
                required
              />
            </div>

            <div class="space-y-2">
              <Label for="no_hp" class="text-sm font-semibold">
                Nomor WhatsApp / HP Aktif <span class="text-destructive">*</span>
              </Label>
              <Input
                id="no_hp"
                type="tel"
                v-model="form.no_hp"
                placeholder="Contoh: 081234567890"
                required
              />
            </div>
          </div>

          <!-- Section 3: Upload Berkas Persyaratan -->
          <div class="space-y-2">
            <Label class="text-sm font-semibold">
              Unggah Berkas Persyaratan (PDF / JPG / PNG)
            </Label>
            
            <div class="rounded-lg border-2 border-dashed border-muted-foreground/30 hover:border-primary/50 transition-colors p-6 text-center cursor-pointer bg-muted/20 relative">
              <input
                type="file"
                multiple
                accept=".pdf,.jpg,.jpeg,.png"
                @change="handleFileChange"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              />
              <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                <div class="size-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                  <IconUpload class="size-5" />
                </div>
                <div>
                  <span class="font-medium text-sm text-foreground">Klik atau seret file ke sini untuk mengunggah</span>
                  <p class="text-xs text-muted-foreground mt-0.5">Format didukung: PDF, JPG, PNG (Maksimum 10MB per file)</p>
                </div>
              </div>
            </div>

            <!-- List File Terpilih -->
            <div v-if="selectedFiles.length > 0" class="mt-3 space-y-1.5">
              <div
                v-for="(file, index) in selectedFiles"
                :key="index"
                class="flex items-center justify-between p-2 rounded-md border bg-card text-xs"
              >
                <div class="flex items-center gap-2 truncate max-w-sm">
                  <IconPaperclip class="size-3.5 text-primary shrink-0" />
                  <span class="truncate font-medium">{{ file.name }}</span>
                  <span class="text-muted-foreground shrink-0">({{ (file.size / 1024 / 1024).toFixed(2) }} MB)</span>
                </div>
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  class="h-6 text-destructive hover:bg-destructive/10 px-2"
                  @click="removeFile(index)"
                >
                  Hapus
                </Button>
              </div>
            </div>

            <p v-if="fileError" class="text-xs text-destructive mt-1">{{ fileError }}</p>
          </div>

          <!-- Section 4: Catatan Tambahan (Opsional) -->
          <div class="space-y-2">
            <Label for="catatan" class="text-sm font-semibold">
              Catatan / Keterangan Tambahan <span class="text-xs text-muted-foreground font-normal">(Opsional)</span>
            </Label>
            <textarea
              id="catatan"
              v-model="form.catatan"
              rows="3"
              placeholder="Tambahkan keterangan instansi tujuan surat, keperluan khusus, dll."
              class="flex min-h-20 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
            ></textarea>
          </div>

          <!-- Tombol Submit -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t">
            <Button variant="outline" type="button" as-child>
              <NuxtLink to="/">Batal</NuxtLink>
            </Button>
            <Button type="submit" :disabled="isSubmitting" class="gap-2 min-w-35">
              <span v-if="isSubmitting" class="size-4 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></span>
              <IconSend v-else class="size-4" />
              <span>{{ isSubmitting ? 'Mengirim...' : 'Kirim Pengajuan' }}</span>
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
