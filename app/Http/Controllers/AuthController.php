<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use App\Traits\ReturnResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ReturnResponse;

    public function register(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return $this->returnError(401, $validator->errors());
        }

        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $token = JWTAuth::fromUser($user);
        $user['token'] = $token;
        return $this->returnData('user', $user, 'User registered');
    }

    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->returnError(401, 'Unauthorized');
        }

        $user = auth()->user();
        $user['token'] = $token;

        return $this->returnData('user', $user, 'User login successfully');
    }

    public function deleteAccount()
    {
        $user = Auth::user();
        $userId = Auth::id();
        File::query()
            ->where('user_id','=',$userId)
            ->where('state','=',1)
            ->update(['state'=>0]);

        if ($user) {
            $user->delete();
            Auth::logout();
            return $this->returnSuccessMessage('account deleted');
        }
        return $this->returnError(202,'invalid');
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return $this->returnSuccessMessage('Successfully logged out');
    }

    public function users()
    {
        $users = User::query()->select(['name', 'email'])->get();
        return $this->returnData('users:', $users);
    }

    public function usersGroup($id)
    {
        $users = User::query()
            ->select(['name','email'])
            ->join('group_users as g','users.id','=','g.user_id')
            ->where('g.group_id','=',$id)->get();
        return $this->returnData('users:', $users);
    }


}
