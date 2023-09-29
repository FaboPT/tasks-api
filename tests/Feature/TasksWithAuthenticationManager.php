<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TasksWithAuthenticationManager extends TestCase
{
    public function testGetAllTasksWithAuthenticationManager()
    {
        $this->actingAs($this->user_manager(), 'api');
        $response = $this->get(route('task.index'), $this->headers());

        $response->assertJsonFragment(['success' => true])->assertSuccessful();

        $this->user_manager()->tokens()->delete();
    }

    public function testStoreTasksWithAuthenticationManager()
    {
        $this->actingAs($this->user_manager());
        $data = [
            'summary' => 'Task test Manager',
        ];

        $response = $this->post(route('task.store'), $data, $this->headers());

        $response->assertJsonFragment(['success' => true])->assertCreated();

        $this->user_manager()->tokens()->delete();
    }

    public function testUpdateTasksWithAuthenticationManager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();
        $data = [
            'summary' => 'Task test Manager 2',
        ];
        $response = $this->put(route('task.update', $task->id), $data, $this->headers());

        $response->assertJsonFragment(['success' => true])->assertSuccessful();

        $this->user_manager()->tokens()->delete();
    }

    public function testSetPerformedTasksWithAuthenticationManager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();
        $response = $this->put(route('task.set_performed', $task), [], $this->headers());

        $response->assertJsonFragment(['success' => true])->assertSuccessful();
        $this->user_manager()->tokens()->delete();

    }

    public function testDestroyTasksWithAuthenticationManager()
    {
        $this->actingAs($this->user_manager());
        $task = Task::MyTasksManagerWithTechnicianTasks(Auth::user()->getAuthIdentifier())->first();

        $response = $this->delete(route('task.destroy', $task->id), [], $this->headers());

        $response->assertJsonFragment(['success' => true])->assertSuccessful();
        $this->user_manager()->tokens()->delete();
    }

    private function user_manager()
    {
        $user = User::firstOrNew(['email' => 'test-manager@email.com']);
        $user->name = 'Test Manager';
        $user->password = bcrypt('secret12345?');
        $user->save();
        $user->assignRole('Manager');

        return $user;

    }

    private function headers(): array
    {
        return ['Authorization' => 'Bearer ' . $this->user_manager()->createToken('test token technician')->plainTextToken];

    }
}
