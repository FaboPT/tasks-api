<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): TaskResource
    {
        return $this->taskService->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): JsonResponse
    {
        $request->merge(['user_id' => Auth::user()->getAuthIdentifier()]);

        return $this->taskService->store($request->all());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, int $id): JsonResponse
    {
        return $this->taskService->update($id, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {

        return $this->taskService->destroy($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function setPerformed(int $id): JsonResponse
    {
        return $this->taskService->setPerformed($id);
    }
}
