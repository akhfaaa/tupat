<?php

namespace App\Http\Controllers;

use App\Models\MitraDudi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MitraDudiController extends Controller
{
    public function index()
    {
        $mitraDudis = MitraDudi::latest()->paginate(10);

        return Inertia::render('Master/MitraDudi/Index', [
            'mitraDudis' => $mitraDudis,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_usaha' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nama_pimpinan' => 'nullable|string|max:255',
            'kontak_person' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:mitra_dudis,email',
        ]);

        MitraDudi::create($validated);

        return redirect()->back()->with('success', 'Data Mitra DUDI berhasil ditambahkan.');
    }

    public function update(Request $request, MitraDudi $mitraDudi)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_usaha' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nama_pimpinan' => 'nullable|string|max:255',
            'kontak_person' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:mitra_dudis,email,' . $mitraDudi->id,
            'status_aktif' => 'required|boolean',
        ]);

        $mitraDudi->update($validated);

        return redirect()->back()->with('success', 'Data Mitra DUDI berhasil diperbarui.');
    }

    public function destroy(MitraDudi $mitraDudi)
    {
        $mitraDudi->delete();

        return redirect()->back()->with('success', 'Data Mitra DUDI berhasil dihapus.');
    }
}
