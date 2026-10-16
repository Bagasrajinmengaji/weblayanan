<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pelacakan')->unique();
            $table->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();
            $table->string('nama_mahasiswa');
            $table->string('nim');
            $table->string('email');
            $table->string('no_hp');
            $table->json('berkas_persyaratan')->nullable();
            $table->enum('status', ['Pending', 'Diproses', 'Perlu Perbaikan', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->text('catatan_revisi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
