<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;

class UserController extends Controller
{
   
    
    public function update(Request $request)
    {
        $data = $request->all();

        $team = User::findOrFail($data['id']);

        $team->update([
            'name' => $data['name'],
            'personal_team' => $data['personal_team'],
            'user_id' => $data['user_id'] ?? Auth::user()->id,
        ]);

        return response()->json([
            'team' => $team,
        ], Response::HTTP_OK);
    }
}
