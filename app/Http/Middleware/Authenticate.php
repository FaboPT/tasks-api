<?php

namespace App\Http\Middleware;

use App\Traits\ResponseAPI;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Symfony\Component\HttpFoundation\Response;


class Authenticate extends Middleware
{
    use ResponseAPI;
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->guest()) {
                return $this->error("Unauthorized.", Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);
    }
}
