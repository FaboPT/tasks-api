<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TasksWithAuthenticationManager extends TestCase
{
    private function user_manager()
    {
        $user = User::firstOrNew(['email'=>'test-manager@email.com']);
        $user->name = 'Test Manager';
        $user->password = 'secret12345?';
        $user->save();
        $user->assignRole('Manager');
        return $user;

    }

    public function test_access_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $response = $this->get(route('task.index'));

        $response->assertViewIs('tasks.index')->assertSuccessful();
    }

    public function test_store_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $data = [
            'name'=>'Task test',
            'user_id'=>Auth::user()->getAuthIdentifier()
        ];


        $response = $this->post(route('task.store'),$data);

        $response->assertRedirect(route('task.index'))->assertStatus(302);
    }

    public function test_edit_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();
        $response = $this->get(route('task.edit',$task->id));


        $response->assertViewIs('tasks.edit')->assertSuccessful();
    }

    public function test_update_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();
        $data = [
            'name'=>'Task test 2',
        ];
        $response = $this->put(route('task.update', $task->id),$data);

        $response->assertRedirect(route('task.index'))->assertStatus(302);
    }

    public function test_set_status_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();
        $data = [
            'status'=>$task->status === 0 ? 1 : 0,
            'performed_at'=>$task->performed_at ? null : Carbon::now(),
        ];
        $response = $this->put(route('task.setStatus', $task),$data);

        $response->assertSuccessful();
    }
    public function test_destroy_tasks_with_authentication_manager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();

        $response = $this->delete(route('task.destroy', $task->id));

        $response->assertSuccessful();
    }
}
