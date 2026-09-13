<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\TahunAjaran;
use Inertia\Inertia;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        return Inertia::render('Akademik/Jadwal/Index', [
            'jadwals' => Jadwal::with(['rombel', 'mataPelajaran', 'guru', 'tahunAjaran'])->latest()->get(),
            'rombels' => Rombel::orderBy('nama_rombel', 'asc')->get(),
            'mapels' => MataPelajaran::orderBy('nama_mapel', 'asc')->get(),
            'gurus' => Guru::orderBy('nama_lengkap', 'asc')->get(),
            'tahunAjarans' => TahunAjaran::orderByDesc('is_active')->latest()->get(),
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

        $validated['tahun_ajaran_id'] = $this->activePeriod()->id;
        $this->ensureNoConflict($validated);

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

        $validated['tahun_ajaran_id'] = $this->activePeriod()->id;
        $this->ensureNoConflict($validated, $jadwal->id);

        $jadwal->update($validated);
        return redirect()->back();
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->back();
    }

    private function activePeriod(): TahunAjaran
    {
        return TahunAjaran::where('is_active', true)->firstOrFail();
    }

    private function ensureNoConflict(array $data, ?int $exceptId = null): void
    {
        $query = Jadwal::where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('hari', $data['hari'])
            ->where(function ($query) use ($data) {
                $query->where('jam_mulai', '<', $data['jam_selesai'])
                    ->where('jam_selesai', '>', $data['jam_mulai']);
            })
            ->where(function ($query) use ($data) {
                $query->where('guru_id', $data['guru_id'])
                    ->orWhere('rombel_id', $data['rombel_id']);
            });

        if ($exceptId) {
            $query->whereKeyNot($exceptId);
        }

        abort_if($query->exists(), 422, 'Jadwal guru atau rombel bentrok pada waktu tersebut.');
    }
}
