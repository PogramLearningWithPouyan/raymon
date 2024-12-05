<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreatRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Http\JsonResponse;
class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function store(UserCreatRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        #first user is admin
        if ($user->id==1){
            $user->role = 'admin';
            $user->save();
        }

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function updateRole(Request $request): JsonResponse
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'You are not an admin'], 403);
        }

        $user = User::where('id',$request->user_id)->first();

        if ($user==null){
            return response()->json(['message' => 'user not found'], 403);
        }

        $user->role = $request->role;
        $user->save();

        return response()->json(['message' => 'Role updated successfully']);
    }
}
