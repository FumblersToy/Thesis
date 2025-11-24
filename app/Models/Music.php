<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Music extends Model
{
    protected $fillable = [
        'musician_id',
        'title',
        'audio_url',
        'audio_public_id',
        'duration',
    ];

    public function musician(): BelongsTo
    {
        return $this->belongsTo(Musician::class);
    }
}
