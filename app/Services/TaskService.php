<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskService
{
    protected TaskRepository $task_repository;

    public function __construct(TaskRepository $task_repository)
    {
        $this->task_repository = $task_repository;
    }

    public function all(): Collection
    {
        return $this->task_repository->all();
    }

    public function store(array $data): JsonResponse
    {
        DB::beginTransaction();
        try {
            $task = $this->task_repository->store($data);
            if ($task) {
                DB::commit();
                return response()->json(['message' => 'Task successfully created', 'success' => true], 201);
            }
            throw new \Exception("Not possible store a task", 400);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], empty($e->getCode()) ? 400 : $e->getCode());
        }

    }

    public function update(int $id, array $data): bool
    {
        return $this->task_repository->update($id, $data);
    }

    public function destroy(int $id): bool
    {
        return $this->task_repository->destroy($id);
    }

    public function set_performed(int $id): Model|bool
    {
        return $this->task_repository->set_performed($id);
    }
}
