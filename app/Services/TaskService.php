<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    protected $taskRepository;
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function all(): Collection
    {
        return $this->taskRepository->all();
    }

    public function store(array $data): Model
    {
        return $this->taskRepository->store($data);
    }

    public function update(int $id,array $data): bool
    {
        return $this->taskRepository->update($id,$data);
    }

    public function destroy(int $id): bool
    {
        return $this->taskRepository->destroy($id);
    }

    public function setPerformed(int $id): Model | bool{
        return $this->taskRepository->setPerformed($id);
    }
}
