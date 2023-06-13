<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Events;

class PageController extends Controller
{
    public function dashboard(Request $request)
{
    $user = Auth::user();
    $notifications = DB::table('notifications')
        ->select('*')
        ->where('group_id', $user->group_id)
        ->orderBy( DB::raw('DATE(created_at)'), 'desc')
        ->get();
    $teams = DB::table('teams')
        ->select('*')
        ->where('group_id', $user->group_id);
    $tasks = DB::table('tasks')
        ->select('*')
        ->where('group_id', $user->group_id);
        
    $users = DB::table('users')
        ->select('*')
        ->where('group_id', $user->group_id)
        ->count();

    $events = Events::where('group_id', $user->group_id)
        ->whereMonth('start', '=', date('m'))
        ->whereYear('start', '=', date('Y'))
        ->get();

    //statistics
    $labelsTasks = ['In progress', 'Complete'];
    $dataLabelsTasks = [0,0];
    $labels = [];
    $dataLabels = [];
    $backgroundColor = ['#3dc0df', '#a066cb', '#2737b2', '#43a046','#f88b02','#3dc6da','#f85b5b','#7f9cf5','#f9c851','#536de6','#6c757d']; // Couleurs de fond prédéfinies


    foreach ($teams->get() as $team) {
        $users = User::where('current_team_id', $team->id)->count();
        $labels[] = $team->name; // Ajouter le nom de l'équipe aux labels
        $dataLabels[] = $users; // Ajouter le nombre d'utilisateurs aux données
    }
    foreach ($tasks->get() as $task) {
        if ($task->is_complete) {
            $dataLabelsTasks[1]++; // Increment completed tasks count
        } else {
            $dataLabelsTasks[0]++; // Increment pending tasks count
        }
    }

    $datasetTask = [
        'backgroundColor' => ['#43a046','#f88b02'],
        'data' => $dataLabelsTasks,
    ];
    $resultTask = [
        'labels' => $labelsTasks,
        'datasets' => [$datasetTask],
    ];

    $dataset = [
        'backgroundColor' => $backgroundColor,
        'data' => $dataLabels,
    ];

    $result = [
        'labels' => $labels,
        'datasets' => [$dataset],
    ];
    $teamsPerUsers = json_decode(json_encode($result));
    $taskPerStatus = json_decode(json_encode($resultTask));
    $data = [
        'user' => $user,
        'notifications' => $notifications,
        'events'=>$events,
        'teams'=>$teams->count(),
        'users'=>$users,
        'tasks'=>$tasks->count(),
        'teamsPerUsers'=>$teamsPerUsers,
        'taskPerStatus'=>$taskPerStatus
    ];

    return Inertia::render('Dashboard/Show', $data);
}

    

}
