<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeEntry;

class TimeEntriesController extends Controller
{
    // start time entry
    public function startTimeEntry(Request $request)
    {
        $request->validate([
            'task_id' => 'nullable|exists:tasks,id',
            'category_id' => 'nullable|exists:categories,id',
            'start_time' => 'required|date',
        ]);

        if (!$request->task_id && !$request->category_id) {
            return response()->json(['error' => 'You must provide either a task_id or a category_id.'], 422);
        }

        $user = $request->user();
        $activeEntry = $user->timeEntries()->whereNull('end_time')->first();
        if ($activeEntry) {
            $activeEntry->end_time = $request->start_time;
            $activeEntry->save();
        }

        $timeEntry = new TimeEntry();
        $timeEntry->start_time = $request->start_time;
        $timeEntry->user_id = $request->user()->id;

        if ($request->task_id) {
            $timeEntry->registrable_type = \App\Models\Task::class;
            $timeEntry->registrable_id = $request->task_id;
        } elseif ($request->category_id) {
            $timeEntry->registrable_type = \App\Models\Category::class;
            $timeEntry->registrable_id = $request->category_id;
        }

        $timeEntry->save();

        return response()->json([
            'message' => 'Time entry started successfully',
        ], 201);
    }

    // stop time entry
    public function stopTimeEntry(Request $request)
    {
        $request->validate([
            'end_time' => 'required|date',
        ]);

        $user = $request->user();
        $activeEntry = $user->timeEntries()->whereNull('end_time')->first();

        if (!$activeEntry) {
            return response()->json(['error' => 'No active time entry found.'], 404);
        }

        $activeEntry->end_time = $request->end_time;
        $activeEntry->save();

        return response()->json([
            'message' => 'Time entry stopped successfully',
        ], 200);
    }
    // edit time entry
    // delete time entry
    // get time entries (paginated or something similar)
    public function getTimeEntries(Request $request)
    {
        $user = $request->user();
        $timeEntries = $user->timeEntries()->with('registrable')->get();

        return response()->json($timeEntries);
    }
    public function getLastTimeEntry(Request $request)
    {
        $user = $request->user();
        $lastTimeEntry = $user->timeEntries()->orderBy('created_at', 'desc')->first();

        return response()->json($lastTimeEntry);
    }
    // get total time by period
}
