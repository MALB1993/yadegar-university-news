<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

// The author (user_id), views and created_at are never mass assigned.
#[Fillable(['category_id', 'title', 'body', 'image', 'status', 'is_pinned', 'published_at'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'views' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Staff see every post. Everybody else sees news that is live (published and due)
     * plus their own posts. The columns are qualified because the bookmarks query joins the pivot table.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isEmployee()) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user): void {
            $query->where(function (Builder $live): void {
                $live->where('posts.status', 'published')
                    ->where(function (Builder $due): void {
                        $due->whereNull('posts.published_at')
                            ->orWhere('posts.published_at', '<=', now());
                    });
            })->orWhere('posts.user_id', $user->id);
        });
    }

    /**
     * Published and due, so readers outside the staff can see it.
     */
    public function isLive(): bool
    {
        return $this->status === 'published'
            && ($this->published_at === null || $this->published_at->isPast());
    }

    /**
     * Published with a future date, so it is not public yet.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->isFuture();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Users who saved this news for later.
     *
     * @return BelongsToMany<User, $this>
     */
    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Public URL of the uploaded image, or null.
     *
     * @return Attribute<string|null, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->image ? asset('storage/'.$this->image) : null
        );
    }

    /**
     * Short plain-text preview for the news cards.
     *
     * @return Attribute<string, never>
     */
    protected function excerpt(): Attribute
    {
        return Attribute::get(fn (): string => Str::limit(strip_tags($this->body), 160));
    }

    /**
     * Rough reading time, about 180 words per minute.
     *
     * @return Attribute<int, never>
     */
    protected function readingMinutes(): Attribute
    {
        return Attribute::get(function (): int {
            $words = count(preg_split('/\s+/u', trim($this->body)) ?: []);

            return max(1, (int) ceil($words / 180));
        });
    }
}
