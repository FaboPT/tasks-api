<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;
use Tests\TestCase;

class TasksWithAuthenticationManager extends TestCase
{
    private function user_manager()
    {
        $user = User::firstOrNew(['email'=>'test-manager@email.com']);
        $user->name = 'Test Manager';
        $user->password = bcrypt('secret12345?');
        $user->save();
        $user->assignRole('Manager');
        return $user;

    }

    #[ArrayShape(['Authorization' => "string"])] private function headers():array{
        return ['Authorization' => 'Bearer '.$this->user_manager()->createToken('test token technician')->plainTextToken];

    }

    public function test_get_all_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager(), 'api');
        $response = $this->get(route('task.index'),$this->headers());

        $response->assertJsonFragment(["success" => true])->assertSuccessful();

        $this->user_manager()->tokens()->delete();
    }

    public function test_store_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $data = [
            'summary'=>'Task test Manager',
        ];


        $response = $this->post(route('task.store'),$data,$this->headers());

        $response->assertJsonFragment(["success"=>true])->assertCreated();

        $this->user_manager()->tokens()->delete();
    }


    public function test_update_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();
        $data = [
            'summary'=>'Task test Manager 2',
        ];
        $response = $this->put(route('task.update', $task->id),$data,$this->headers());

        $response->assertJsonFragment(["success"=>true])->assertSuccessful();

        $this->user_manager()->tokens()->delete();
    }

    public function test_set_performed_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();
        $response = $this->put(route('task.set_performed', $task),[],$this->headers());

        $response->assertJsonFragment(["success"=>true])->assertSuccessful();
        $this->user_manager()->tokens()->delete();

    }

    public function test_destroy_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();

        $response = $this->delete(route('task.destroy', $task->id),[],$this->headers());

        $response->assertJsonFragment(["success"=>true])->assertSuccessful();
        $this->user_manager()->tokens()->delete();
    }
}
