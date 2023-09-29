<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Task;
use Carbon\Carbon;
use Tests\TestCase;

class TasksWithoutAuthentication extends TestCase
{
    public function testGetAllTasksWithoutAuthentication()
    {
        $response = $this->json('get', route('task.index'));

        $response->assertJson(['message' => 'Unauthorized.'])->assertUnauthorized();
    }

    public function testStoreTasksWithoutAuthentication()
    {
        $data[] = [
            'name' => 'Task test',
        ];
        $response = $this->post(route('task.store'), $data);

        $response->assertJson(['message' => 'Unauthorized.'])->assertUnauthorized();
    }

    public function testUpdateTasksWithoutAuthentication()
    {
        $task = Task::findOrFail(1);
        $data[] = [
            'name' => 'Task test 2',
        ];
        $response = $this->put(route('task.update', $task), $data);

        $response->assertJson(['message' => 'Unauthorized.'])->assertUnauthorized();
    }

    public function testSetPerformedTasksWithoutAuthentication()
    {
        $task = Task::findOrFail(1);
        $data[] = [
            'status_id' => 1,
            'performed_at' => Carbon::now(),
        ];
        $response = $this->put(route('task.setStatus', $task), $data);

        $response->assertJson(['message' => 'Unauthorized.'])->assertUnauthorized();
    }

    public function testDestroyTasksWithoutAuthentication()
    {
        $task = Task::findOrFail(1);

        $response = $this->delete(route('task.destroy', $task));

        $response->assertJson(['message' => 'Unauthorized.'])->assertUnauthorized();
    }
}
