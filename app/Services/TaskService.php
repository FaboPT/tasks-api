<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskPerformed;
use App\Repositories\TaskRepository;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TaskService
{
    use ResponseAPI;

    protected TaskRepository $taskRepository;

    /**
     * Construct
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Service get all tasks
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        $tasks = TaskResource::collection($this->taskRepository->all());
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
            $this->taskRepository->store($data);
            DB::commit();
            return $this->success('Task successfully created', Response::HTTP_CREATED);

        } catch (Throwable $e) {
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
            $this->taskRepository->update($id, $data);
            DB::commit();
            return $this->success('Task successfully updated');

        } catch (Throwable $e) {
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
            $this->taskRepository->destroy($id);
            DB::commit();
            return $this->success('Task successfully deleted');

        } catch (Throwable $e) {
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
    public function setPerformed(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $task = $this->taskRepository->setPerformed($id);
            if ($task instanceof Task) {
                if (Auth::user()->hasRole('Technician'))
                    Notification::send($this->getManagers(), new TaskPerformed($task));
                DB::commit();
                return $this->success('Task successfully performed');
            }
            throw new Exception("Access Denied", Response::HTTP_FORBIDDEN);

        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            return $this->error($e->getMessage(), $e->getCode() ?: Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Private method used to return all managers
     * @return Collection
     */
    private function getManagers(): Collection
    {
        return User::WhereHas('roles', function ($query) {
            $query->where('name', 'Manager');
        })->get();
    }
}
