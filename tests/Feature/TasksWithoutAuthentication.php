<?php

namespace Tests\Feature;

use App\Models\Task;
use Carbon\Carbon;
use Tests\TestCase;

class TasksWithoutAuthentication extends TestCase
{
    public function test_get_tasks_without_authentication()
    {
        $response = $this->get(route('task.index'));

        $response->assertRedirect('/login');
    }

    public function test_store_tasks_without_authentication()
    {
        $data[] = [
            'name'=>'Task test',
        ];
        $response = $this->post(route('task.store'),$data);

        $response->assertRedirect(route('login'));
    }

    public function test_edit_tasks_without_authentication()
    {
        $task = Task::findOrFail(1);
        $response = $this->get(route('task.edit', $task->id));

        $response->assertRedirect(route('login'));
    }

    public function test_update_tasks_without_authentication()
    {
        $task = Task::findOrFail(1);
        $data[] = [
            'name'=>'Task test 2',
        ];
        $response = $this->put(route('task.update', $task),$data);

        $response->assertRedirect(route('login'));
    }

    public function test_set_status_tasks_without_authentication()
    {
        $task = Task::findOrFail(1);
        $data[] = [
            'status_id'=>1,
            'performed_at'=>Carbon::now(),
        ];
        $response = $this->put(route('task.setStatus', $task),$data);

        $response->assertRedirect(route('login'));
    }
    public function test_destroy_tasks_without_authentication()
    {
        $task = Task::findOrFail(1);

        $response = $this->delete(route('task.destroy', $task));

        $response->assertRedirect(route('login'));
    }
}
