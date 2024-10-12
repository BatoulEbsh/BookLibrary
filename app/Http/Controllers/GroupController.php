<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Group;
use App\Models\GroupUser;
use App\Traits\ReturnResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    use ReturnResponse;

    public function createGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|unique:groups,group_name',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 404);
        }
        $group = new Group();
        $groupUser = new GroupUser();
        $group->fill([
            'group_name' => $request->input('group_name'),
        ]);
        $group->save();
        $groupUser->fill([
            'user_id' => Auth::id(),
            'group_id' => $group['id']
        ]);
        $groupUser->save();
        return $this->returnData('success', $group, 'group_name');
    }

    public function addUserToGroup($groupId, Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|exists:users,id',
        ]);
        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        $userIds = $request->input('user_ids');
        $group->users()->attach($userIds);
        return response()->json(['message' => 'Users added to group successfully']);
    }

    public function usersGroup()
    {
        $userId = Auth::id();
        $groups = Group::query()
            ->join('group_users as g', 'groups.id', '=', 'g.group_id')
            ->where('g.user_id', '=', $userId)
            ->get();
        return $this->returnData('groups:', $groups);
    }
    public function filesGroup($id){
        $files = File::query()
            ->where('group_id','=',$id)
            ->get();
        return $this->returnData('files:', $files);
    }
}
