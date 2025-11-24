<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Business extends Model
{
    use HasFactory;

    protected $table = 'businesses';

    protected $fillable = [
        'user_id',
        'profile_picture',
        'profile_picture_public_id',
        'business_permit',
        'business_permit_public_id',
        'verified',
        'business_name',
        'contact_email',
        'phone_number',
        'address',
        'venue',
        'location',
        'latitude',
        'longitude',
        'location_name',
        'address_latitude',
        'address_longitude',
        'instagram_url',
        'facebook_url',
        'twitter_url',
        'website_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
