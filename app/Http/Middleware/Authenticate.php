<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * @param Request $request
     * @return string|null
     */
//    protected function redirectTo( \Illuminate\Http\Request $request)
//    {
//        if (!$request->expectsJson()) {
//            return route('login');
//        }
//    }
}
