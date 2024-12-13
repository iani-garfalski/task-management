<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Category;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Generate 20 tasks and associate them with random categories
        Task::factory()
            ->count(20)
            ->create()
            ->each(function ($task) {
                // Attach 1 to 3 random categories to each task
                $categories = Category::inRandomOrder()->take(rand(1, 3))->pluck('id');
                $task->categories()->attach($categories);
            });
    }
}
