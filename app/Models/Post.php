<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'description',
        'image_path',
        'image_path_2',
        'image_path_3',
        'image_public_id',
        'image_public_id_2',
        'image_public_id_3',
        'media_type',
        'deletion_reason',
        'deleted_by',
        'appeal_status',
        'appeal_message',
        'appeal_at',
    ];

    protected $dates = ['deleted_at', 'appeal_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'deleted_by');
    }
}