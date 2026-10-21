<script setup lang="ts">
import {
  IconAlertCircle,
  IconAlertTriangle,
  IconArrowLeft,
  IconCheck,
  IconCopy,
  IconEye,
  IconFile,
  IconFileCheck,
  IconFileText,
  IconPaperclip,
  IconRefresh,
  IconSend,
  IconTrash,
  IconUpload,
} from "@tabler/icons-vue"

import { Badge } from "~/components/ui/badge"
import { Button } from "~/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "~/components/ui/card"
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "~/components/ui/dialog"
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
  description: "Ajukan permohonan surat administrasi akademik dengan mengunggah berkas persyaratan terstruktur.",
})

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase || "http://localhost:8000/api"

// Ambil daftar layanan untuk dropdown opsi secara lazy (non-blocking)
const { data: layananResponse } = useLazyFetch<any>(`${apiBase}/layanan`, {
  default: () => ({ data: [] }),
})
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

// File state per slot persyaratan
const filesBySlot = ref<Record<number, File | null>>({})
const fileErrorsBySlot = ref<Record<number, string | null>>({})

// Review Dialog State
const isReviewDialogOpen = ref(false)

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

// Persyaratan dokumen terstruktur
const requirementsList = computed<string[]>(() => {
  if (!selectedLayanan.value) return []
  return selectedLayanan.value.persyaratan || []
})

// Inisialisasi jika query param berubah
watch(
  () => route.query.layanan_id,
  (newId) => {
    if (newId) {
      form.layanan_id = String(newId)
    }
  },
  { immediate: true }
)

// Reset slot files jika layanan berganti
watch(
  () => form.layanan_id,
  () => {
    filesBySlot.value = {}
    fileErrorsBySlot.value = {}
  }
)

const allowedExtensions = [".pdf", ".jpg", ".jpeg", ".png"]
const maxFileSizeBytes = 10 * 1024 * 1024 // 10MB

function validateFile(file: File): string | null {
  const extension = "." + file.name.split(".").pop()?.toLowerCase()
  if (!allowedExtensions.includes(extension)) {
    return `Format file "${file.name}" tidak didukung. Harap unggah berkas PDF, JPG, atau PNG.`
  }
  if (file.size > maxFileSizeBytes) {
    const sizeMb = (file.size / (1024 * 1024)).toFixed(1)
    return `Ukuran file "${file.name}" (${sizeMb} MB) melebihi batas maksimal 10MB.`
  }
  return null
}

function handleSlotFileChange(slotIndex: number, event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    const file = target.files[0]
    if (!file) return

    const validationError = validateFile(file)
    if (validationError) {
      fileErrorsBySlot.value[slotIndex] = validationError
      target.value = "" // reset input
      return
    }

    fileErrorsBySlot.value[slotIndex] = null
    filesBySlot.value[slotIndex] = file
  }
}

function removeSlotFile(slotIndex: number) {
  filesBySlot.value[slotIndex] = null
  fileErrorsBySlot.value[slotIndex] = null
  const inputEl = document.getElementById(`slot-input-${slotIndex}`) as HTMLInputElement | null
  if (inputEl) inputEl.value = ""
}

function triggerSlotInput(slotIndex: number) {
  const inputEl = document.getElementById(`slot-input-${slotIndex}`) as HTMLInputElement | null
  if (inputEl) inputEl.click()
}

// Daftar berkas yang telah terunggah
const uploadedFilesSummary = computed(() => {
  const result: { slotIndex: number; requirementName: string; file: File }[] = []
  if (requirementsList.value.length > 0) {
    requirementsList.value.forEach((req, idx) => {
      const file = filesBySlot.value[idx]
      if (file) {
        result.push({ slotIndex: idx, requirementName: req, file })
      }
    })
  } else {
    // Layanan tanpa persyaratan spesifik (slot 0)
    const file = filesBySlot.value[0]
    if (file) {
      result.push({ slotIndex: 0, requirementName: "Dokumen Persyaratan Umum", file })
    }
  }
  return result
})

function formatFileSize(bytes: number) {
  if (bytes < 1024) return `${bytes} B`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
  return `${(bytes / (1024 * 1024)).toFixed(2)} MB`
}

function copyTrackingCode(code: string) {
  navigator.clipboard.writeText(code)
  isCopied.value = true
  setTimeout(() => {
    isCopied.value = false
  }, 2000)
}

// Langkah 1: Validasi form sebelum membuka Dialog Ringkasan
function handleReview() {
  errorMessage.value = null

  if (!form.layanan_id) {
    errorMessage.value = "Silakan pilih jenis layanan akademik terlebih dahulu."
    return
  }
  if (!form.nama_mahasiswa.trim() || !form.nim.trim() || !form.email.trim() || !form.no_hp.trim()) {
    errorMessage.value = "Mohon lengkapi seluruh kolom identitas mahasiswa bertanda bintang (*)."
    return
  }

  // Buka Modal Dialog Ringkasan
  isReviewDialogOpen.value = true
}

// Langkah 2: Submit ke backend API setelah konfirmasi
async function confirmAndSubmit() {
  isReviewDialogOpen.value = false
  errorMessage.value = null
  isSubmitting.value = true

  try {
    const formData = new FormData()
    formData.append("layanan_id", form.layanan_id)
    formData.append("nama_mahasiswa", form.nama_mahasiswa.trim())
    formData.append("nim", form.nim.trim())
    formData.append("email", form.email.trim())
    formData.append("no_hp", form.no_hp.trim())
    if (form.catatan.trim()) {
      formData.append("catatan_revisi", form.catatan.trim())
    }

    // Lampirkan berkas dari slot yang terisi
    const filesToUpload: File[] = []
    Object.keys(filesBySlot.value).forEach((key) => {
      const f = filesBySlot.value[Number(key)]
      if (f) filesToUpload.push(f)
    })

    if (filesToUpload.length === 1 && filesToUpload[0]) {
      formData.append("berkas_persyaratan", filesToUpload[0])
    } else if (filesToUpload.length > 1) {
      filesToUpload.forEach((file, idx) => {
        formData.append(`berkas_persyaratan[${idx}]`, file)
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
          <Button variant="ghost" @click="successData = null; form.nama_mahasiswa = ''; form.nim = ''; filesBySlot = {}">
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
          Isi data identitas mahasiswa dan unggah dokumen pendukung sesuai persyaratan slot layanan.
        </CardDescription>
      </CardHeader>

      <CardContent>
        <form @submit.prevent="handleReview" class="space-y-6">
          <!-- Alert Error Utama -->
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

          <!-- Section 3: Unggah Berkas Persyaratan (Multi-Dropzone / Slot Spesifik) -->
          <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
              <div>
                <Label class="text-sm font-semibold flex items-center gap-1.5">
                  <IconFileCheck class="size-4 text-primary" />
                  Unggah Berkas Persyaratan
                </Label>
                <p class="text-xs text-muted-foreground mt-0.5">
                  Format yang didukung: <strong>PDF, JPG, PNG</strong> &bull; Maksimum ukuran <strong>10MB</strong> per file.
                </p>
              </div>
              <Badge variant="outline" class="text-xs">
                {{ uploadedFilesSummary.length }} / {{ requirementsList.length || 1 }} Berkas Terunggah
              </Badge>
            </div>

            <!-- Tampilan Jika Layanan Memiliki Daftar Persyaratan Spesifik -->
            <div v-if="requirementsList.length > 0" class="space-y-3">
              <div
                v-for="(syarat, idx) in requirementsList"
                :key="idx"
                class="rounded-xl border transition-all duration-200 overflow-hidden"
                :class="[
                  filesBySlot[idx]
                    ? 'border-emerald-500/70 bg-emerald-50/30 dark:bg-emerald-950/20'
                    : fileErrorsBySlot[idx]
                    ? 'border-destructive/60 bg-destructive/5'
                    : 'border-border bg-card hover:border-primary/40'
                ]"
              >
                <!-- Hidden file input per slot -->
                <input
                  :id="`slot-input-${idx}`"
                  type="file"
                  accept=".pdf,.jpg,.jpeg,.png"
                  @change="(e) => handleSlotFileChange(idx, e)"
                  class="hidden"
                />

                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                  <div class="flex items-start gap-3 flex-1 min-w-0">
                    <div
                      class="size-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5 text-xs font-semibold"
                      :class="[
                        filesBySlot[idx]
                          ? 'bg-emerald-500 text-white'
                          : 'bg-muted text-muted-foreground'
                      ]"
                    >
                      <IconCheck v-if="filesBySlot[idx]" class="size-4" />
                      <span v-else>{{ idx + 1 }}</span>
                    </div>

                    <div class="space-y-1 min-w-0 flex-1">
                      <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-foreground line-clamp-1">
                          Slot {{ idx + 1 }}: {{ syarat }}
                        </span>
                        <Badge
                          v-if="filesBySlot[idx]"
                          variant="secondary"
                          class="bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 text-[10px] px-1.5 py-0 h-4.5"
                        >
                          Siap
                        </Badge>
                      </div>

                      <!-- Detail File Terunggah di Slot Ini -->
                      <div v-if="filesBySlot[idx]" class="flex items-center gap-2 text-xs text-muted-foreground pt-0.5">
                        <IconPaperclip class="size-3.5 text-emerald-600 shrink-0" />
                        <span class="font-medium text-foreground truncate max-w-xs">{{ filesBySlot[idx]?.name }}</span>
                        <span class="shrink-0 text-[11px]">({{ formatFileSize(filesBySlot[idx]?.size || 0) }})</span>
                      </div>
                      <p v-else class="text-xs text-muted-foreground">
                        Belum ada dokumen yang diunggah untuk slot ini.
                      </p>
                    </div>
                  </div>

                  <!-- Aksi Tombol Slot -->
                  <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                    <template v-if="filesBySlot[idx]">
                      <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        class="h-8 text-xs gap-1"
                        @click="triggerSlotInput(idx)"
                      >
                        <IconRefresh class="size-3.5" />
                        Ganti
                      </Button>
                      <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        class="h-8 text-xs text-destructive hover:bg-destructive/10 px-2.5 gap-1"
                        @click="removeSlotFile(idx)"
                      >
                        <IconTrash class="size-3.5" />
                        Hapus
                      </Button>
                    </template>
                    <template v-else>
                      <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        class="h-8 text-xs gap-1.5 border-dashed hover:border-primary"
                        @click="triggerSlotInput(idx)"
                      >
                        <IconUpload class="size-3.5" />
                        Pilih Berkas
                      </Button>
                    </template>
                  </div>
                </div>

                <!-- Error Message Instan per Slot -->
                <div v-if="fileErrorsBySlot[idx]" class="px-4 pb-3 text-xs text-destructive flex items-center gap-1.5">
                  <IconAlertCircle class="size-3.5 shrink-0" />
                  <span>{{ fileErrorsBySlot[idx] }}</span>
                </div>
              </div>
            </div>

            <!-- Tampilan Fallback Jika Layanan Belum Dipilih / Tanpa Syarat Khusus -->
            <div v-else class="rounded-xl border border-dashed p-6 text-center bg-muted/20">
              <input
                id="slot-input-0"
                type="file"
                accept=".pdf,.jpg,.jpeg,.png"
                @change="(e) => handleSlotFileChange(0, e)"
                class="hidden"
              />
              <div class="flex flex-col items-center justify-center gap-2">
                <div class="size-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                  <IconUpload class="size-5" />
                </div>
                <div>
                  <p class="font-medium text-sm text-foreground">
                    {{ form.layanan_id ? 'Unggah Dokumen Pendukung' : 'Pilih jenis layanan di atas terlebih dahulu' }}
                  </p>
                  <p class="text-xs text-muted-foreground mt-0.5">
                    Slot unggah dokumen persyaratan akan disesuaikan secara otomatis.
                  </p>
                </div>
                <Button
                  v-if="form.layanan_id && !filesBySlot[0]"
                  type="button"
                  variant="outline"
                  size="sm"
                  class="mt-2"
                  @click="triggerSlotInput(0)"
                >
                  Pilih Berkas (.pdf, .jpg, .png)
                </Button>

                <!-- Status file di slot fallback -->
                <div v-if="filesBySlot[0]" class="flex items-center gap-3 mt-3 p-2.5 rounded-lg border border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 text-xs">
                  <IconPaperclip class="size-4 text-emerald-600" />
                  <span class="font-medium text-foreground">{{ filesBySlot[0]?.name }}</span>
                  <span class="text-muted-foreground">({{ formatFileSize(filesBySlot[0]?.size || 0) }})</span>
                  <Button type="button" variant="ghost" size="sm" class="h-6 text-destructive px-1.5 text-xs" @click="removeSlotFile(0)">Hapus</Button>
                </div>
                <p v-if="fileErrorsBySlot[0]" class="text-xs text-destructive mt-1">{{ fileErrorsBySlot[0] }}</p>
              </div>
            </div>
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
              placeholder="Tambahkan keterangan instansi tujuan surat, keperluan khusus, atau informasi relevan lainnya..."
              class="flex min-h-20 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
            ></textarea>
          </div>

          <!-- Tombol Aksi Form -->
          <div class="flex items-center justify-between pt-4 border-t">
            <Button variant="outline" type="button" as-child>
              <NuxtLink to="/">Batal</NuxtLink>
            </Button>
            <Button type="submit" class="gap-2 min-w-36">
              <IconEye class="size-4" />
              <span>Tinjau & Kirim Pengajuan</span>
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <!-- Modal Ringkasan Data (Review Dialog) Sebelum Submit (Lazy Loaded) -->
    <LazyDialog v-if="isReviewDialogOpen" :open="isReviewDialogOpen" @update:open="isReviewDialogOpen = $event">
      <DialogContent class="sm:max-w-lg max-h-[85vh] p-5 sm:p-6 flex flex-col gap-3">
        <DialogHeader class="shrink-0 space-y-1">
          <DialogTitle class="text-base sm:text-lg font-bold flex items-center gap-2">
            <IconFileCheck class="size-5 text-primary" />
            Konfirmasi Data Pengajuan
          </DialogTitle>
          <DialogDescription class="text-xs text-muted-foreground">
            Mohon periksa kembali kelengkapan identitas dan berkas lampiran sebelum berkas diproses ke bagian akademik.
          </DialogDescription>
        </DialogHeader>

        <div class="space-y-3 py-1 text-sm overflow-y-auto pr-1.5 flex-1 min-h-0">
          <!-- Ringkasan Identitas -->
          <div class="rounded-lg border bg-muted/40 p-3 space-y-1.5">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground border-b pb-1">
              Identitas Pemohon
            </div>
            <div class="grid grid-cols-3 gap-y-1 gap-x-2 text-xs">
              <span class="text-muted-foreground">Nama:</span>
              <span class="col-span-2 font-medium text-foreground truncate">{{ form.nama_mahasiswa }}</span>
              <span class="text-muted-foreground">NIM:</span>
              <span class="col-span-2 font-mono font-medium text-foreground">{{ form.nim }}</span>
              <span class="text-muted-foreground">Layanan:</span>
              <span class="col-span-2 font-medium text-primary">{{ selectedLayanan?.nama_layanan || '-' }}</span>
              <span class="text-muted-foreground">WhatsApp:</span>
              <span class="col-span-2 font-medium text-foreground">{{ form.no_hp }}</span>
              <span class="text-muted-foreground">Email:</span>
              <span class="col-span-2 font-medium text-foreground truncate">{{ form.email }}</span>
            </div>
          </div>

          <!-- Ringkasan Berkas Terlampir -->
          <div class="rounded-lg border p-3 space-y-2">
            <div class="flex items-center justify-between border-b pb-1">
              <span class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
                Berkas Terlampir
              </span>
              <Badge variant="secondary" class="text-[10px] px-1.5 py-0 h-5">
                {{ uploadedFilesSummary.length }} Dokumen
              </Badge>
            </div>

            <div v-if="uploadedFilesSummary.length > 0" class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
              <div
                v-for="item in uploadedFilesSummary"
                :key="item.slotIndex"
                class="flex items-center justify-between text-xs p-2 rounded-md bg-muted/30"
              >
                <div class="flex items-center gap-2 min-w-0 flex-1">
                  <IconFile class="size-3.5 text-primary shrink-0" />
                  <div class="min-w-0">
                    <p class="font-medium text-foreground truncate">{{ item.file.name }}</p>
                    <p class="text-[10px] text-muted-foreground truncate">{{ item.requirementName }}</p>
                  </div>
                </div>
                <span class="text-[11px] text-muted-foreground shrink-0 pl-2">
                  {{ formatFileSize(item.file.size) }}
                </span>
              </div>
            </div>
            <div v-else class="text-xs text-amber-600 dark:text-amber-400 py-1 flex items-center gap-1.5">
              <IconAlertTriangle class="size-4 shrink-0" />
              <span>Tidak ada berkas yang dilampirkan. Pastikan berkas pendukung tidak diwajibkan oleh fakultas.</span>
            </div>
          </div>

          <!-- Catatan Tambahan Jika Ada -->
          <div v-if="form.catatan" class="rounded-lg border p-2.5 text-xs bg-muted/20">
            <span class="font-semibold text-muted-foreground">Catatan Tambahan:</span>
            <p class="mt-0.5 text-foreground italic">{{ form.catatan }}</p>
          </div>
        </div>

        <DialogFooter class="shrink-0 pt-2 border-t flex flex-col-reverse sm:flex-row gap-2 sm:gap-2">
          <Button variant="outline" size="sm" type="button" @click="isReviewDialogOpen = false">
            Perbaiki Data
          </Button>
          <Button
            type="button"
            size="sm"
            class="gap-1.5 bg-primary text-primary-foreground hover:bg-primary/90"
            :disabled="isSubmitting"
            @click="confirmAndSubmit"
          >
            <span v-if="isSubmitting" class="size-3.5 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></span>
            <IconSend v-else class="size-3.5" />
            <span>{{ isSubmitting ? 'Mengirim...' : 'Kirim Sekarang' }}</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </LazyDialog>
  </div>
</template>
