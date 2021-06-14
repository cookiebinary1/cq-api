<?php

namespace App\Http\Controllers;

use App\Events\UserNotification;
use App\Exceptions\EntityException;
use App\Exceptions\ErrorException;
use App\Models\Collab;
use App\Models\CollabStatus;
use App\Models\Like;
use Cache;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\NoReturn;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class CollabController
 * @package App\Http\Controllers
 * @author  Cookie
 */
class CollabController extends Controller
{
    /**
     * @return Response|Application|ResponseFactory|Collab
     * @throws EntityException|ErrorException
     */
    public function create(): Response|Application|ResponseFactory|Collab
    {
        // get and validate request
        $collabData = request()->validate([
            'creator1_id' => 'required|integer',
            'creator2_id' => 'required|integer',
            'title'       => 'required|max:50',
            'description' => '',
        ]);

        $id1 = request('creator1_id');
        $id2 = request('creator2_id');

        if ($id1 == $id2) {
            return error(6002, additionalData: request()->toArray());
        }

        // check if exists (else throw error) and check priorities
        $creator1 = get_entity('Creator', $id1);
        $creator2 = get_entity('Creator', $id2);

        // sort them by priority
        if ($creator1->priority < $creator2->priority) {
            // just switch
            list($id1, $id2) = [$id2, $id1];
        }

        // fill data back to array
        $collabData['user_id'] = auth()->id();
        $collabData['creator1_id'] = $id1;
        $collabData['creator2_id'] = $id2;

        return Collab::create($collabData)->addLike();
    }

    /**
     * @return array|Collection
     */
    public function byCreators(): array|Collection
    {
        request()->validate([
            'creator1_id' => 'integer',
            'creator2_id' => 'integer',
        ]);

        return Collab::where(function ($query) {
            $query
                ->whereCreator1Id(request('creator1_id'))
                ->whereCreator2Id(request('creator2_id'));
        })->orWhere(function ($query) {
            $query
                ->whereCreator1Id(request('creator2_id'))
                ->whereCreator2Id(request('creator1_id'));
        })->with('creator1.image', 'creator2.image')->withCount('userLikes')->get();
    }

    /**
     * @throws InvalidArgumentException|ErrorException
     * @author Cookie
     */
    public function index(): LengthAwarePaginator|Collection
    {
        // Init Collabs builder with default ordering
        $builder = Collab::select("collabs.*");

        // Created Range filter
        if ($createdRange = request("createdRange")) {
            $builder = $builder->where('collabs.created_at', '>', date_from_range($createdRange));
        }

        // Trending Range filter
        if ($trendingRange = request("trendingRange")) {
            $builder = $builder
                ->select("collabs.*", DB::raw("count(l.id) as trending_likes_count"))
                ->join('likes as l',
                    function (JoinClause $join) use ($trendingRange) {
                        $join
                            ->on('l.collab_id', 'collabs.id')
                            ->where('l.created_at', '>', date_from_range($trendingRange));
                    })
                ->groupBy('collabs.id')
                ->orderByDesc('trending_likes_count');
        } else {
            // by default ordering by total likes count (not only trending ones)
            $builder = $builder->orderByDesc('likes_count');
        }

        // Sources
        if ($sources = request("sources")) {
            $sources = explode(",", $sources);
            $builder
                ->join("creator_source as cs1", "cs1.creator_id", "collabs.creator1_id")
                ->whereIn("cs1.source_id", $sources);
            $builder
                ->join("creator_source as cs2", "cs2.creator_id", "collabs.creator2_id")
                ->whereIn("cs1.source_id", $sources);
//            $builder = $builder->join('creator_source',
//                function (JoinClause $join) use ($sources) {
//                    $join->on(function ($join) {
//                        $join
//                            ->on("creator_source.creator_id", "=", "collabs.creator1_id")
//                            ->orOn("creator_source.creator_id", "=", "collabs.creator2_id");
//                    })->whereIn("creator_source.source_id", $sources);
//                });
        }

        // Status
        ($status = request('status')) && $builder = $builder->where('status', $status);

        // Creator(s)
        if ($creatorId = request('creatorId')) {
            $creatorIds = explode(',', $creatorId);
            $builder->where(function ($request) use ($creatorIds) {
                $request
                    ->whereIn('creator1_id', $creatorIds)
                    ->orWhereIn('creator2_id', $creatorIds);
            });
        }

        // Category
        // @todo category filter - extra low priority

        // Filter (none, created, liked)

        if (($filters = request("filter")) and ($filters != 'all')) {
            if (!$userId = request('userId') ?? auth()->guard('api')->id()) {
                throw new ErrorException(30001);
            }
            $builder = $builder->where(function ($builder) use ($userId, $filters) {
                $builder->where(null);
                foreach (explode(",", $filters) as $filter) {
                    switch ($filter) {
                        case 'liked':
                            $builder->orWhereRaw(DB::raw("collabs.id in (select collab_id from likes where user_id = $userId)"));
                            break;
                        case 'created':
                            $builder->orWhere("collabs.user_id", $userId);
                            break;
                    }
                }
                return $builder;
            });
        }

//        dd($builder->toSql(), $builder->getBindings());

        // Caching
        if (request("trendingRange") and !request("filter")) {
            $cacheName = request()->getRequestUri();

            if (!$collabs = Cache::get($cacheName)) {
                // cache all get data
                $collabs = $builder->paginate();
                Cache::set($cacheName, $collabs, now()->addMinutes(5));
                \Log::info("CACHE UPDATE: " . $cacheName);
            }
        } else {
            // without caching - so use origin paginate in database query
            $collabs = $builder->paginate();
        }

        // Scopes
        $scopes = explode(',', request('scopes'));
        in_array('creator1', $scopes) && $collabs->load('creator1');
        in_array('creator2', $scopes) && $collabs->load('creator2');
        in_array('creator1.image', $scopes) && $collabs->load('creator1.image');
        in_array('creator2.image', $scopes) && $collabs->load('creator2.image');
        in_array('userLikes', $scopes) && $collabs->loadCount('userLikes');

//        if (isset($cacheName)) {
//            Cache::set($cacheName, $collabs, now()->addHour());
//        }

        return $collabs;
    }

    /**
     * @return mixed
     * @throws EntityException
     */
    public function detail(): mixed
    {
        $collabs = get_entity(Collab::class, request('id'), true);

        $scopes = explode(',', request('scopes'));

        // resolve scopes
        /** @var Collab $collab */
        foreach ($collabs as $collab) {
            in_array('creators', $scopes) && $collab->load('creator1', 'creator2');
            in_array('creators.images', $scopes) && $collab->load('creator1.image', 'creator2.image');
            in_array('userLikes', $scopes) && $collab->loadCount('userLikes');
            $collab->can_delete = $collab->canDelete();
        }

        if ($collabs->count() == 1) {
            return $collabs->first();
        } else {
            return $collabs;
        }
    }

    /**
     * @return Application|ResponseFactory|Response|string[]
     * @throws EntityException
     * @throws Exception
     */
    public function delete(): array|Response|Application|ResponseFactory
    {
        /** @var Collab $collab */
        $collab = get_entity(Collab::class, request('id'));

        if (auth()->id() != $collab->user_id) {
            return error(400422, 404, 'User not authorized for deleting this collab.');
        }

        if ($collab->created_at->addMinutes(30) < now()) {
            return error(400423, 404, 'Collab was older than 30 minutes.');
        }

        $collab->delete();

        return ['status' => 'ok'];
    }

    /**
     * @return Collab
     * @throws Exception
     */
    public function createLike(): Collab
    {
        /** @var Collab $collab */
        $collab = get_entity(Collab::class, request('id'));

        $collab->addLike();

        $collab->creator1->load('image');
        $collab->creator2->load('image');

        if (auth()->id() != $collab->user_id) {
            \Log::info("Broadcast: LIKE!");
            event(new UserNotification($collab->user_id, [
                'type'       => 'collabLike',
                'entityType' => 'collab',
                'entity'     => $collab,
            ]));
        }

        return $collab;
    }

    /**
     * @return Collab
     * @throws Exception
     */
    public function deleteLike(): Collab
    {
        return get_entity(Collab::class, request('id'))
            ->deleteLike();
    }

    /**
     * @return mixed
     * @throws EntityException
     */
    public function id()
    {
        return get_entity_by_slug(Collab::class, request('slug'))->id;
    }

    /**
     * @return Application|ResponseFactory|Response|string[]
     * @throws EntityException
     */
    public function status()
    {
        /** @var Collab $collab */
        $collab = get_entity(Collab::class, request('collab_id'));

        $collabStatus = CollabStatus::create([
            'collab_id' => $collab->id,
            'user_id'   => auth()->id(),
            'status'    => request("new_status"),
        ]);

        if ($collabStatus) {
            return ['status' => 'ok'];
        } else {
            return error(202033, 404, "Some issue");
        }
    }
}
