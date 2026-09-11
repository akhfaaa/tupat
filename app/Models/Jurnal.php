<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $guarded = ['id'];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    // Satu jurnal memiliki banyak data absensi siswa
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
