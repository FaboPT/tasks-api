<?php

namespace App\Repositories;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class TaskRepository extends BaseRepository
{
    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }


    public function all(): LengthAwarePaginator
    {
        return $this->isTechnician() ? $this->getTasksTechnician() : $this->getTasksManager();
    }

    public function store(array $attributes): Model
    {
        return $this->task->create($attributes);
    }

    public function destroy(int $id): bool
    {
        return $this->task->findOrFail($id)->delete();

    }

    public function setPerformed(int $id): Model|bool
    {
        $task = $this->task->findOrfail($id);
        $attributes = [
            'status' => $task->status === 0 ? 1 : 0,
            'performed_at' => $task->performed_at ? null : Carbon::now(),
        ];
        return $this->update($id, $attributes);
    }

    public function update(int $id, array $attributes): Model|bool
    {
        $task = $this->task->findOrFail($id);
        $task->update($attributes);
        return $task;
    }

    private function isTechnician(): bool
    {
        return Auth::user()->hasRole('Technician');
    }

    private function getTasksTechnician(): LengthAwarePaginator
    {
        return $this->task->with('user.roles')->myTasks(Auth::user()->getAuthIdentifier())->paginate();
    }

    private function getTasksManager(): LengthAwarePaginator
    {
        return $this->task->with('user.roles')->MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->paginate()->get();
    }

    private function havePermissions(Task $task): bool
    {
        return (Auth::user()->hasRole('Manager') && $task->user_id === Auth::user()->getAuthIdentifier()) || (Auth::user()->hasRole('Manager') && $task->user->hasRole('Technician'));

    }

}
