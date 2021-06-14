<?php

namespace App\Http\Controllers;

use App\Events\UserNotification;
use App\Exceptions\EntityException;
use App\Models\Alias;
use App\Models\Category;
use App\Models\Collab;
use App\Models\Creator;
use App\Models\Source;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Creators;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use JetBrains\PhpStorm\ArrayShape;
use Log;

/**
 * Class CommonController
 * @package App\Http\Controllers
 * @author Cookie
 */
class CommonController extends Controller
{
    /**
     * @return Alias|Builder|Model|object|null
     * @throws EntityException
     */
    public function id()
    {
        try {
            return $this->entity()?->id;
        } catch (EntityException $exception) {
            if ($alias = Alias::where([
                'entity'        => request('entity'),
                'original_slug' => request('slug'),
            ])->orderByDesc('id')->first()) {
                return $alias;
            } else {
                throw $exception;
            }
        }
    }

    /**
     * @return mixed
     * @throws EntityException
     */
    public function entity()
    {
        return get_entity_by_slug(request('entity'), request('slug'));
    }

    /**
     * @return Creator[]|HttpResponse
     */
    public function search(): HttpResponse|array
    {
        if (!strlen($query = request('query'))) {
            return error(5005);
        }

        $scopes = explode(",", request('scope'));

        // sort by computed priority
        $totalList = [];

        if (in_array("creators", $scopes)) {
            $creators = Creator::search($query, 200)->cursor();
            /** @var Creator $creator */
            foreach ($creators as $creator) {
                $creator->load('image', 'info', 'sources');
                $totalList[] = [
                    'priority_index' => $creator->priority,
                    'entity_type'    => 'Creator',
                    'data'           => $creator,
                ];
            }
        }

        if (in_array("collabs", $scopes)) {
            $collabs = Collab::search($query, 200)->cursor();
            /** @var Collab $collab */
            foreach ($collabs as $collab) {
                $collab->load('creator1.image', 'creator2.image', 'creator1.info', 'creator2.info');
                $collab->loadCount('userLikes');
                $totalList[] = [
                    'priority_index' => $collab->likes_count * 10000,
                    'entity_type'    => 'Collab',
                    'data'           => $collab,
                ];
            }
        }

        if (in_array("users", $scopes)) {
            $users = User::search($query, 200)
                ->select(["users.*", \DB::raw("count(likes.id) as likes_count")])
                ->leftJoin("collabs", "users.id","=","collabs.user_id")
                ->leftJoin("likes", "collabs.id","=","likes.collab_id")
                ->groupBy("users.id")
                ->cursor();

            /** @var User $user */
            foreach ($users as $user) {
                $user->load('image');
                $user->makeHidden("fusion_data", "email");
                $totalList[] = [
                    'priority_index' => $user->likes_count,
                    'entity_type'    => 'user',
                    'data'           => $user,
                ];
            }
        }

        usort($totalList, fn($a, $b) => -($a['priority_index'] <=> $b['priority_index']));

        return $totalList;
    }

    /**
     * @return array
     */
    #[ArrayShape(['creators' => "\Illuminate\Support\Collection",
                  'collabs'  => "\Illuminate\Support\Collection",
                  'users'    => "\Illuminate\Support\Collection"])]
    public function slugs()
    {
        return [
            'creators' => Creator::pluck('slug'),
            'collabs'  => Collab::pluck('slug'),
            'users'    => User::pluck('slug'),
            'sources'  => Source::pluck('slug'),
        ];
    }

    /**
     * @return Alias[]|Builder[]|Collection
     */
    public function alias(): Collection
    {
        return Alias::where([
            'entity'        => request('entity'),
            'original_slug' => request('slug'),
        ])->orderByDesc('id')->get();
    }
}
