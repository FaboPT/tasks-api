<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Task;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanEdit
{
    use ResponseAPI;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next)
    {
        $task = Task::find($request->id);

        if ($this->isEditable($request, $task)) {
            return $next($request);

        }

        return $this->error('Access Denied', Response::HTTP_FORBIDDEN);

    }

    private function isEditable(Request $request, Task $task): bool
    {
        return $task->user_id === $request->user()->getAuthIdentifier() || ($request->user()->hasRole('Manager') && $task->user->hasRole('Technician'));
    }
}
