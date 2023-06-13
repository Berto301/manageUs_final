<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Models\Events;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function events(Request $request)
    {
       $user = Auth::user();
       $notifications = DB::table('notifications')
       ->select('*')
       ->where('group_id', $user->group_id)
       ->orderBy( DB::raw('DATE(created_at)'), 'desc')
       ->get();
       $events =Events::where('group_id', $user->group_id)->get(); 
        $data = [
            'events' => $events,
            "notifications"=>$notifications
        ];

        return Inertia::render('Calendar/Show', $data);
    }
    public function create(Request $request)
    {
        $user = Auth::user();
        $event = Events::forceCreate([
            'group_id' => $user->group_id,
            'title' => $request->get('title'),
            'description'=> $request->get('description'),
            'type' => $request->get('type'),
            'start' => $request->get('start'),
            'end'=> $request->get('end'),
            'allDay'=> $request->get('allDay')
        ]);
        Notifications::forceCreate([
            'user_id' => NULL,
            'group_id' => $user->group_id,
            'type' => 'event_created',
            'team_id' => NULL,
            'configuration'=> json_encode(['icon' => 'mdi-calendar', 'color' => 'bg-primary']),
            'path' => '/events',
        ]);
        return response()
            ->json([
                'event' => $event,
            ], Response::HTTP_CREATED);
    }
    public function update(Request $request)
    {
        $data = $request->all();

        $event = Events::findOrFail($data['id']);

        $event->update([
            'title' => $data->get('title'),
            'description'=> $data->get('description'),
            'type' => $data->get('type'),
            'start' => $data->get('start'),
            'end'=> $data->get('end'),
            'allDay'=> $data->get('allDay')
        ]);

        return response()->json([
            'event' => $event,
        ], Response::HTTP_OK);
    }

    public function delete(Events $event)
    {
        // Vérifier s'il y a des utilisateurs associés à l'équipe
        // $usersCount = User::where('current_team_id', $team->id)->count();
        // if ($usersCount > 0) {
        //     return response()->json([
        //         'message' => 'FORBIDDEN',
        //     ], Response::HTTP_BAD_REQUEST);
        // }

        $event->delete();
        return response()->json([
            'team_id' => $event->id,
        ], Response::HTTP_OK);

    }
}
