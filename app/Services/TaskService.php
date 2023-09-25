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
     * @return TaskResource
     */
    public function all(): TaskResource
    {
        return new TaskResource($this->taskRepository->all(), 'Tasks successfully received', 'tasks');
    }

    /**
     * Service store new task
     * @param array $data
     * @return JsonResponse
     */
    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(fn() => $this->taskRepository->store($data));

            return $this->success('Task successfully created', Response::HTTP_CREATED);
        } catch (Exception $e) {
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
        try {
            DB::transaction(fn() => $this->taskRepository->update($id, $data));

            return $this->success('Task successfully updated');
        } catch (Exception $e) {
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
        try {
            DB::transaction(fn() => $this->taskRepository->destroy($id));

            return $this->success('Task successfully deleted');
        } catch (Exception $e) {
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
        $task = DB::transaction(function () use (&$id) {
            $task = $this->taskRepository->setPerformed($id);
            $this->sendNotification($task);

            return $task;
        });

        return $this->success($this->messagePerformed($task));
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
        if (Auth::user()?->hasRole('Technician'))
            Notification::send($this->getManagers(), new TaskPerformed($task));
    }

    private function messagePerformed($task): string
    {
        return $task->status === 1 ? 'Task successfully performed' : 'Task successfully not performed';
    }
}
