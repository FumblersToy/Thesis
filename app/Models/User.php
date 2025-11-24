<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
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
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
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

    // Update last seen timestamp
    public function updateLastSeen(): void
    {
        $this->update(['last_seen_at' => now()]);
    }

    // Check if user is online (seen within last 5 minutes)
    public function isOnline(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }
        
        return $this->last_seen_at->diffInMinutes(now()) < 5;
    }
}
