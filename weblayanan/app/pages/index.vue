<template>
  <div
    style="
      font-family: Arial, sans-serif;
      padding: 40px;
      text-align: center;
      max-width: 600px;
      margin: 0 auto;
    "
  >
    <h1>Tes Koneksi Decoupled Project</h1>

    <!-- Status Loading -->
    <div v-if="pending" style="margin-top: 20px">
      <p style="color: #666">Sedang menghubungkan ke server Laravel...</p>
    </div>

    <!-- Status Error -->
    <div
      v-else-if="error"
      style="
        background: #ffebee;
        border: 1px solid #ef5350;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
      "
    >
      <h3 style="color: #c62828; margin-top: 0">
        ❌ Gagal Terhubung ke Backend
      </h3>
      <p style="color: #b71c1c; margin-bottom: 0">{{ error.message }}</p>
    </div>

    <!-- Status Sukses -->
    <div
      v-else
      style="
        background: #e8f5e9;
        border: 1px solid #66bb6a;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
      "
    >
      <h3 style="color: #2e7d32; margin-top: 0">
        ✅ Status: {{ data?.status }}
      </h3>
      <p style="font-size: 18px; color: #1b5e20">{{ data?.message }}</p>
      <small style="color: #388e3c"
        >Waktu Server Backend: {{ data?.timestamp }}</small
      >
    </div>
  </div>
</template>

<script setup>
const { data, pending, error } = await useFetch(
  "http://backend_api.test/api/ping",
);
</script>
