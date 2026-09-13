<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnals', function (Blueprint $table) {
            if (! Schema::hasColumn('jurnals', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (! Schema::hasColumn('jurnals', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            $table->unique(
                ['guru_id', 'rombel_id', 'mata_pelajaran_id', 'tanggal', 'jam_ke'],
                'jurnals_schedule_entry_unique'
            );
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->unique(['jurnal_id', 'siswa_id'], 'absensis_jurnal_siswa_unique');
        });

        Schema::table('nilais', function (Blueprint $table) {
            $table->unique(
                ['siswa_id', 'mata_pelajaran_id', 'tahun_ajaran_id'],
                'nilais_student_subject_period_unique'
            );
        });

        Schema::table('pkls', function (Blueprint $table) {
            $table->foreignId('mitra_dudi_id')
                ->nullable()
                ->after('guru_id')
                ->constrained('mitra_dudis')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pkls', function (Blueprint $table) {
            $table->dropForeign(['mitra_dudi_id']);
            $table->dropColumn('mitra_dudi_id');
        });

        Schema::table('nilais', function (Blueprint $table) {
            $table->dropUnique('nilais_student_subject_period_unique');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('absensis_jurnal_siswa_unique');
        });

        Schema::table('jurnals', function (Blueprint $table) {
            $table->dropUnique('jurnals_schedule_entry_unique');
            if (Schema::hasColumn('jurnals', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('jurnals', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};