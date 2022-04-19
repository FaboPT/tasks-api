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
        return $this->success(null, Response::HTTP_OK, TaskResource::collection($this->taskRepository->all())->resource, 'tasks');
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
            $this->sendNotification($task);
            DB::commit();
            return $this->success($this->messagePerformed($task));

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

    /**
     * @throws Exception
     */
    private function sendNotification($task): void
    {
        $this->instanceOfTask($task) ?
            $this->createNotificationWhenTechnicianUser($task) :
            throw new Exception("Access Denied", Response::HTTP_UNAUTHORIZED);
    }

    private function instanceOfTask($task): bool
    {
        return $task instanceof Task;
    }

    private function createNotificationWhenTechnicianUser($task): void
    {
        if (Auth::user()->hasRole('Technician'))
            Notification::send($this->getManagers(), new TaskPerformed($task));
    }

    private function messagePerformed($task): string
    {
        return $task->status === 1 ? 'Task successfully performed' : 'Task successfully not performed';
    }
}
