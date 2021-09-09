<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskPerformed;
use App\Repositories\TaskRepository;
use App\Traits\ResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TaskService
{
    use ResponseAPI;

    protected TaskRepository $task_repository;

    public function __construct(TaskRepository $task_repository)
    {
        $this->task_repository = $task_repository;
    }

    public function all(): JsonResponse
    {
        $tasks = TaskResource::collection($this->task_repository->all());
        return $this->success(null, 200, $tasks);
    }

    public function store(array $data): JsonResponse
    {
        DB::beginTransaction();
        try {
            $task = $this->task_repository->store($data);
            if ($task) {
                DB::commit();
                return $this->success('Task successfully created', 201);
            }
            throw new \Exception("Not possible store a task", 400);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), empty($e->getCode()) ? 400 : $e->getCode());
        }

    }

    /** Service update Task
     * @param int $id
     * @param array $data
     * @return JsonResponse
     */
    public function update(int $id, array $data): JsonResponse
    {
        DB::beginTransaction();
        try {
            $task = $this->task_repository->update($id, $data);
            if ($task) {
                DB::commit();
                return $this->success('Task successfully updated');
            }
            throw new \Exception("Access Denied", 403);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), empty($e->getCode()) ? 400 : $e->getCode());

        }

    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $task = $this->task_repository->destroy($id);
            if ($task) {
                DB::commit();
                return $this->success('Task successfully deleted');
            }
            throw new \Exception("Access Denied", 403);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), empty($e->getCode()) ? 400 : $e->getCode());
        }
    }

    public function set_performed(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $task = $this->task_repository->set_performed($id);
            if ($task instanceof Task) {
                if (Auth::user()->hasRole('Technician'))
                    Notification::send($this->get_managers(), new TaskPerformed($task));
                DB::commit();
                return $this->success('Task successfully performed');
            }
            throw new \Exception("Access Denied", 403);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), empty($e->getCode()) ? 400 : $e->getCode());
        }
    }

    private function get_managers(): Collection
    {
        return User::WhereHas('roles', function ($query) {
            $query->where('name', 'Manager');
        })->get();
    }
}
