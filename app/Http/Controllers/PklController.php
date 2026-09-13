<?php

namespace App\Http\Controllers;

use App\Models\Pkl;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\MitraDudi;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PklController extends Controller
{
    public function index()
    {
        return Inertia::render('Akademik/Pkl/Index', [
            'pkls' => Pkl::with(['siswa', 'guru', 'mitraDudi'])->latest()->get(),
            'siswas' => Siswa::orderBy('nama_lengkap', 'asc')->get(),
            'gurus' => Guru::orderBy('nama_lengkap', 'asc')->get(),
            'mitraDudis' => MitraDudi::where('status_aktif', true)->orderBy('nama_perusahaan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'guru_id' => 'nullable|exists:gurus,id',
            'mitra_dudi_id' => 'nullable|exists:mitra_dudis,id',
            'nama_perusahaan' => 'required|string|max:255',
            'divisi_pekerjaan' => 'nullable|string|max:255',
            'alamat_perusahaan' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:Pengajuan,Aktif,Selesai',
        ]);

        Pkl::create($validated);
        return redirect()->back();
    }

    public function update(Request $request, Pkl $pkl)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'guru_id' => 'nullable|exists:gurus,id',
            'mitra_dudi_id' => 'nullable|exists:mitra_dudis,id',
            'nama_perusahaan' => 'required|string|max:255',
            'divisi_pekerjaan' => 'nullable|string|max:255',
            'alamat_perusahaan' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:Pengajuan,Aktif,Selesai',
        ]);

        $pkl->update($validated);
        return redirect()->back();
    }

    public function destroy(Pkl $pkl)
    {
        $pkl->delete();
        return redirect()->back();
    }
}
