<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

    public function store($data): Model
    {
        return $this->taskRepository->store($data);
    }

    public function edit(int $id): Task
    {
        return $this->taskRepository->edit($id);
    }


    public function update($id,$data): bool
    {
        return $this->taskRepository->update($id,$data);
    }

    public function destroy($id): bool
    {
        return $this->taskRepository->destroy($id);
    }

    public function setStatus($id,$data): bool{
        return $this->taskRepository->setStatus($id,$data);
    }
}
