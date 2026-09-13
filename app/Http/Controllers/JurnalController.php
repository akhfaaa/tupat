<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Absensi;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $rombelId = $request->input('rombel_id');
        
        // Ambil data siswa hanya jika kelas (rombel) sudah dipilih
        $siswas = [];
        if ($rombelId) {
            $siswas = Siswa::where('rombel_id', $rombelId)->orderBy('nama_lengkap', 'asc')->get();
        }

        return Inertia::render('Guru/Jurnal/Index', [
            'rombels' => Rombel::orderBy('nama_rombel', 'asc')->get(),
            'mapels' => MataPelajaran::orderBy('nama_mapel', 'asc')->get(),
            'siswas' => $siswas,
            'filters' => $request->only(['rombel_id']),
            // Menampilkan riwayat jurnal terakhir
            'riwayat_jurnals' => Jurnal::with(['rombel', 'mataPelajaran'])->latest()->take(10)->get(), 
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'tanggal' => 'required|date',
            'jam_ke' => 'required|string|max:50',
            'materi_pembelajaran' => 'required|string',
            'catatan_kelas' => 'nullable|string',
            'absensi' => 'required|array',
            'absensi.*.siswa_id' => 'required|exists:siswas,id',
            'absensi.*.status' => 'required|in:Hadir,Sakit,Izin,Alpa',
            'absensi.*.keterangan' => 'nullable|string',
        ]);

        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        abort_unless($tahunAktif, 422, 'Belum ada tahun ajaran aktif.');

        // Semua jurnal harus memiliki profil guru yang valid agar foreign key tetap konsisten.
        $guru = Guru::where('user_id', Auth::id())->first();
        abort_unless($guru, 403, 'Profil guru belum terhubung ke akun ini.');

        /** @var User $user */
        $user = Auth::user();
        if (! $user->hasSystemRole('super-admin')) {
            $teachesClass = Jadwal::where('guru_id', $guru->id)
                ->where('rombel_id', $request->rombel_id)
                ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->exists();

            abort_unless($teachesClass, 403, 'Guru tidak memiliki jadwal untuk kelas dan mata pelajaran ini.');
        }

        $studentIds = Siswa::where('rombel_id', $request->rombel_id)
            ->whereIn('id', collect($request->absensi)->pluck('siswa_id'))
            ->pluck('id');
        abort_unless($studentIds->count() === count($request->absensi), 422, 'Terdapat siswa yang bukan anggota rombel tersebut.');

        $duplicate = Jurnal::where('guru_id', $guru->id)
            ->where('rombel_id', $validated['rombel_id'])
            ->where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->where('tanggal', $validated['tanggal'])
            ->where('jam_ke', $validated['jam_ke'])
            ->exists();
        abort_if($duplicate, 422, 'Jurnal untuk jadwal tersebut sudah tersedia.');

        DB::transaction(function () use ($validated, $guru, $tahunAktif) {
            $jurnal = Jurnal::create([
                'guru_id' => $guru->id,
                'rombel_id' => $validated['rombel_id'],
                'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
                'tahun_ajaran_id' => $tahunAktif->id,
                'tanggal' => $validated['tanggal'],
                'jam_ke' => $validated['jam_ke'],
                'materi_pembelajaran' => $validated['materi_pembelajaran'],
                'catatan_kelas' => $validated['catatan_kelas'] ?? null,
            ]);

            foreach ($validated['absensi'] as $ab) {
                Absensi::create([
                    'jurnal_id' => $jurnal->id,
                    'siswa_id' => $ab['siswa_id'],
                    'status' => $ab['status'],
                    'keterangan' => $ab['keterangan'] ?? null,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Jurnal dan Presensi berhasil disimpan.');
    }
}