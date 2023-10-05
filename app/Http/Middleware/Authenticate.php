<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Traits\ResponseAPI;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    use ResponseAPI;

    public function handle($request, \Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->guest()) {
                return $this->error('Unauthorized.', Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);
    }
}
