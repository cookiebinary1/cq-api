<?php

namespace App\Models;

use App\Models\Traits\UserRelations;
use Cviebrock\EloquentSluggable\Sluggable;
use DB;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use JetBrains\PhpStorm\Pure;
use Laravel\Passport\HasApiTokens;
use Log;

/**
 * Class User
 * @package App\Models
 * @author Cookie
 */
class User extends Authenticatable
{
    use
        HasFactory,
        Notifiable,
        HasApiTokens,
        Sluggable,
        UserRelations;

    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id',
        'fusion_id',
        'fusion_data'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'fusion_data'       => 'array',
    ];

    /**
     * @param object $fusionUser
     * @return User
     */
    public static function syncFusionUser(object $fusionUser)
    {
        $fusionId = $fusionUser->id;

        $user = self::whereFusionId($fusionId)
            ->orWhere("email", $fusionUser->email)
            ->first();

        if (!$user) {
            $user = self::create([
                'name'      => $fusionUser->username ?? explode("@", $fusionUser->email)[0],
                'email'     => $fusionUser->email,
                'fusion_id' => $fusionId,
            ]);
        }

        $user->fusion_data = $fusionUser;
        $user->save();

        return $user;
    }

    /**
     * @return string[][]
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'forcename',
            ]
        ];
    }

    /**
     * @return mixed
     */
    #[Pure]
    public function getForcenameAttribute(): mixed
    {
        return $this->name ? $this->name : strstr($this->email, '@', true);
    }

    /**
     *
     */
    public function notificationProcess()
    {
        $userId = $this->id;

        // get datetime of last notification
        if (!$lastNotificationDate = Notification::whereUserId($userId)
            ->orderByDesc('created_at')->first()?->created_at) {
            $lastNotificationDate = now()->subCentury();
        }

        // Collab Likes
        $builder = Collab::select(["collabs.*", DB::raw("count(likes.id) as likes_count")])
            ->join("likes", "likes.collab_id", "=", "collabs.id")
            ->where("collabs.user_id", $userId)
            ->where("likes.created_at", ">", $lastNotificationDate)
            ->groupBy("collabs.id");

        /** @var Collab $collab */
        foreach ($builder->cursor() as $collab) {
            Notification::create([
                'type'        => 'collabLike',
                'user_id'     => $userId,
                'entity_type' => $collab::class,
                'entity_id'   => $collab->id,
                'count'       => $collab->likes_count,
                'image_id'    => $collab->creator1->image_id,
            ]);
        }


        // Collab Comments
        $builder = Collab::select(["collabs.*", DB::raw("count(comments.id) as comments_count")])
            ->join("comments", "comments.commentable_id", "=", "collabs.id")
            ->where('comments.commentable_type', 'App\Models\Collab')
            ->where("collabs.user_id", $userId)
            ->where("comments.created_at", ">", $lastNotificationDate)
            ->groupBy("collabs.id");

        /** @var Collab $collab */
        foreach ($builder->cursor() as $collab) {
            Notification::create([
                'type'        => 'collabComment',
                'user_id'     => $userId,
                'entity_type' => $collab::class,
                'entity_id'   => $collab->id,
                'count'       => $collab->likes_count,
                'image_id'    => $collab->creator1->image_id,
            ]);
        }

        // Comment Likes
        $builder = Comment::select(["comments.*", DB::raw("count(comment_likes.id) as comment_likes_count")])
            ->join("comment_likes", "comment_likes.comment_id", "=", "comments.id")
            ->where("comments.user_id", $userId)
            ->where("comment_likes.created_at", ">", $lastNotificationDate)
            ->groupBy("comments.id");

        /** @var Comment $comment */
        foreach ($builder->cursor() as $comment) {
            $collab = $comment->commentable;
            if ($collab) {
                Notification::create([
                    'type'        => 'commentLike',
                    'user_id'     => $userId,
                    'entity_type' => $collab::class,
                    'entity_id'   => $collab->id,
                    'count'       => $comment->comment_likes_count,
                    'image_id'    => $comment->user->image_id,
                ]);
            }
        }

        Log::alert("DONE");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Cookie
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
