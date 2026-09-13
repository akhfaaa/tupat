<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklAssessment extends Model
{
    protected $fillable = [
        'pkl_id',
        'assessed_by',
        'technical_score',
        'discipline_score',
        'communication_score',
        'teamwork_score',
        'notes',
        'assessed_at',
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
    ];

    public function pkl()
    {
        return $this->belongsTo(Pkl::class);
    }
}