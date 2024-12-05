<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\FilterRequest;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'admin') {
            $tasks = Task::all();
        } else {
            $tasks = Task::where('assigned_to', auth()->id())->get();
        }

        return response()->json($tasks);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $task->assigned_to !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($task);
    }

    public function create(StoreRequest $request)
    {
        $user=User::where('name',$request->name_to_assigned)->first();
        if($user==null)
            return response()->json(['error' => 'user did not exist'], 403);

        $task = Task::create([
            'task_name' => $request->task_name,
            'description' => $request->description,
            'assigned_to' => $user->id,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);
        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ]);
    }
    public function update(UpdateTaskRequest $request)
    {
        $task = Task::where('id',$request->id)->first();
        if ($task==null){
            return response()->json(['error' => 'id is wrong'], 403);
        }
        $task->update($request->only([
            'task_name',
            'description',
            'assigned_to',
            'due_date',
            'status',
        ]));
        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ]);

    }
    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $task->assigned_to !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function filter(FilterRequest $request)
    {
        $tasks = Task::when(
                $request->has('status'),
                fn($q) => $q->where('status', $request->status)
            )
            ->when(
                $request->has('assigned_to'),
                fn($q) => $q->where('assigned_to', $request->assigned_to)
            )
            ->when(
                $request->has('start_time') && $request->has('end_time'),
                fn($q) => $q->whereBetween('due_date', [
                    Carbon::parse($request->start_time)->startOfDay(),
                    Carbon::parse($request->end_time)->endOfDay()
                ])
            )
            ->when(
                $request->has('start_time') && !$request->has('end_time'),
                fn($q) => $q->where('due_date', '>=', Carbon::parse($request->start_time)->startOfDay())
            )
            ->when(
                !$request->has('start_time') && $request->has('end_time'),
                fn($q) => $q->where('due_date', '<=', Carbon::parse($request->end_time)->endOfDay())
            )
            ->latest()
            ->paginate($request->has('count') ? $request->count : 15);

        return response()->json($tasks);
    }
}
