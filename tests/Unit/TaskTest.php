<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_can_be_created()
    {
        $task = Task::create([
            'title' => 'Finish report',
            'status' => 'Pending',
            'priority' => 'High',
            'due_date' => '2024-12-20',
        ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Finish report']);
    }
}
