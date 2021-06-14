<?php

namespace App\Models;

use App\Exceptions\ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Comment
 * @package App\Models
 * @author Cookie
 * @method static create(array $array)
 */
class Comment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return MorphTo
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Builder $builder
     * @return mixed
     */
    public function scopeMine(Builder $builder): mixed
    {
        return $builder->whereUserId(auth()->id());
    }

    /**
     * @param Builder $builder
     * @return mixed
     */
    public function scopeInitial(Builder $builder)
    {
        return $builder->whereType('init');
    }

    /**
     * @param null $userId
     * @return $this
     * @throws ErrorException
     */
    public function addLike($userId = null): self
    {
        $data = [
            'user_id' => $userId ?? auth()->id(),
            'comment_id' => $this->id,
        ];

        if (CommentLike::where($data)->exists()) {
            // User has liked this comment already
            throw new ErrorException(20003);
        }

        // fire "like"
        CommentLike::create($data);

        return $this
            ->recomputeLikes()
            ->loadCount('userLikes');
    }

    /**
     * @param null $userId
     * @return $this
     * @throws ErrorException
     */
    public function deleteLike($userId = null): self
    {
        $data = [
            'user_id' => $userId ?? auth()->id(),
            'comment_id' => $this->id,
        ];

        if (!$like = CommentLike::where($data)->first()) {
            // Like does not exist
            throw new ErrorException(20004);
        }

        $like->delete();

        return $this
            ->recomputeLikes()
            ->loadCount('userLikes');
    }

    /**
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * @return HasMany
     */
    public function userLikes(): HasMany
    {
        return $this
            ->hasMany(CommentLike::class)
            ->where('user_id', auth()->guard('api')->id());
    }

    /**
     * @return $this
     */
    public function recomputeLikes(): self
    {
        $this->update(
            ['likes_count' => $this->likes()->count()],
            ['timestamps' => false],
        );

        return $this;
    }

    /**
     * @return MorphOne
     */
    public function notification(): MorphOne
    {
        return $this->morphOne(Notification::class, 'entity');
    }
}

