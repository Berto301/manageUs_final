<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Group;

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
        $data = [
            'user'=>$user,
            'group'=>$group
        ];
        return Inertia::render('Profile/Show',$data);
    }

}
