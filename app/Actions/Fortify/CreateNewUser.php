<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use App\Models\Notifications;
use App\Models\Permissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', 'max:255'],
            'nameGroup' => ['required', 'string', 'max:255'],
            'activityAreaGroup' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();
        $group = Group::create([
            'name' => $input['nameGroup'],
            'activityArea' => $input['activityAreaGroup'],
        ]);
        return DB::transaction(function () use ($input,$group) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'role' => $input['role'],
                'group_id'=>$group->id
            ]), function (User $user) use ($group){
                $this->createTeam($user);
                $this->createPermissions($user);
                $this->welcomeNotification($group);
            });
        });
    }
    


    /**
     * Create a personal team for the user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function createTeam(User $user) // , array $input
    {
        
        $team = Team::forceCreate([
            'user_id' => $user->id,
            'group_id'=>$user->group_id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true
        ]);
        // $user->ownedTeams()->save();
        $user->current_team_id = $team->id;
        $user->save();
    }

    protected function createPermissions(User $user)
    {
        
        Permissions::forceCreate([
            'user_id' => $user->id,
            'canAddTeamMembers' => true,
            'canRemoveTeamMembers' => true,
            'canDeleteTeam' => true,
        ]);
    }
    protected function welcomeNotification(Group $group)
    {
        
        Notifications::forceCreate([
            'user_id' => NULL,
            'team_id' => NULL,
            'configuration'=> json_encode(['icon' => 'mdi-poll', 'color' => 'bg-success']),
            'group_id' => $group->id,
            'type' => 'welcome',
            'path' => '/dashboard',
        ]);
    }
}
