<?php

namespace App\Models;

use Cache;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class Category
 * @package App\Models
 * @author  Cookie
 */
class Category extends Model
{
    use HasFactory;
    use Sluggable;

    protected $fillable = ['name', 'description'];

//    /**
//     * @param $name
//     * @return Category|Builder|Model|mixed|object|null
//     * @throws InvalidArgumentException
//     */
//    static public function getId($name)
//    {
//        if (!$categoryId = Cache::get("category_$name")) {
//            $categoryId = optional(self::whereName($name)->first())->id;
//            Cache::set("category_$name", $categoryId, now()->addMonth());
//        }
//
//        return $categoryId;
//    }

    /**
     * @return BelongsToMany
     */
    public function creators(): BelongsToMany
    {
        return $this->belongsToMany(Creator::class);
    }

    /**
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id', 'users');
    }

    /**
     * @return string[][]
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ]
        ];
    }
}
