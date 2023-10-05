<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Traits\ResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TaskResource;
use App\Notifications\TaskPerformed;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

class TaskService
{
    use ResponseAPI;

    /**
     * Construct.
     */
    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    /**
     * Service get all tasks.
     */
    public function all(): TaskResource
    {
        return new TaskResource($this->taskRepository->all(), 'Tasks successfully received', 'tasks');
    }

    /**
     * Service store new task.
     */
    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(fn () => $this->taskRepository->store($data));

            return $this->success('Task successfully created', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: Response::HTTP_BAD_REQUEST);
        }

    }

    /** Service update task.
     */
    public function update(int $id, array $data): JsonResponse
    {
        try {
            DB::transaction(fn () => $this->taskRepository->update($id, $data));

            return $this->success('Task successfully updated');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Service delete task.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            DB::transaction(fn () => $this->taskRepository->destroy($id));

            return $this->success('Task successfully deleted');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * Service performed the task.
     */
    public function setPerformed(int $id): JsonResponse
    {
        $task = DB::transaction(function() use (&$id) {
            $task = $this->taskRepository->setPerformed($id);
            $this->sendNotification($task);

            return $task;
        });

        return $this->success($this->messagePerformed($task));
    }

    /**
     * Private method used to return all managers.
     */
    private function getManagers(): Collection
    {
        return User::WhereHas('roles', static function($query) {
            $query->where('name', 'Manager');
        })->get();
    }

    /**
     * @param mixed $task
     *
     * @throws \Exception
     */
    private function sendNotification($task): void
    {
        $this->instanceOfTask($task) ?
            $this->createNotificationWhenTechnicianUser($task) :
            throw new \Exception('Access Denied', Response::HTTP_UNAUTHORIZED);
    }

    private function instanceOfTask($task): bool
    {
        return $task instanceof Task;
    }

    private function createNotificationWhenTechnicianUser($task): void
    {
        if (Auth::user()?->hasRole('Technician')) {
            Notification::send($this->getManagers(), new TaskPerformed($task));
        }
    }

    private function messagePerformed($task): string
    {
        return $task->status === 1 ? 'Task successfully performed' : 'Task successfully not performed';
    }
}
