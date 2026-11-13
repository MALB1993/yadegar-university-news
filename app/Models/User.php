<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'fullname',
    'phone_number',
    'role',
    'identification_code',
    'profile_picture',
    'status',
    'password',
    'email',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * News saved by this user.
     *
     * @return BelongsToMany<Post, $this>
     */
    public function bookmarkedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }

    /**
     * Categories this user follows.
     *
     * @return BelongsToMany<Category, $this>
     */
    public function followedCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    /**
     * Comments this user has liked.
     *
     * @return BelongsToMany<Comment, $this>
     */
    public function likedComments(): BelongsToMany
    {
        return $this->belongsToMany(Comment::class)->withTimestamps();
    }

    /**
     * Persian label of the role, used in the views.
     *
     * @return Attribute<string, never>
     */
    protected function roleLabel(): Attribute
    {
        return Attribute::get(fn (): string => match ($this->role) {
            'professor' => 'استاد',
            'employee' => 'کارمند',
            default => 'دانشجو',
        });
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isProfessor(): bool
    {
        return $this->role === 'professor';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }
}
