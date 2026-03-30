<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    // 1. Create Task
    public function store(Request $request)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('tasks')->where(function ($query) use ($request) {
                    return $query->where('due_date', $request->due_date);
                })
            ],
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:low,medium,high'
        ]);

        $task = Task::create([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'status' => 'pending'
        ]);

        return response()->json($task, 201);
    }

    // 2. List Tasks
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $tasks = $query
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc')
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found'], 404);
        }

        return response()->json($tasks);
    }

    // 3. Update Status
    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,in_progress,done'
        ]);

        $validTransitions = [
            'pending' => 'in_progress',
            'in_progress' => 'done'
        ];

        if (!isset($validTransitions[$task->status]) ||
            $validTransitions[$task->status] !== $request->status) {
            return response()->json(['error' => 'Invalid status transition'], 400);
        }

        $task->update(['status' => $request->status]);

        return response()->json($task);
    }

    // 4. Delete Task
    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if ($task->status !== 'done') {
            return response()->json(['error' => 'Only done tasks can be deleted'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }

    // 5. Daily Report (Bonus)
    public function report(Request $request)
    {
        $date = $request->query('date');

        $tasks = Task::whereDate('due_date', $date)->get();

        $priorities = ['high', 'medium', 'low'];
        $statuses = ['pending', 'in_progress', 'done'];

        $summary = [];

        foreach ($priorities as $priority) {
            foreach ($statuses as $status) {
                $summary[$priority][$status] = $tasks
                    ->where('priority', $priority)
                    ->where('status', $status)
                    ->count();
            }
        }

        return response()->json([
            'date' => $date,
            'summary' => $summary
        ]);
    }
}