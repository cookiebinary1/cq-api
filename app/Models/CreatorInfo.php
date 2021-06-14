<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CreatorInfo
 * @package App\Models
 * @author  Cookie
 */
class CreatorInfo extends Model
{
    use HasFactory;

    protected $table = 'creator_info';

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(Creator::class);
    }

    /**
     * @return BelongsTo
     */
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
