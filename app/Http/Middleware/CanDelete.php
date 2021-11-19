<?php

namespace App\Http\Middleware;

use App\Models\Task;
use App\Traits\ResponseAPI;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanDelete
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

        if ($this->isDeletable($request,$task)) {
            return $next($request);
        }

        return $this->error('Access Denied', Response::HTTP_FORBIDDEN);

    }

    private function isDeletable(Request $request, Task $task): bool {
        return $request->user()->hasRole('Manager') && ($task->user_id === $request->user()->getAuthIdentifier() || $task->user->hasRole('Technician'));
    }
}
