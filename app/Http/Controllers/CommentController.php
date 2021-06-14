<?php

namespace App\Http\Controllers;

use App\Events\UserNotification;
use App\Exceptions\EntityException;
use App\Exceptions\ErrorException;
use App\Models\Collab;
use App\Models\Comment;
use App\Models\Creator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


/**
 * Class CommentController
 * @package App\Http\Controllers
 * @author Cookie
 */
class CommentController extends Controller
{
    /**
     * @return Collection
     * @throws EntityException
     */
    public function index(): Collection
    {
        /** @var Collab|Creator $entity */
        $entity = get_entity(request('entity'), request('id'));
        $scopes = explode(',', request('scopes'));

        $builder = $entity->comments()->orderBy("created_at");

        if ($type = request('type')) {
            $builder = $builder->where('type', $type);
        }

        in_array('user.image', $scopes)
        && $builder = $builder->with(['user.image' => fn($query) => $query->select('id', 'url')]);

        in_array('user', $scopes)
        && $builder = $builder->with(['user' => fn($query) => $query->select('id', 'name', 'slug', 'image_id')]);

        in_array('userLikes', $scopes)
        && $builder = $builder->withCount('userLikes');

        return $builder->get();
    }

    /**
     * @throws EntityException
     * @throws ErrorException
     */
    public function create(): Comment
    {
        /** @var Creator|Collab $entity */
        $entity = get_entity(request('entity'), request('id'));
        if ($entity instanceof Collab) {
            if (request("type") == 'init') {
                if ($entity->comments()->initial()->exists()) {
                    throw new ErrorException(20001);
                }
                if ($entity->user_id != auth()->id()) {
                    throw new ErrorException(20002);
                }
            }
        }

        /** @var Comment $comment */
        $comment = Comment::create([
            'commentable_id'   => $entity->id,
            'commentable_type' => get_class($entity),
            'user_id'          => auth()->id(),
            'text'             => request('text'),
            'type'             => request('type'),
            'reply_to_id'      => request('reply_to_id'), // @todo check if exists and have authorization
        ]);

        $comment->load('commentable');
        if ($comment?->commentable->creator1_id) {
            $comment->commentable->creator1->load('image');
            $comment->commentable->creator2->load('image');
        }

        $comment->collab = $comment->commentable;
        $comment->load("user");

        if (($userId = $entity?->user_id) and $userId != auth()->id()) {
            event(new UserNotification($userId, [
                'type'       => 'collabComment',
                'entityType' => 'comment',
                'entity'     => $comment,
            ]));
        }

        event(new \App\Events\Comment('Collab', $entity->id, ['test'=>'test123123']));

        return $comment;
    }

    /**
     * @return Comment
     * @throws EntityException
     */
    public function createLike(): Comment
    {
        /** @var Comment $comment */
        $comment = get_entity(Comment::class, request('id'))
            ->addLike();

        $comment->load('commentable');
        if ($comment->commentable->creator1) {
            $comment->commentable->creator1->load('image');
            $comment->commentable->creator2->load('image');
            $comment->load("user");
        }

        $comment->collab = $comment->commentable;

        // @todo move to comment fire notification
        if (auth()->id() != $comment->user_id) {
            event(new UserNotification($comment->user_id, [
                'type'       => 'commentLike',
                'entityType' => 'comment',
                'entity'     => $comment,
            ]));
        }

        return $comment;
    }

    /**
     * @return Comment
     * @throws EntityException
     */
    public function deleteLike(): Comment
    {
        return get_entity(Comment::class, request('id'))
            ->deleteLike();
    }
}
