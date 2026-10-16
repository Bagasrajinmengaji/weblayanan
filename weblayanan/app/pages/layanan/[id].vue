<script setup lang="ts">
const route = useRoute()
await navigateTo(`/pengajuan?layanan_id=${route.params.id}`, { replace: true })
</script>

<template>
  <div class="p-8 text-center text-muted-foreground">
    <p>Mengalihkan ke formulir pengajuan layanan...</p>
  </div>
</template>
