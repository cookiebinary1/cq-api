<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityException;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers
 * @author  Cookie
 */
class UserController extends Controller
{
    /**
     * @return Model|Collection|array|User|null
     * @throws EntityException
     */
    public function __invoke(): Model|Collection|array|User|null
    {
        return get_entity(User::class, request()->validate(['id' => 'required|integer'])['id'])
            ?->makeHidden('fusion_data', 'fusion_id', 'email')
            ?->load('image')
            ?->load('country');
    }
    // test2

    /**
     * @return Collection|array|User[]
     */
    public function search()
    {
        return User::search(request()->validate(['query' => 'required'])['query'])->get();
    }
}
