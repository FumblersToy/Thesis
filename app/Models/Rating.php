<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'musician_id',
        'rating',
        'comment',
    ];

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function musician()
    {
        return $this->belongsTo(User::class, 'musician_id');
    }
}