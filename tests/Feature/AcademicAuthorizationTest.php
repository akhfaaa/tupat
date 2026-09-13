<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AcademicAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_cannot_submit_a_journal_for_an_unassigned_schedule(): void
    {
        Role::create(['name' => 'guru']);
        $user = User::factory()->create();
        $user->assignRole('guru');

        $guru = Guru::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Guru Test',
            'jenis_kelamin' => 'L',
        ]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $period = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'is_active' => true]);
        $rombel = Rombel::create([
            'tahun_ajaran_id' => $period->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'X',
            'nama_rombel' => 'X RPL 1',
        ]);
        $mapel = MataPelajaran::create(['kode_mapel' => 'PWEB', 'nama_mapel' => 'Pemrograman Web', 'kelompok' => 'C']);
        $student = Siswa::create([
            'jurusan_id' => $jurusan->id,
            'rombel_id' => $rombel->id,
            'nisn' => '1234567890',
            'nama_lengkap' => 'Siswa Test',
            'jenis_kelamin' => 'P',
        ]);

        $response = $this->actingAs($user)->post(route('jurnal.store'), [
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $mapel->id,
            'tanggal' => '2026-09-12',
            'jam_ke' => '1-2',
            'materi_pembelajaran' => 'Materi test',
            'absensi' => [['siswa_id' => $student->id, 'status' => 'Hadir']],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('jurnals', ['guru_id' => $guru->id]);
    }

    public function test_schedule_creation_requires_an_active_period(): void
    {
        Role::create(['name' => 'tu']);
        $user = User::factory()->create();
        $user->assignRole('tu');

        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer dan Jaringan']);
        $period = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'is_active' => false]);
        $rombel = Rombel::create([
            'tahun_ajaran_id' => $period->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'X',
            'nama_rombel' => 'X TKJ 1',
        ]);
        $guru = Guru::create(['nama_lengkap' => 'Guru Test', 'jenis_kelamin' => 'L']);
        $mapel = MataPelajaran::create(['kode_mapel' => 'JAR', 'nama_mapel' => 'Jaringan', 'kelompok' => 'C']);

        $response = $this->actingAs($user)->post(route('jadwal.store'), [
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $guru->id,
            'hari' => 'Senin',
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:00',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('jadwals', 0);
    }

    public function test_master_data_jurusan_is_available_to_tu_but_not_guru(): void
    {
        Role::create(['name' => 'tu']);
        Role::create(['name' => 'guru']);

        $tu = User::factory()->create();
        $tu->assignRole('tu');
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($tu)->get(route('jurusan.index'))->assertOk();
        $this->actingAs($guru)->get(route('jurusan.index'))->assertForbidden();
    }

    public function test_role_dashboards_render_without_profile_data(): void
    {
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'guru']);
        Role::create(['name' => 'orang-tua']);

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $teacher = User::factory()->create();
        $teacher->assignRole('guru');
        $parent = User::factory()->create();
        $parent->assignRole('orang-tua');

        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $this->actingAs($teacher)->get(route('dashboard'))->assertOk();
        $this->actingAs($parent)->get(route('dashboard'))->assertOk();
    }
}