<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Notifications;

class TaskController extends Controller
{
    public function tasks(Request $request)
    {
       $user = Auth::user();
       $tasks =Task::where('creator_id', $user->id)->get(); 
       $notifications = DB::table('notifications')
       ->select('*')
       ->where('group_id', $user->group_id)
       ->orderBy( DB::raw('DATE(created_at)'), 'desc')
       ->get();
        $data = [
            'tasks' => $tasks,
            'user' => $user,
            "notifications"=>$notifications
        ];

        return Inertia::render('Tasks/Show', $data);
    }
    public function createTask(Request $request, Team $team)
    {
        $user = Auth::user();
        $task = Task::create([
            'team_id' => $team->id,
            'creator_id' => Auth::user()->id,
            'group_id'=>Auth::user()->group_id,
            'title' => $request->get('title'),
            'description' => $request->get('description'),
        ]);

        Notifications::forceCreate([
            'user_id' => $user->id,
            'group_id' => $user->group_id,
            'type' => 'new_task',
            'team_id' =>$team->id,
            'configuration'=> json_encode(['icon' => 'mdi-check-circle-outline', 'color' => 'bg-success']),
            'path' => '/tasks',
        ]);

        return response()
            ->json([
                'task' => $task,
            ], Response::HTTP_CREATED);
    }

    public function patchTask(Request $request, Task $task)
    {
        $data = $request->all();

        if (isset($data['is_complete']))
        {
            $task->is_complete = $data['is_complete'];
        }

        if (isset($data['title']))
        {
            $task->title = $data['title'];
        }

        if (isset($data['description']))
        {
            $task->description = $data['description'];
        }
        $task->save();

        return response()
            ->json([
                'task' => $task,
            ], Response::HTTP_OK);
    }


    public function deleteTask(Task $task)
    {
        $task->delete();

        return response()
            ->json([
                'task_id' => $task->id
            ], Response::HTTP_OK);


    }

}

