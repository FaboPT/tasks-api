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
use Symfony\Component\HttpFoundation\Response;

class TaskService
{
    use ResponseAPI;

    protected TaskRepository $task_repository;

    /**
     * Construct
     * @param TaskRepository $task_repository
     */
    public function __construct(TaskRepository $task_repository)
    {
        $this->task_repository = $task_repository;
    }

    /**
     * Service get all tasks
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        $tasks = TaskResource::collection($this->task_repository->all());
        return $this->success(null, Response::HTTP_OK, $tasks, 'tasks');
    }

    /**
     * Service store new task
     * @param array $data
     * @return JsonResponse
     */
    public function store(array $data): JsonResponse
    {
        DB::beginTransaction();
        try {
                $this->task_repository->store($data);
            DB::commit();
                return $this->success('Task successfully created', Response::HTTP_CREATED);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return $this->error($e->getMessage(), $e->getCode() ?: Response::HTTP_BAD_REQUEST);
        }

    }

    /** Service update task
     * @param int $id
     * @param array $data
     * @return JsonResponse
     */
    public function update(int $id, array $data): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->task_repository->update($id, $data);
            DB::commit();
            return $this->success('Task successfully updated');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return $this->error($e->getMessage(), $e->getCode() ?: Response::HTTP_BAD_REQUEST);

        }

    }

    /**
     * Service delete task
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->task_repository->destroy($id);
            DB::commit();
            return $this->success('Task successfully deleted');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return $this->error($e->getMessage(), $e->getCode() ?: Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Service performed the task
     * @param int $id
     * @return JsonResponse
     */
    public
    function set_performed(int $id): JsonResponse
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
            throw new \Exception("Access Denied", Response::HTTP_FORBIDDEN);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return $this->error($e->getMessage(), $e->getCode() ?: Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Private method used to return all managers
     * @return Collection
     */
    private function get_managers(): Collection
    {
        return User::WhereHas('roles', function ($query) {
            $query->where('name', 'Manager');
        })->get();
    }
}
