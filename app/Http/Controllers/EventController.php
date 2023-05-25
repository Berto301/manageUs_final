<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EventController extends Controller
{
    public function lists(Request $request)
    {
        $user = Auth::user();
    	$tasks = Task::where('team_id', $user->currentTeam->id)->get();

    	$data = [
    		'user' => $user,
    		'tasks' => $tasks
    	];

        return Inertia::render('Calendar/Show', $data);
    }
}
