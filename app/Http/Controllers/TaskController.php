<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Notifications\TaskPerformed;
use Illuminate\Support\Facades\Notification;
use Ramsey\Collection\Collection;

class TaskController extends Controller
{
    private TaskService $task_service;

    public function __construct(TaskService $task_service)
    {
        $this->task_service = $task_service;
    }


    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tasks = TaskResource::collection($this->task_service->all());
        return response()->json(['data' => $tasks, 'success' => true]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param TaskRequest $request
     * @return JsonResponse
     */
    public function store(TaskRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->merge(['user_id' => Auth::user()->getAuthIdentifier()]);
            $task = $this->task_service->store($request->all());
            if ($task) {
                DB::commit();
                return response()->json(['message' => 'Task successfully created', 'success' => true], 201);
            }
            Throw new \Exception("Not possible store a task",400);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], empty($e->getCode()) ? 400 : $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TaskRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(TaskRequest $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $task = $this->task_service->update($id,$request->all());
            if($task){
                DB::commit();
                return response()->json(['message' => 'Task successfully updated', 'success' => true]);
            }
            Throw new \Exception("Access Denied",403);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], empty($e->getCode()) ? 400 : $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $task = $this->task_service->destroy($id);
            if($task){
                DB::commit();
                return response()->json(['message' => 'Task successfully deleted', 'success' => true]);
            }
            Throw new \Exception("Access Denied",403);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], empty($e->getCode()) ? 400 : $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function set_performed(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $task = $this->task_service->setPerformed($id);
            if($task instanceof Task){
                if(Auth::user()->hasRole('Technician'))
                    Notification::send($this->getManagers(),new TaskPerformed($task));

                DB::commit();
                return response()->json(['message' => 'Task successfully performed', 'success' => true]);
            }
            Throw new \Exception("Access Denied",403);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], empty($e->getCode()) ? 400 : $e->getCode());
        }
    }

    private function getManagers():\Illuminate\Support\Collection {
       return User::WhereHas('roles', function($query) {
            $query->where('name','Manager');
        })->get();
    }
}
