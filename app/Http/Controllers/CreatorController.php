<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityException;
use App\Exceptions\ErrorException;
use App\Helpers\UrlSniffer;
use App\Models\Category;
use App\Models\Collab;
use App\Models\Creator;
use App\Models\CreatorInfo;
use App\Models\Image;
use App\Models\Traits\ThirdPartyParser;
use Cache;
use Carbon\Carbon;
use Cloudinary\Uploader;
use DB;
use Exception;
use Google_Client;
use Google_Service_Exception;
use Google_Service_YouTube;
use Google_Service_YouTube_ChannelListResponse;
use Google_Service_YouTube_SearchResult;
use Google_Service_YouTube_Thumbnail;
use Goutte\Client;
use http\Client\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Log;
use Throwable;
use TwitchApi\Exceptions\ClientIdRequiredException;
use TwitchApi\Exceptions\EndpointNotSupportedByApiVersionException;
use TwitchApi\Exceptions\InvalidIdentifierException;

/**
 * Class CreatorController
 * @package App\Http\Controllers
 * @author  Cookie
 */
class CreatorController extends Controller
{
    /**
     * @return LengthAwarePaginator
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author Cookie
     */
    public function index(): LengthAwarePaginator
    {
        // Init Collabs builder with default ordering
        $builder = Creator::select("creators.*");

        // Created Range filter
        if ($createdRange = request("createdRange")) {
            $builder = $builder
                ->where('creators.created_at', '>', date_from_range($createdRange));
        }

        // Trending Range filter
        if ($trendingRange = request("trendingRange")) {
            $builder = $builder
                ->select("creators.*", DB::raw("count(l.id) as trending_likes_count"))
                ->join('collabs as c',
                    function (JoinClause $join) {
                        $join
                            ->on('creators.id', '=', 'c.creator1_id')
                            ->orOn('creators.id', '=', 'c.creator2_id');
                    }
                )
                ->join('likes as l', 'l.collab_id', 'c.id')
                ->where('l.created_at', '>', date_from_range($trendingRange))
                ->groupBy('creators.id')
                ->orderByRaw('count(l.id) desc');;
        }

        // Sources
        if ($sources = request("sources")) {
            $sources = explode(",", $sources);
            $builder = $builder->join('creator_source',
                function (JoinClause $join) use ($sources) {
                    $join
                        ->on("creator_source.creator_id", "=", "creators.id")
                        ->whereIn("creator_source.source_id", $sources);
                });
        }

        // Status
        ($status = request('status')) && $builder = $builder->where('status', $status);

        // Category
        // @todo category filter

        // Filter (none, created, liked)
        $userId = auth()->guard('api')->id();
        switch (request('filter')) {
//            case 'liked':
//                $builder->where(DB::raw("id in (select collab_id from likes where user_id = $userId)"));
//                break;
            case 'created':
                $builder->where("created_by_id", $userId);
                break;
            case 'in-created-collab':
                $builder
                    ->join("collabs",
                        DB::raw("collabs.creator1_id = creators.id or collabs.creator2_id"),
                        'creators.id')
                    ->where('collabs.user_id', auth()->guard("api")->id());

                break;
            case 'in-liked-collab':
                $builder
                    ->join("collabs",
                        DB::raw("collabs.creator1_id = creators.id or collabs.creator2_id"),
                        'creators.id')
                    ->join("likes", "collabs.id", "=", "likes.collab_id")
                    ->where('likes.user_id', 1);
                break;
        }

        // Caching
        if (request("trendingRange") and !request("filter")) {
            $cacheName = request()->getRequestUri();

            if (!$creators = Cache::get($cacheName)) {
                // cache all get data
                $creators = $builder->paginate();
                Cache::set($cacheName, $creators, now()->addMinutes(15));
            }
        } else {
            // without caching - so use origin paginate in database query
            $creators = $builder->paginate();
        }

        // Scopes
        $scopes = explode(',', request('scopes'));
        in_array('image', $scopes) && $creators->load('image');
        in_array('userLikes', $scopes) && $creators->loadCount('userLikes');
        in_array('sources', $scopes) && $creators->load('sources');

        return $creators;
    }

    /**
     * @return HttpResponse|array|EloquentCollection
     */
    public function search(): HttpResponse|array|EloquentCollection
    {
        if (!strlen($query = request('query'))) {
            return error(5005);
        }

        return Creator::search($query, request("limit"))
            ->get();
    }

    /**
     * @return Creator|Model
     */
    public function create(): Model|Creator
    {
        $creator = Creator::create(request()->only('name', 'description', 'country_id'));
        $creator->created_by_id = auth()->id();
        $creator->save();

        // @todo bind categories
        $creator->categories()->attach(request('categories'));

        $creator_id = $creator->id;
        // @todo refactor assignment
        foreach (request('additional_info') as $field => $value) {
            $creatorInfo = CreatorInfo::create(compact('field', 'value', 'creator_id'));
        }

        return $creator
            ->load(['info', 'categories']);
    }

    /**
     * @return Collection|Creator[]|Creator
     * @throws EntityException
     */
    public function detail(): Creator|Collection|array
    {
        $creators = get_entity(Creator::class, request('id'), true);

        $scopes = explode(',', request('scopes'));

        // resolve scopes
        foreach ($creators as $creator) {
            in_array('collabs', $scopes) && $creator->loadCollabs();
            in_array('info', $scopes) && $creator->load('info');
            in_array('image', $scopes) && $creator->load('image');
            in_array('sources', $scopes) && ($creator->loadSources());
            in_array('categories', $scopes) && $creator->load('categories');
        }


        if ($creators->count() == 1) {
            return $creators->first();
        } else {
            return $creators;
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws EntityException
     * @deprecated Update this method
     */
    public function avatar(Request $request): mixed
    {
        /** @var Creator $creator */
        $creator = get_entity('Creator', request('creator_id'));

        if ($creator->created_by_id != auth()->id()) {
            return response(['message' => 'User not authorized to change this creator.'], 422);
        }

        $path = (object)$request->file('file');

        $image = Image::upload($path, 'creator_avatar');

        $creator->image()->associate($image);
        $creator->save();

        return $image;
    }

    /**
     * @return Creator|ThirdPartyParser|Application|ResponseFactory|HigherOrderBuilderProxy|Model|HttpResponse|mixed
     * @throws ClientIdRequiredException
     * @throws EndpointNotSupportedByApiVersionException
     * @throws InvalidIdentifierException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws Throwable
     */
    public function url()
    {
        if ($creator = app(UrlSniffer::class)->fire($url = request('url'))) {
            return $creator;
        } else {
            return error(10001, additionalData: compact('url'));
        }
    }

    /**
     * @return mixed
     * @throws EntityException
     */
    public function id(): mixed
    {
        return get_entity_by_slug(Creator::class, request('slug'))->id;
    }

    /**
     * @return Creator
     * @throws EntityException
     */
    public function createLike(): Creator
    {
        return get_entity(Creator::class, request('id'))
            ->addLike();
    }

    /**
     * @return Creator
     * @throws Exception
     */
    public function deleteLike(): Creator
    {
        return get_entity(Creator::class, request('id'))
            ->deleteLike();
    }
}
