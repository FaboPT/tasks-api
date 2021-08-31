<?php

namespace App\Repositories;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TaskRepository extends BaseRepository
{
    protected $task;
    public function __construct(Task $task)
    {
        $this->task = $task;
        parent::__construct($this->task);
    }


    public function all(): Collection{
        return Auth::user()->hasRole('Technician') ?
        $this->task->with('user')->myTasks(Auth::user()->getAuthIdentifier())->get() :
            $this->task->with('user')->MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->get();
    }

    public function store(array $attributes): Model
    {
        return $this->task->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {
        $task = $this->task->findOrFail($id);

        if($task->where('user_id',Auth::user()->getAuthIdentifier()) || (Auth::user()->hasRole('Manager') && $task->user()->hasRole('Technician'))) {
            return $task->update($attributes);
        }
        return false;
    }

    public function destroy(int $id): bool
    {
        $task = $this->task->findOrFail($id);

        if( (Auth::user()->hasRole('Manager') && $task->user_id === Auth::user()->getAuthIdentifier())  || (Auth::user()->hasRole('Manager') &&  $task->user->hasRole('Technician'))) {
            return $task->delete();
        }
        return false;
    }

    public function setPerformed(int $id): bool {
        $task = $this->task->findOrfail($id);
        $attributes = [
            'status'=>$task->status === 0 ? 1 : 0,
            'performed_at' => $task->performed_at ? null : Carbon::now(),
        ];
        return $this->update($id,$attributes);
    }
}
