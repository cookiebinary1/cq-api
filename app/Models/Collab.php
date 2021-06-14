<?php

namespace App\Models;

use App\Exceptions\ErrorException;
use Cviebrock\EloquentSluggable\Sluggable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Collab
 * @package App\Models
 * @author  Cookie
 */
class Collab extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $collab) {
            $collab->notification()->delete();
        });
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function creator1()
    {
        return $this->belongsTo(Creator::class, 'creator1_id');
    }

    /**
     * @return BelongsTo
     */
    public function creator2()
    {
        return $this->belongsTo(Creator::class, 'creator2_id');
    }

    /**
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * @return HasMany
     */
    public function userLikes(): HasMany
    {
        return $this
            ->hasMany(Like::class)
            ->where('user_id', auth()->guard('api')->id());
    }

    /**
     * @param null $userId
     * @return Collab
     * @throws ErrorException
     */
    public function addLike($userId = null): Collab
    {
        $data = [
            'user_id'   => $userId ?? auth()->id(),
            'collab_id' => $this->id,
        ];

        if (Like::where($data)->exists()) {
            // User has liked this collab already
            throw new ErrorException(6003);
        }

        // fire "like"
        Like::create($data);

        return $this
            ->recomputeLikes()
            ->loadCount('userLikes');
    }

    /**
     * @param null $userId
     * @return Collab
     * @throws ErrorException
     * @throws Exception
     */
    public function deleteLike($userId = null): Collab
    {
        $data = [
            'user_id'   => $userId ?? auth()->id(),
            'collab_id' => $this->id,
        ];

        if (!$like = Like::where($data)->first()) {
            // Like does not exist
            throw new ErrorException(6004);
        }

        $like->delete();

        return $this
            ->recomputeLikes()
            ->loadCount('userLikes');
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
     * @return string[][]
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'full_title',
            ]
        ];
    }

    /**
     * @return string
     */
    public function getFullTitleAttribute(): string
    {
        return $this->creator1->name . " with " . $this->creator2->name . " " . $this->title;
    }

    /**
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @param Builder $builder
     * @param string $query
     * @param int|null $limit
     * @return Builder
     */
    public function scopeSearch(Builder $builder, string $query, $limit = null): Builder
    {
        $query = strlen($query) >= 3 ? "%$query%" : "$query%";
        return $builder
            ->select('collabs.*')
            ->leftJoin('creators as c1', 'creator1_id', 'c1.id')
            ->leftJoin('creators as c2', 'creator2_id', 'c2.id')
            ->where(function (Builder $builder) use ($query) {
                return $builder
                    ->orWhere('c1.name', 'like', $query)
                    ->orWhere('c2.name', 'like', $query)
                    ->orWhere('collabs.title', 'like', $query);
            })
            ->with([
                'creator1.info',
                'creator2.info',
                'creator1.image',
                'creator2.image',
                'creator1.sources',
                'creator2.sources'
            ])
            ->limit($limit ?? 10)
            ->orderByDesc('likes_count');
    }

    /**
     * @return MorphOne
     */
    public function notification(): MorphOne
    {
        return $this->morphOne(Notification::class, 'entity');
    }

    /**
     * @return $this
     */
    public function loadImages(): self
    {
        return $this->load('creator1.image', 'creator2.image');
    }

    /**
     * @return bool
     */
    public function canDelete()
    {
        return $this->user_id == auth()->guard('api')->id() and $this->created_at->addMinutes(30) > now();
    }
}
