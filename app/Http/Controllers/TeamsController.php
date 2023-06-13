<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Models\Role;
use App\Models\Permissions;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Actions\Jetstream\DeleteTeam;
use Illuminate\Support\Facades\DB;

class TeamsController extends Controller
{
    public function teams(Request $request)
    {
       $teams = $this->getAllTeams();
       $user = Auth::user();
       $availableRoles = Role::all();
       $permissions = Permissions::where('user_id', $user->id)->firstOrFail();
       $notifications = DB::table('notifications')
       ->select('*')
       ->where('group_id', $user->group_id)
       ->orderBy( DB::raw('DATE(created_at)'), 'desc')
       ->get();
    
        $data = [
            'teams' => $teams,
            'user' => $user,
            'availableRoles' => $availableRoles,
            'permissions' => $permissions,
            "notifications"=>$notifications
        ];

        return Inertia::render('Teams/Lists', $data);
    }
    public function settingsTeams(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
       
        $availableRoles = Role::all();
        $permissions = Permissions::where('user_id', $user->id)->firstOrFail();
        $teamInvitations = TeamInvitation::where('team_id',$user->id)->get();
        $team = Team::where('id', $data['id'])->firstOrFail();
        $owner = User::where([
            ['role', 'founder'],
            ['current_team_id', $team->id]
        ])->firstOrFail();
        $team->team_invitations = $teamInvitations;
        $team->owner = $owner;
        $data = [
            'team' => $team,
            'availableRoles' => $availableRoles,
            'permissions' => $permissions,
            'user'=>$user
        ];
        

        return Inertia::render('Teams/Show', $data);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $team = Team::forceCreate([
            'user_id' => Auth::user()->id,
            'group_id'=>$user->group_id,
            'name' => $request->get('name'),
            'personal_team'=> $request->get('personal_team')
        ]);
        Notifications::forceCreate([
            'user_id' => NULL,
            'group_id' => $user->group_id,
            'type' => 'team_created',
            'team_id' => $team->id,
            'configuration'=> json_encode(['icon' => 'mdi-account-multiple-plus', 'color' => 'bg-primary']),
            'path' => '/teams',
        ]);
        return response()
            ->json([
                'team' => $team,
            ], Response::HTTP_CREATED);
    }
    public function update(Request $request)
    {
        $data = $request->all();

        $team = Team::findOrFail($data['id']);

        $team->update([
            'name' => $data['name'],
            'personal_team' => $data['personal_team'],
            'user_id' => $data['user_id'] ?? Auth::user()->id,
            'group_id'=>Auth::user()->group_id,
        ]);

        return response()->json([
            'team' => $team,
        ], Response::HTTP_OK);
    }

    public function deleteTeam(Team $team)
    {
        // Vérifier s'il y a des utilisateurs associés à l'équipe
        $usersCount = User::where('current_team_id', $team->id)->count();
        if ($usersCount > 0) {
            return response()->json([
                'message' => 'FORBIDDEN',
            ], Response::HTTP_BAD_REQUEST);
        }

        $team->delete();
        return response()->json([
            'team_id' => $team->id,
        ], Response::HTTP_OK);

    }



    public function createMember(Request $request)
    {
        $auth = Auth::user();
        $user = User::forceCreate([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'current_team_id'=>$request->get('current_team_id'),
            'role'=>$request->get('role'),
            'group_id'=>$auth->group_id
        ]);
        if($user->id){
            Permissions::forceCreate([
                'user_id' => $user->id,
                'canAddTeamMembers' => $request->get('canAddTeamMembers'),
                'canRemoveTeamMembers' => $request->get('canRemoveTeamMembers'),
                'canDeleteTeam' => $request->get('canDeleteTeam'),
            ]);
            Notifications::forceCreate([
                'user_id' => $user->id,
                'group_id' => $auth->group_id,
                'type' => 'new_member',
                'team_id' => $request->get('current_team_id'),
                'configuration'=> json_encode(['icon' => 'mdi-account-plus', 'color' => 'bg-info']),
                'path' => '/teams',
            ]);
        }
        
        return response()
            ->json([
                'user' => $user,
               // 'teams'=> $teams
            ], Response::HTTP_CREATED);
    }
    protected function getAllTeams (){
        $user = Auth::user();
    	//$tasks = Task::where('team_id', $user->currentTeam->id)->get();
        $teams = Team::where('user_id', $user->id)->get();
        foreach ($teams as $team) {
            $users = User::where('current_team_id', $team->id)->get();
            $teamInvitations = TeamInvitation::where('team_id',$team->id)->get();
            $owner = User::where(
                'id', $team->user_id
            )->firstOrFail();
            $team->team_invitations = $teamInvitations;
            $team->owner = $owner;
            $team->users = $users;
        };
        return $teams;
    }
}

