<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_complete_tasks()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // Hash the password
        ]);

        // Generate a JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Create some tasks
        $tasks = Task::factory()->count(3)->create(['status' => 'Pending']);

        // Send a POST request to complete the tasks, passing the JWT token in the Authorization header
        $response = $this->postJson('/api/tasks/bulk-complete', [
            'task_ids' => $tasks->pluck('id')->toArray(),
        ], [
            'Authorization' => 'Bearer ' . $token, // Include the token in the Authorization header
        ]);

        // Assert the response status and message
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Tasks marked as completed successfully.']);

        // Assert that each task is marked as completed in the database
        foreach ($tasks as $task) {
            $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'Completed']);
        }
    }
}
