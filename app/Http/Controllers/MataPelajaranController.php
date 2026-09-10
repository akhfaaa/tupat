<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Inertia\Inertia;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/Mapel/Index', [
            'mapels' => MataPelajaran::orderBy('kelompok', 'asc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajarans,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'kelompok' => 'required|in:A,B,C',
        ]);

        MataPelajaran::create($validated);
        return redirect()->back();
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajarans,kode_mapel,' . $mataPelajaran->id,
            'nama_mapel' => 'required|string|max:255',
            'kelompok' => 'required|in:A,B,C',
        ]);

        $mataPelajaran->update($validated);
        return redirect()->back();
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();
        return redirect()->back();
    }
}
