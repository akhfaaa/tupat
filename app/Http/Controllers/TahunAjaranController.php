<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/TahunAjaran/Index', [
            'tahun_ajarans' => TahunAjaran::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|string|max:20', // Contoh: 2025/2026
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'boolean',
        ]);

        // Jika user mencentang "Aktif", nonaktifkan semua tahun ajaran lain terlebih dahulu
        if ($validated['is_active'] ?? false) {
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        }

        TahunAjaran::create($validated);
        return redirect()->back();
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'tahun' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'boolean',
        ]);

        // Jika diubah menjadi aktif, nonaktifkan yang lain kecuali data ini sendiri
        if ($validated['is_active'] ?? false) {
            TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['is_active' => false]);
        }

        $tahunAjaran->update($validated);
        return redirect()->back();
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return redirect()->back();
    }
}
