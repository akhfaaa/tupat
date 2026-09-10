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
        Schema::create('pkls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            // Guru pembimbing dari pihak sekolah
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();

            $table->string('nama_perusahaan');
            $table->string('divisi_pekerjaan')->nullable();
            $table->text('alamat_perusahaan')->nullable();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['Pengajuan', 'Aktif', 'Selesai'])->default('Pengajuan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkls');
    }
};
