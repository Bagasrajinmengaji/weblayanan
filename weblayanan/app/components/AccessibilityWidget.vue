<script setup lang="ts">
import {
  IconAccessible,
  IconAdjustments,
  IconCheck,
  IconLetterCase,
  IconRefresh,
  IconRotate,
  IconX,
} from "@tabler/icons-vue"

import { Button } from "~/components/ui/button"

const isOpen = ref(false)

interface A11ySettings {
  fontSize: "normal" | "large" | "xlarge"
  highContrast: boolean
  underlineLinks: boolean
  readableFont: boolean
  reduceMotion: boolean
}

const defaultSettings: A11ySettings = {
  fontSize: "normal",
  highContrast: false,
  underlineLinks: false,
  readableFont: false,
  reduceMotion: false,
}

const settings = reactive<A11ySettings>({ ...defaultSettings })

function applySettings() {
  if (import.meta.server) return

  const html = document.documentElement

  // Font size
  html.classList.remove("a11y-font-large", "a11y-font-xlarge")
  if (settings.fontSize === "large") {
    html.classList.add("a11y-font-large")
  } else if (settings.fontSize === "xlarge") {
    html.classList.add("a11y-font-xlarge")
  }

  // Contrast
  if (settings.highContrast) {
    html.classList.add("a11y-high-contrast")
  } else {
    html.classList.remove("a11y-high-contrast")
  }

  // Underline
  if (settings.underlineLinks) {
    html.classList.add("a11y-underline-links")
  } else {
    html.classList.remove("a11y-underline-links")
  }

  // Readable font
  if (settings.readableFont) {
    html.classList.add("a11y-readable-font")
  } else {
    html.classList.remove("a11y-readable-font")
  }

  // Reduce motion
  if (settings.reduceMotion) {
    html.classList.add("a11y-reduce-motion")
  } else {
    html.classList.remove("a11y-reduce-motion")
  }

  // Save to localStorage
  try {
    localStorage.setItem("portal_a11y_settings", JSON.stringify(settings))
  } catch (e) {
    // ignore
  }
}

function resetSettings() {
  Object.assign(settings, defaultSettings)
  applySettings()
}

onMounted(() => {
  try {
    const saved = localStorage.getItem("portal_a11y_settings")
    if (saved) {
      Object.assign(settings, JSON.parse(saved))
    }
  } catch (e) {
    // ignore
  }
  applySettings()

  // Keyboard shortcut: Alt + A to toggle accessibility
  window.addEventListener("keydown", handleKeydown)
})

onUnmounted(() => {
  if (import.meta.client) {
    window.removeEventListener("keydown", handleKeydown)
  }
})

function handleKeydown(e: KeyboardEvent) {
  if (e.altKey && (e.key === "a" || e.key === "A")) {
    e.preventDefault()
    isOpen.value = !isOpen.value
  } else if (e.key === "Escape" && isOpen.value) {
    isOpen.value = false
  }
}
</script>

<template>
  <div class="fixed bottom-5 right-5 z-50 select-none">
    <!-- Floating Circular Button (Sesuai Tombol Biru di Pojok Kanan Bawah Screenshot) -->
    <button
      type="button"
      id="a11y-widget-button"
      class="size-12 rounded-full bg-[#0062cc] hover:bg-[#0051a8] active:bg-[#003d80] text-white shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center cursor-pointer focus:outline-hidden focus-visible:ring-4 focus-visible:ring-blue-400"
      :aria-expanded="isOpen"
      aria-haspopup="dialog"
      aria-label="Buka Menu Aksesibilitas (Pintasan Alt+A)"
      title="Aksesibilitas / Accessibility Tools (Alt + A)"
      @click="isOpen = !isOpen"
    >
      <!-- Universal Accessibility Pictogram -->
      <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="4.5" r="2.5" fill="currentColor" stroke="none" />
        <path d="M4 8.5h16" />
        <path d="M12 8.5v6" />
        <path d="M8 20l4-5.5 4 5.5" />
      </svg>
    </button>

    <!-- Modal Panel Pengaturan Aksesibilitas -->
    <div
      v-if="isOpen"
      role="dialog"
      aria-modal="true"
      aria-labelledby="a11y-title"
      class="fixed bottom-20 right-5 w-82.5 sm:w-90 rounded-2xl border border-border bg-card/95 backdrop-blur-md shadow-2xl p-5 text-foreground transition-all duration-200 animate-in fade-in zoom-in-95"
    >
      <!-- Header -->
      <div class="flex items-center justify-between pb-3 border-b border-border/80">
        <div class="flex items-center gap-2">
          <div class="size-8 rounded-lg bg-[#0062cc] text-white flex items-center justify-center">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="4.5" r="2.5" fill="currentColor" stroke="none" />
              <path d="M4 8.5h16" />
              <path d="M12 8.5v6" />
              <path d="M8 20l4-5.5 4 5.5" />
            </svg>
          </div>
          <div>
            <h3 id="a11y-title" class="font-bold text-sm text-foreground">
              Aksesibilitas
            </h3>
            <p class="text-[11px] text-muted-foreground">Kustomisasi tampilan & kenyamanan baca</p>
          </div>
        </div>

        <button
          type="button"
          class="size-7 rounded-md hover:bg-muted text-muted-foreground hover:text-foreground flex items-center justify-center cursor-pointer transition-colors"
          aria-label="Tutup panel aksesibilitas"
          @click="isOpen = false"
        >
          <IconX class="size-4" />
        </button>
      </div>

      <!-- Controls -->
      <div class="py-3.5 space-y-4 text-xs">
        <!-- 1. Text Size Scaling -->
        <div>
          <label class="font-medium text-foreground block mb-1.5">
            Ukuran Teks
          </label>
          <div class="grid grid-cols-3 gap-1.5 p-1 rounded-lg bg-muted/60 border border-border/60">
            <button
              type="button"
              class="py-1.5 text-xs rounded-md font-medium transition-all"
              :class="settings.fontSize === 'normal' ? 'bg-background shadow-xs text-foreground font-bold' : 'text-muted-foreground hover:text-foreground'"
              @click="settings.fontSize = 'normal'; applySettings()"
            >
              Normal (100%)
            </button>
            <button
              type="button"
              class="py-1.5 text-xs rounded-md font-medium transition-all"
              :class="settings.fontSize === 'large' ? 'bg-background shadow-xs text-foreground font-bold' : 'text-muted-foreground hover:text-foreground'"
              @click="settings.fontSize = 'large'; applySettings()"
            >
              Besar (115%)
            </button>
            <button
              type="button"
              class="py-1.5 text-xs rounded-md font-medium transition-all"
              :class="settings.fontSize === 'xlarge' ? 'bg-background shadow-xs text-foreground font-bold' : 'text-muted-foreground hover:text-foreground'"
              @click="settings.fontSize = 'xlarge'; applySettings()"
            >
              Ekstra (130%)
            </button>
          </div>
        </div>

        <!-- 2. Kontras Tinggi -->
        <div class="flex items-center justify-between">
          <div>
            <span class="font-medium text-foreground block">Kontras Tinggi</span>
            <span class="text-[10px] text-muted-foreground">Perjelas teks dan batas elemen</span>
          </div>
          <button
            type="button"
            role="switch"
            :aria-checked="settings.highContrast"
            class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
            :class="settings.highContrast ? 'bg-[#0062cc]' : 'bg-muted'"
            @click="settings.highContrast = !settings.highContrast; applySettings()"
          >
            <span
              class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
              :class="settings.highContrast ? 'translate-x-5' : 'translate-x-0'"
            />
          </button>
        </div>

        <!-- 3. Garis Bawahi Tautan -->
        <div class="flex items-center justify-between">
          <div>
            <span class="font-medium text-foreground block">Garis Bawahi Tautan</span>
            <span class="text-[10px] text-muted-foreground">Beri garis bawah pada semua tautan link</span>
          </div>
          <button
            type="button"
            role="switch"
            :aria-checked="settings.underlineLinks"
            class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
            :class="settings.underlineLinks ? 'bg-[#0062cc]' : 'bg-muted'"
            @click="settings.underlineLinks = !settings.underlineLinks; applySettings()"
          >
            <span
              class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
              :class="settings.underlineLinks ? 'translate-x-5' : 'translate-x-0'"
            />
          </button>
        </div>

        <!-- 4. Huruf Ramah Baca -->
        <div class="flex items-center justify-between">
          <div>
            <span class="font-medium text-foreground block">Font Ramah Baca</span>
            <span class="text-[10px] text-muted-foreground">Gunakan font standar dengan spasi jelas</span>
          </div>
          <button
            type="button"
            role="switch"
            :aria-checked="settings.readableFont"
            class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
            :class="settings.readableFont ? 'bg-[#0062cc]' : 'bg-muted'"
            @click="settings.readableFont = !settings.readableFont; applySettings()"
          >
            <span
              class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
              :class="settings.readableFont ? 'translate-x-5' : 'translate-x-0'"
            />
          </button>
        </div>

        <!-- 5. Kurangi Animasi -->
        <div class="flex items-center justify-between">
          <div>
            <span class="font-medium text-foreground block">Kurangi Gerakan</span>
            <span class="text-[10px] text-muted-foreground">Hentikan efek transisi dan animasi berkedip</span>
          </div>
          <button
            type="button"
            role="switch"
            :aria-checked="settings.reduceMotion"
            class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
            :class="settings.reduceMotion ? 'bg-[#0062cc]' : 'bg-muted'"
            @click="settings.reduceMotion = !settings.reduceMotion; applySettings()"
          >
            <span
              class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
              :class="settings.reduceMotion ? 'translate-x-5' : 'translate-x-0'"
            />
          </button>
        </div>
      </div>

      <!-- Footer Reset -->
      <div class="pt-3 border-t border-border/80 flex items-center justify-between">
        <button
          type="button"
          class="text-xs text-muted-foreground hover:text-foreground flex items-center gap-1 transition-colors cursor-pointer"
          @click="resetSettings"
        >
          <IconRotate class="size-3.5" />
          <span>Atur Ulang Pengaturan</span>
        </button>

        <span class="text-[10px] text-muted-foreground font-mono">Alt + A</span>
      </div>
    </div>
  </div>
</template>
