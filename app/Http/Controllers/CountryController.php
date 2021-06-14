<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CountryController
 * @package App\Http\Controllers
 * @author Cookie
 */
class CountryController extends Controller
{
    /**
     * @return Country[]|Collection
     */
    public function index()
    {
        return Country::all();
    }
}
