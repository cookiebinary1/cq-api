<?php

namespace App\Models;

use App\Events\UserNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Notification
 * @package App\Models
 * @author Cookie
 */
class Notification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @deprecated
     */
    public function fire()
    {
        return;
//        $this->load('entity');

//        switch($this->entity_type) {
//            case'commentLike':
//                $this->entity->load('comments');
//        }

        // load all possible relations
        $this->entity?->load("creator1.image", "creator2.image");

        event(new UserNotification($this->user_id, [
            'type'       => $this->type,
            'entityType' => $this->entity_type,
            'entity'     => $this->entity,
            'count'      => $this->count,
            'image'      => $this->image,
        ]));

        return $this;
    }

    /**
     * @return MorphTo
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }
}
