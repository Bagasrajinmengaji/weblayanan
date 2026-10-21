<script setup lang="ts">
definePageMeta({
  middleware: ["auth"],
})

navigateTo("/", { replace: true })
</script>

<template>
  <div></div>
</template>
