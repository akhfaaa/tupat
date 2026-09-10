<?php

namespace App\Http\Controllers;

use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\TahunAjaran;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $rombelId = $request->input('rombel_id');
        $mapelId = $request->input('mata_pelajaran_id');

        $siswas = [];
        if ($rombelId) {
            // Ambil siswa berdasarkan rombel yang dipilih
            $siswas = Siswa::with(['nilais' => function ($query) use ($mapelId) {
                if ($mapelId) {
                    $query->where('mata_pelajaran_id', $mapelId);
                }
            }])->where('rombel_id', $rombelId)->get();
        }

        return Inertia::render('Guru/Penilaian/Index', [
            'rombels' => Rombel::with('tahunAjaran')->orderBy('nama_rombel', 'asc')->get(),
            'mapels' => MataPelajaran::orderBy('nama_mapel', 'asc')->get(),
            'siswas' => $siswas,
            'filters' => $request->only(['rombel_id', 'mata_pelajaran_id']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'nilais' => 'required|array',
            'nilais.*.siswa_id' => 'required|exists:siswas,id',
            'nilais.*.nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilais.*.nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilais.*.nilai_uas' => 'nullable|numeric|min:0|max:100',
        ]);

        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        $tahunAjaranId = $tahunAktif ? $tahunAktif->id : 1;

        foreach ($request->nilais as $data) {
            $tugas = $data['nilai_tugas'] ?? 0;
            $uts = $data['nilai_uts'] ?? 0;
            $uas = $data['nilai_uas'] ?? 0;

            // Formula standar SMK: 30% Tugas + 30% UTS + 40% UAS
            $nilaiAkhir = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);

            Nilai::updateOrCreate(
                [
                    'siswa_id' => $data['siswa_id'],
                    'mata_pelajaran_id' => $request->mata_pelajaran_id,
                    'tahun_ajaran_id' => $tahunAjaranId,
                ],
                [
                    'nilai_tugas' => $tugas,
                    'nilai_uts' => $uts,
                    'nilai_uas' => $uas,
                    'nilai_akhir' => $nilaiAkhir,
                ]
            );
        }

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }
}
