<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserProfileResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'address' => 'required',
            'phone_number' => 'required'
        ]);

        $user = User::create($validatedData);

        return response()->json([
            'message' => 'User created successfully.',
            'data' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $token = Auth::attempt($validatedData);
        if (!$token) {
            return response()->json([
                'message' => 'Incorrect login credentials.'
            ], 401);
        }

        return response()->json([
            'message' => 'User login successful.',
            'token' => $token
        ], 200);
    }

    public function profile()
    {
        $user = Auth::user();
        return new UserProfileResource($user);
    }

    public function editProfile(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email' . $user->id,
            'address' => 'required',
            'phone_number' => 'required'
        ]);

        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number']
        ]);

        return response()->json([
            'message' => 'User profile updated successfully.'
        ], 200);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'User logout successfully'
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required'
        ]);

        $user = Auth::user();

        $passwordCheck = Hash::check($validatedData['old_password'], $user->password);
        if (!$passwordCheck) {
            return response()->json([
                'message' => 'Incorrect Password!'
            ], 401);
        }

        $user->password = Hash::make($validatedData['new_password']);
        $user->save();
        return response()->json([
            'message' => 'Password changed successfully.'
        ], 200);
    }
}
