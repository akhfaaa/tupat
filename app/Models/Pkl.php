<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkl extends Model
{
    protected $guarded = ['id'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function mitraDudi()
    {
        return $this->belongsTo(MitraDudi::class);
    }

    public function logbooks()
    {
        return $this->hasMany(PklLogbook::class);
    }

    public function assessment()
    {
        return $this->hasOne(PklAssessment::class);
    }
}
