<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Source;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 * @author  Cookie
 */
class SourceController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return Source::all();
    }
}
