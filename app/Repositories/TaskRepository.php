<?php

namespace App\Repositories;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TaskRepository extends BaseRepository
{
    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
        parent::__construct($this->task);
    }


    public function all(): Collection
    {
        return $this->isTechnician() ? $this->getTasksTechnician() : $this->getTasksManager();
    }

    public function store(array $attributes): Model
    {
        return $this->task->create($attributes);
    }

    public function destroy(int $id): bool
    {
        $task = $this->task->findOrFail($id);

        return $task->delete();

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

    public function update(int $id, array $attributes): bool
    {
        $task = $this->task->findOrFail($id);
        return $task->update($attributes);
    }

    private function isTechnician(): bool
    {
        return Auth::user()->hasRole('Technician');
    }

    private function getTasksTechnician(): Collection
    {
        return $this->task->with('user.roles')->myTasks(Auth::user()->getAuthIdentifier())->get('user.name');
    }

    private function getTasksManager(): Collection
    {
        return $this->task->with('user.roles')->MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->get();
    }

    private function havePermissions(Task $task): bool
    {
        return (Auth::user()->hasRole('Manager') && $task->user_id === Auth::user()->getAuthIdentifier()) || (Auth::user()->hasRole('Manager') && $task->user->hasRole('Technician'));

    }

}
