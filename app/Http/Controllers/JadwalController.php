<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Inertia\Inertia;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        return Inertia::render('Akademik/Jadwal/Index', [
            'jadwals' => Jadwal::with(['rombel', 'mataPelajaran', 'guru'])->latest()->get(),
            'rombels' => Rombel::orderBy('nama_rombel', 'asc')->get(),
            'mapels' => MataPelajaran::orderBy('nama_mapel', 'asc')->get(),
            'gurus' => Guru::orderBy('nama_lengkap', 'asc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'guru_id' => 'required|exists:gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        Jadwal::create($validated);
        return redirect()->back();
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'guru_id' => 'required|exists:gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal->update($validated);
        return redirect()->back();
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->back();
    }
}
