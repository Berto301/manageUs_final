<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
   
    
    public function logout()
    {
        Auth::guard('web')->logout();

        return Inertia::location(route('login'));
    }
    public function profile()
    {
        $user = Auth::user();
        $group = Group::where('id', $user->group_id)->firstOrFail();
        $notifications = DB::table('notifications')
        ->select('*')
        ->where('group_id', $user->group_id)
        ->orderBy( DB::raw('DATE(created_at)'), 'desc')
        ->get();
        $data = [
            'user'=>$user,
            'group'=>$group,
            'notifications'=>$notifications
        ];
        return Inertia::render('Profile/Show',$data);
    }

}
