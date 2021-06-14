<?php

namespace App\Http\Middleware;

use Closure;
use Cloudinary;
use Illuminate\Http\Request;

/**
 * Class CloudinaryMiddleware
 * @package App\Http\Middleware
 * @deprecated
 */
class CloudinaryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @deprecated
     */
    public function handle(Request $request, Closure $next)
    {
        Cloudinary::config(array(
            "cloud_name" => config('cloudinary.name'),
            "api_key"    => config('cloudinary.api_key'),
            "api_secret" => config('cloudinary.secret'),
            "secure"     => true
        ));

        return $next($request);
    }
}
