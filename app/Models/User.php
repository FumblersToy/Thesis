<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function musician(): HasOne
    {
        return $this->hasOne(Musician::class);
    }

    public function business(): HasOne
    {
        return $this->hasOne(Business::class);
    }

    // Follow relationships
    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    public function following(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function followingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }

    public function followerUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    // Message relationships
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function unreadMessages(): HasMany
    {
        return $this->receivedMessages()->where('is_read', false);
    }

    public function unreadMessageCount(): int
    {
        return $this->unreadMessages()->count();
    }

    // Post relationships
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // Like relationships
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    // Comment relationships
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
