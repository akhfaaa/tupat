<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $fillable = [
        'posted_by',
        'title',
        'company',
        'location',
        'description',
        'closing_date',
        'status',
    ];

    protected $casts = [
        'closing_date' => 'date',
    ];

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
