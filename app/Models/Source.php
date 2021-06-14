<?php

namespace App\Models;

use Cache;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JetBrains\PhpStorm\Pure;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

/**
 * Class Source
 * @package App\Models
 * @author  Cookie
 */
class Source extends Model
{
    use HasFactory;
    use Sluggable;

    protected $guarded = ['id'];

    /**
     * @param string $name
     * @return Source|Builder|Model|mixed|object|null
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    static public function getId(string $name)
    {
        if (!$sourceId = Cache::get("source_$name")) {
            $sourceId = optional(self::whereName($name)->first())->id;
            Cache::set("source_$name", $sourceId, now()->addMonth());
        }

        throw_unless($sourceId, "Source not found");

        return $sourceId;
    }

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

    /**
     * @param self|int $source
     * @return Source|int
     * @deprecated
     *
     * returns Id of Source (whether $source is Id or Model)
     */
    #[Pure] static public function id(Source|int $source): Source|int
    {
        return is_object($source) ? $source->id : $source;
    }

    public function getUrlAttribute()
    {
        return "1234";
    }
}
