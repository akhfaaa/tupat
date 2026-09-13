<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkl_logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_id')->constrained('pkls')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('kegiatan');
            $table->text('hasil')->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();
            $table->unique(['pkl_id', 'tanggal']);
        });

        Schema::create('pkl_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_id')->constrained('pkls')->cascadeOnDelete();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('technical_score')->nullable();
            $table->unsignedTinyInteger('discipline_score')->nullable();
            $table->unsignedTinyInteger('communication_score')->nullable();
            $table->unsignedTinyInteger('teamwork_score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();
            $table->unique('pkl_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_assessments');
        Schema::dropIfExists('pkl_logbooks');
    }
};