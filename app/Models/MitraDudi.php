<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraDudi extends Model
{
    use HasFactory;

    protected $table = 'mitra_dudis';

    protected $fillable = [
        'nama_perusahaan',
        'bidang_usaha',
        'alamat',
        'nama_pimpinan',
        'kontak_person',
        'telepon',
        'email',
        'status_aktif',
    ];
}
