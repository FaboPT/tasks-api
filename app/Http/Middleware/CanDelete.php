<?php

namespace App\Http\Middleware;

use App\Models\Task;
use App\Traits\ResponseAPI;
use Closure;
use Illuminate\Http\Request;

class CanDelete
{
    use ResponseAPI;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->hasRole('Technician')) {
            return $this->error('Access Denied', 403);
        }
        $task = Task::find($request->id);

        if ($task->user_id === $request->user()->getAuthIdentifier() || $task->user->hasRole('Technician')) {
            return $next($request);
        }

        return $this->error('Access Denied', 403);

    }
}
