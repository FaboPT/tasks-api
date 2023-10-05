<?php

declare(strict_types=1);

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository extends BaseRepository
{
    public function __construct(private readonly Task $task)
    {
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
        return $this->findById($id)->delete();

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

    /**
     * @param int $id
     *
     * @return Task
     */
    public function findById(int $id): Task
    {
        return $this->task->findOrFail($id);
    }

    private function isTechnician(User $user = null): bool
    {
        return $user ? $user->hasRole('Technician') : Auth::user()?->hasRole('Technician');
    }

    private function getTasksTechnician(): LengthAwarePaginator
    {
        return $this->task->with('user.roles')->myTasks(Auth::user()?->getAuthIdentifier())->paginate();
    }

    private function getTasksManager(): LengthAwarePaginator
    {
        //        dd($this->task->with('user.roles')->myTasksManagerWithTechnicianTasks(Auth::user()?->getAuthIdentifier())->paginate()->toArray());
        return $this->task->with('user.roles')->myTasksManagerWithTechnicianTasks(Auth::user()?->getAuthIdentifier())->paginate();
    }

    /*private function isManager(): bool
   {
       return Auth::user()?->hasRole('Manager');
   }*/

    /* private function havePermissions(Task $task): bool
     {
         return ($this->isManager() && $task->user_id === Auth::user()?->getAuthIdentifier()) || ($this->isManager() && $this->isTechnician($task->user));

     }*/
}
