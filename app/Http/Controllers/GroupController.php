<?php

namespace App\Http\Controllers;
use App\Models\Group;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    //
    public function update(Request $request)
    {
        $data = $request->all();

        $group = Group::findOrFail($data['id']);

        $group->update([
            'name' => $data['name'],
            'activityArea' => $data['activityArea']
        ]);

        return response()->json([
            'group' => $group,
        ], Response::HTTP_OK);
    }
}
