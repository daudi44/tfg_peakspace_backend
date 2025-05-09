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

        $activeEntry = $request->user()->timeEntries()->whereNull('end_time')->first();
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

        $activeEntry = $request->user()->timeEntries()->whereNull('end_time')->first();

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
    public function deleteTimeEntry(Request $request)
    {
        $request->validate([
            'time_entry_id' => 'required|exists:time_entries,id',
        ]);

        $timeEntry = $request->user()->timeEntries()->find($request->time_entry_id);

        if (!$timeEntry) {
            return response()->json(['error' => 'Time entry not found.'], 404);
        }

        $timeEntry->delete();

        return response()->json([
            'message' => 'Time entry deleted successfully',
        ], 200);
    }
    // get time entries (paginated or    something similar)
    public function getTimeEntries(Request $request)
    {
        $timeEntries = $request->user()->timeEntries()->with('registrable')->orderBy('created_at', 'desc')->limit(50)->get();

        return response()->json($timeEntries);
    }
    public function getLastTimeEntry(Request $request)
    {
        $lastTimeEntry = $request->user()->timeEntries()->orderBy('created_at', 'desc')->first();

        return response()->json($lastTimeEntry);
    }
    // get total time by period
    public function getTotalTime(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $totalTime = $request->user()->timeEntries()
            ->whereBetween('start_time', [$request->start_date, $request->end_date])
            ->get()
            ->sum(function ($te) {
                return $te->seconds_elapsed;
            });

        return response()->json(['total_time' => $totalTime]);
    }
}
