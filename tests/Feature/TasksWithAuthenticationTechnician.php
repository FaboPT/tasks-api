<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TasksWithAuthenticationTechnician extends TestCase
{
    public function testGetAllTasksWithAuthenticationTechnician()
    {
        $this->actingAs($this->user_technician(), 'api');
        $response = $this->json('get', route('task.index'), [], $this->headers());

        $response->assertJsonFragment(['success' => true])->assertSuccessful();

        $this->user_technician()->tokens()->delete();
    }

    public function testStoreTasksWithAuthenticationTechnician()
    {
        $this->actingAs($this->user_technician(), 'api');
        $data = [
            'summary' => 'Task test',
        ];

        $response = $this->post(route('task.store'), $data, $this->headers());

        $response->assertJsonFragment(['success' => true])->assertCreated();

        $this->user_technician()->tokens()->delete();
    }

    public function testUpdateTasksWithAuthenticationTechnician()
    {
        $this->actingAs($this->user_technician(), 'api');
        $task = Task::MyTasks(Auth::user()->getAuthIdentifier())->first();
        $data = [
            'summary' => 'Task test 2',
        ];
        $response = $this->put(route('task.update', $task->id), $data, $this->headers());

        $response->assertJsonFragment(['success' => true])->assertSuccessful();

        $this->user_technician()->tokens()->delete();
    }

    public function testSetPerformedTasksWithAuthenticationTechnician()
    {
        $this->actingAs($this->user_technician(), 'api');
        $task = Task::MyTasks(Auth::user()->getAuthIdentifier())->first();
        $response = $this->put(route('task.set_performed', $task), [], $this->headers());

        $response->assertJsonFragment(['success' => true])->assertSuccessful();
        $this->user_technician()->tokens()->delete();
    }

    public function testDestroyTasksWithAuthenticationTechnician()
    {
        $this->actingAs($this->user_technician(), 'api');
        $task = Task::MyTasks(Auth::user()->getAuthIdentifier())->first();

        $response = $this->delete(route('task.destroy', $task->id), [], $this->headers());

        $response->assertJsonFragment(['success' => false])->assertForbidden();
        $this->user_technician()->tokens()->delete();

    }

    private function user_technician(): User
    {
        $user = User::firstOrNew(['email' => 'test-technician@email.com']);
        $user->name = 'Test Technician';
        $user->password = bcrypt('secret12345?');
        $user->save();
        $user->assignRole('Technician');

        return $user;

    }

    private function headers(): array
    {
        return ['Authorization' => 'Bearer ' . $this->user_technician()->createToken('test token technician')->plainTextToken];

    }
}
