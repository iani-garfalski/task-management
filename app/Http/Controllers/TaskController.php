<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->whereIn('id', $request->category);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('due_before')) {
            $query->where('due_date', '<=', $request->due_before);
        }

        if ($request->filled('due_after')) {
            $query->where('due_date', '>=', $request->due_after);
        }

        return TaskResource::collection($query->paginate(10));
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  \App\Http\Requests\TaskStoreRequest  $request
     * @return \App\Http\Resources\TaskResource
     */
    public function store(TaskStoreRequest $request)
    {
        $validated = $request->validated();
        $task = Task::create($validated);

        if ($request->filled('categories')) {
            $task->categories()->sync($validated['categories']);
        }

        return new TaskResource($task->load('categories'));
    }

    /**
     * Display the specified task.
     *
     * @param  int  $id
     * @return \App\Http\Resources\TaskResource
     */
    public function show($id)
    {
        $task = Task::with('categories')->findOrFail($id);
        return new TaskResource($task);
    }

    /**
     * Update the specified task in storage.
     *
     * @param  \App\Http\Requests\TaskUpdateRequest  $request
     * @param  int  $id
     * @return \App\Http\Resources\TaskResource|\Illuminate\Http\JsonResponse
     */
    public function update(TaskUpdateRequest $request, $id)
    {
        $task = Task::findOrFail($id);
    
        // Check if the task is completed and prevent certain updates
        if ($task->status === 'Completed' && $request->status !== 'In Progress') {
            return response()->json(['error' => 'Completed tasks cannot be updated.'], 400);
        }
    
        $validated = $request->validated();
    
        // Only update fields that are present in the request
        $task->title = $validated['title'] ?? $task->title;
        $task->description = $validated['description'] ?? $task->description;
        $task->status = $validated['status'] ?? $task->status;
        $task->priority = $validated['priority'] ?? $task->priority;
        $task->due_date = $validated['due_date'] ?? $task->due_date;
    
        // Update the task in the database
        $task->save();
    
        // Update categories if provided
        if ($request->filled('categories')) {
            $task->categories()->sync($validated['categories']);
        }
    
        return new TaskResource($task->load('categories'));
    }
    
    /**
     * Remove the specified task from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }


    public function bulkComplete(Request $request)
    {
        $validated = $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        Task::whereIn('id', $validated['task_ids'])
            ->where('status', '<>', 'Completed')
            ->update(['status' => 'Completed']);

        return response()->json([
            'message' => 'Tasks marked as completed successfully.',
        ], 200);
    }
}
