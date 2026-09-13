<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklLogbook extends Model
{
    protected $fillable = [
        'pkl_id',
        'tanggal',
        'kegiatan',
        'hasil',
        'status_verifikasi',
        'verified_by',
        'verified_at',
        'catatan_verifikasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'verified_at' => 'datetime',
    ];

    public function pkl()
    {
        return $this->belongsTo(Pkl::class);
    }
}