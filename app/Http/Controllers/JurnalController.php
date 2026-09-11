<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Absensi;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Guru;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'tanggal' => 'required|date',
            'jam_ke' => 'required|string|max:50',
            'materi_pembelajaran' => 'required|string',
            'catatan_kelas' => 'nullable|string',
            'absensi' => 'required|array',
        ]);

        // Cari ID Guru berdasarkan user yang login (fallback ke ID 1 jika menggunakan akun super-admin)
        $guru = Guru::where('user_id', Auth::id())->first();
        $guruId = $guru ? $guru->id : 1; 

        // 1. Simpan Data Jurnal
        $jurnal = Jurnal::create([
            'guru_id' => $guruId,
            'rombel_id' => $request->rombel_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'tanggal' => $request->tanggal,
            'jam_ke' => $request->jam_ke,
            'materi_pembelajaran' => $request->materi_pembelajaran,
            'catatan_kelas' => $request->catatan_kelas,
        ]);

        // 2. Simpan Data Absensi Siswa
        foreach ($request->absensi as $ab) {
            Absensi::create([
                'jurnal_id' => $jurnal->id,
                'siswa_id' => $ab['siswa_id'],
                'status' => $ab['status'],
                'keterangan' => $ab['keterangan'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Jurnal dan Presensi berhasil disimpan.');
    }
}