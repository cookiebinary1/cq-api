<?php

namespace App\Models\Traits;

use App\Models\Country;
use App\Models\Image;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait UserRelations
 * @package App\Models\Traits
 * @author Cookie
 * User relations and scopes
 */
trait UserRelations
{
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * @param Builder $builder
     * @param string $query
     * @param int|null $limit
     * @return Builder
     */
    public function scopeSearch(Builder $builder, string $query, $limit = null): Builder
    {
        return $builder
            ->where('name', 'like', strlen($query) >= 3 ? "%$query%" : "$query%")
            ->with('image')
            ->limit($limit ?? 10);
//            ->orderByDesc('likes_count');
    }
}
