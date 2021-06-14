<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 * @author  Cookie
 */
class CategoryController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * @return Category|Model
     */
    public function create()
    {
        $category = Category::create(request()->all());

        $category->created_by_id = auth()->id();
        $category->save();

        return $category;
    }
}
