<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra_dudis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('bidang_usaha');
            $table->text('alamat');
            $table->string('nama_pimpinan')->nullable();
            $table->string('kontak_person'); // Nama HR atau Mentor penanggung jawab
            $table->string('telepon', 20)->nullable();
            $table->string('email')->unique()->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_dudis');
    }
};
