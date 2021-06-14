<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityException;
use App\Models\Collab;
use App\Models\ExistingCollab;
use Illuminate\Http\Request;

/**
 * Class ExistingCollab
 * @package App\Http\Controllers
 * @author Cookie
 */
class ExistingCollabController extends Controller
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return ExistingCollab::whereCollabId(request('collab_id'))->get();
    }

    /**
     * @return mixed
     * @throws EntityException
     */
    public function post(): mixed
    {
        // just check collab id
        get_entity(Collab::class, request('collab_id'));

        return ExistingCollab::create(request()->only(["url", "collab_id"]));
    }
}
