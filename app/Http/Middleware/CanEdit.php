<?php

namespace App\Http\Middleware;

use App\Models\Task;
use App\Traits\ResponseAPI;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanEdit
{
    use ResponseAPI;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $task = Task::find($request->id);

        if ($task->user_id === $request->user()->getAuthIdentifier() || ($request->user()->hasRole('Manager') && $task->user->hasRole('Technician'))) {
            return $next($request);

        }
        return $this->error('Access Denied', Response::HTTP_FORBIDDEN);

    }
}
