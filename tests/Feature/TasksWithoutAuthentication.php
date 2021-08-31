<?php

namespace Tests\Feature;

use App\Models\Task;
use Carbon\Carbon;
use Tests\TestCase;

class TasksWithoutAuthentication extends TestCase
{
    public function test_get_tasks_without_authentication()
    {
        $response = $this->json('get',route('task.index'));

        $response->assertJson(["message"=>"Unauthorized."])->assertStatus(401);
    }

    public function test_store_tasks_without_authentication()
    {
        $data[] = [
            'name'=>'Task test',
        ];
        $response = $this->post(route('task.store'),$data);

        $response->assertJson(["message"=>"Unauthorized."])->assertStatus(401);
    }

    public function test_update_tasks_without_authentication()
    {
        $task = Task::findOrFail(1);
        $data[] = [
            'name'=>'Task test 2',
        ];
        $response = $this->put(route('task.update', $task),$data);

        $response->assertJson(["message"=>"Unauthorized."])->assertUnauthorized();
    }

    public function test_set_performed_tasks_without_authentication()
    {
        $task = Task::findOrFail(1);
        $data[] = [
            'status_id'=>1,
            'performed_at'=>Carbon::now(),
        ];
        $response = $this->put(route('task.setStatus', $task),$data);

        $response->assertJson(["message"=>"Unauthorized."])->assertUnauthorized();
    }
    public function test_destroy_tasks_without_authentication()
    {
        $task = Task::findOrFail(1);

        $response = $this->delete(route('task.destroy', $task));

        $response->assertJson(["message"=>"Unauthorized."])->assertStatus(401);
    }
}
