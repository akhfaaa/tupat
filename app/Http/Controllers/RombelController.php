<?php

namespace App\Http\Controllers;

use App\Models\Rombel;
use App\Models\TahunAjaran;
use App\Models\Jurusan;
use App\Models\Guru;
use Inertia\Inertia;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index()
    {
        // Mengambil rombel beserta data relasinya (Eager Loading untuk performa)
        $rombels = Rombel::with(['tahunAjaran', 'jurusan', 'waliKelas'])->latest()->get();

        return Inertia::render('Master/Rombel/Index', [
            'rombels' => $rombels,
            // Data untuk dropdown di Modal
            'tahun_ajarans' => TahunAjaran::orderBy('tahun', 'desc')->get(),
            'jurusans' => Jurusan::orderBy('nama_jurusan', 'asc')->get(),
            'gurus' => Guru::orderBy('nama_lengkap', 'asc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jurusan_id' => 'required|exists:jurusans,id',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama_rombel' => 'required|string|max:50',
        ]);

        Rombel::create($validated);
        return redirect()->back();
    }

    public function update(Request $request, Rombel $rombel)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jurusan_id' => 'required|exists:jurusans,id',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama_rombel' => 'required|string|max:50',
        ]);

        $rombel->update($validated);
        return redirect()->back();
    }

    public function destroy(Rombel $rombel)
    {
        $rombel->delete();
        return redirect()->back();
    }
}
