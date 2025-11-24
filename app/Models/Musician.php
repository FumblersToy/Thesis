<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musician extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'stage_name',
        'genre',
        'instrument',
        'bio',
        'location',
        'profile_picture',
        'profile_picture_public_id',
        'credential_document',
        'credential_document_public_id',
        'verified',
        'latitude',
        'longitude',
        'location_name',
    ];
}
