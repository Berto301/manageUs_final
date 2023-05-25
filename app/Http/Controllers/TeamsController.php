<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Models\Role;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Actions\Jetstream\DeleteTeam;

class TeamsController extends Controller
{
    public function teams(Request $request)
    {
       $teams = $this->getAllTeams();
       $user = Auth::user();
       $availableRoles = Role::all();
       $permissions = Permissions::where('user_id', $user->id)->firstOrFail();
    
        $data = [
            'teams' => $teams,
            'user' => $user,
            'availableRoles' => $availableRoles,
            'permissions' => $permissions,
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

        $team = Team::forceCreate([
            'user_id' => Auth::user()->id,
            'name' => $request->get('name'),
            'personal_team'=> $request->get('personal_team')
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

        $user = User::forceCreate([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'current_team_id'=>$request->get('current_team_id'),
            'role'=>$request->get('role')
        ]);
        if($user->id){
            Permissions::forceCreate([
                'user_id' => $user->id,
                'canAddTeamMembers' => $request->get('canAddTeamMembers'),
                'canRemoveTeamMembers' => $request->get('canRemoveTeamMembers'),
                'canDeleteTeam' => $request->get('canDeleteTeam'),
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

