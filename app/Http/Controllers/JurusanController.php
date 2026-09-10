<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Inertia\Inertia;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        // Mengambil semua data jurusan dari database
        $jurusans = Jurusan::latest()->get();

        // Mengirim data ke halaman Vue
        return Inertia::render('Master/Jurusan/Index', [
            'jurusans' => $jurusans
        ]);
    }
}
