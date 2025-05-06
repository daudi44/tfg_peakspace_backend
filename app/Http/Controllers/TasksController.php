<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Enums\TaskStatus;

class TasksController extends Controller
{
    // add task
    public function addTask(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'parent_task_id' => 'nullable|exists:tasks,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        $task = new Task();
        $task->name = $request->name;
        $task->description = $request->description;
        $task->status = TaskStatus::NOT_STARTED;
        $task->category_id = $request->category_id;
        $task->user_id = $request->user()->id; 
        $task->parent_task_id = $request->parent_task_id;
        $task->start_date = $request->start_date;
        $task->due_date = $request->due_date;
        $task->save();
        
        return response()->json([
            'message' => 'Task created successfully',
        ], 201);
    }
    // edit task
    public function deleteTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
        ]);

        $task = Task::find($request->task_id);
        if ($task) {
            $task->delete();
            return response()->json([
                'message' => 'Task deleted successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'Task not found',
        ], 404);
    }
    public function getAllTasks(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)->get();
        return response()->json($tasks);
    }
    public function getTasksByStatus(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)
            ->where('status', $request->status)
            ->with('category', 'subtasks')
            ->get();
        return response()->json($tasks);
    }
    public function updateTaskStatus(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'status' => 'required|in:' . implode(',', TaskStatus::getValues()),
        ]);

        $task = Task::find($request->task_id);
        if ($task) {
            $task->status = $request->status;
            $task->save();
            return response()->json([
                'message' => 'Task status updated successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'Task not found',
        ], 404);
    }
}
