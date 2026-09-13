<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukk_schemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->string('name');
            $table->string('certification_body')->nullable();
            $table->json('competency_units')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->timestamps();
        });

        Schema::create('ukk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained('ukk_schemes')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('assessor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('score')->nullable();
            $table->enum('status', ['belum_dinilai', 'kompeten', 'belum_kompeten'])->default('belum_dinilai');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['scheme_id', 'siswa_id']);
        });

        Schema::create('discipline_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['pelanggaran', 'prestasi']);
            $table->integer('points');
            $table->string('category');
            $table->text('notes')->nullable();
            $table->date('occurred_at');
            $table->timestamps();
        });

        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('graduation_year', 9);
            $table->enum('bmw_status', ['bekerja', 'melanjutkan', 'wirausaha', 'belum_terlacak'])->default('belum_terlacak');
            $table->string('institution')->nullable();
            $table->string('position')->nullable();
            $table->string('contact')->nullable();
            $table->timestamps();
        });

        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('company');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->date('closing_date')->nullable();
            $table->enum('status', ['draft', 'aktif', 'ditutup'])->default('draft');
            $table->timestamps();
        });

        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->enum('status', ['bekerja', 'melanjutkan', 'wirausaha', 'belum_terlacak']);
            $table->string('institution')->nullable();
            $table->date('reported_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pkl_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_id')->constrained('pkls')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['pkl_id', 'attendance_date']);
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category');
            $table->string('location')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->string('unit', 30)->default('unit');
            $table->enum('condition', ['baik', 'perlu_perbaikan', 'rusak'])->default('baik');
            $table->timestamps();
        });

        Schema::create('inventory_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('borrower_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->date('borrowed_at');
            $table->date('due_at')->nullable();
            $table->date('returned_at')->nullable();
            $table->enum('status', ['dipinjam', 'dikembalikan', 'terlambat'])->default('dipinjam');
            $table->timestamps();
        });

        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('relationship', 30)->default('wali');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['parent_id', 'siswa_id']);
        });

        Schema::create('report_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->enum('component', ['formatif', 'sumatif', 'praktik', 'projek_p5', 'pkl']);
            $table->decimal('score', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['siswa_id', 'mata_pelajaran_id', 'tahun_ajaran_id', 'component'], 'report_component_unique');
        });

        Schema::create('dapodik_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('direction', ['import', 'export']);
            $table->string('file_name');
            $table->enum('status', ['berhasil', 'gagal']);
            $table->json('summary')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dapodik_sync_logs');
        Schema::dropIfExists('report_components');
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('inventory_loans');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('pkl_attendances');
        Schema::dropIfExists('tracer_studies');
        Schema::dropIfExists('job_postings');
        Schema::dropIfExists('alumni_profiles');
        Schema::dropIfExists('discipline_records');
        Schema::dropIfExists('ukk_assessments');
        Schema::dropIfExists('ukk_schemes');
    }
};