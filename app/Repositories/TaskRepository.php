<?php

declare(strict_types=1);

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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

    public function myTasks(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function myTasksManagerWithTechnicianTasks(Builder $query, int $userid): Builder
    {
        return $query->where('user_id', $userid)->orWhereIn('user_id', User::role('Technician')->pluck('id')->toArray());
    }

    private function isTechnician(User $user = null): bool
    {
        return $user ? $user->hasRole('Technician') : Auth::user()?->hasRole('Technician');
    }

    private function getTasksTechnician(): LengthAwarePaginator
    {
        $query = $this->task->with('user.roles');
        $this->myTasks($query, $this->getAuthIdentifier());

        return $query->paginate();
    }

    private function getTasksManager(): LengthAwarePaginator
    {
        $query = $this->task->with('user.roles');
        $this->myTasksManagerWithTechnicianTasks($query, $this->getAuthIdentifier());

        return $query->paginate();

    }

    /*private function isManager(): bool
   {
       return Auth::user()?->hasRole('Manager');
   }*/

    /* private function havePermissions(Task $task): bool
     {
         return ($this->isManager() && $task->user_id === Auth::user()?->getAuthIdentifier()) || ($this->isManager() && $this->isTechnician($task->user));

     }*/
    /**
     * @return mixed|null
     */
    private function getAuthIdentifier(): mixed
    {
        return Auth::user()?->getAuthIdentifier();
    }

}
