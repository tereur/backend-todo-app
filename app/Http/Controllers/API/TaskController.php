<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
    $tasks = Task::all();

    $tasksWithOwnerStatus = $tasks->map(function ($task) {
        $task->isMine = ($task->user_id == Auth::id());
        return $task;
    });

    return response()->json($tasksWithOwnerStatus);
    }

    public function store(StoreTaskRequest $request)
    {
        

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = "storage/".$request->file('image')->store('images', 'public');
        }

        $task = Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json($task, 201);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::findOrFail($id);
    
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $imagePath = $task->image;
        if ($request->hasFile('image')) {
            if ($imagePath && File::exists(public_path("storage/{$imagePath}"))) {
                File::delete(public_path("storage/{$imagePath}"));
            }
    
            $imagePath ="storage/". $request->file('image')->store('images', 'public');
        }
    
        $task->update([
            'name' => $request->input('name', $task->name),
            'description' => $request->input('description', $task->description),
            'image' => $imagePath,
            'latitude' => $request->input('latitude', $task->latitude),
            'longitude' => $request->input('longitude', $task->longitude),
        ]);

    
        return response()->json($task);
    }    

    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($task->image ) {
            File::delete($task->image);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }
}
