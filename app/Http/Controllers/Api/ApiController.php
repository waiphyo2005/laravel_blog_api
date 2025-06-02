<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'address' => 'required',
            'phone_number' => 'required'
        ]);

        $user = User::create($data);

        return response()->json([
            'message' => 'User created successfully.',
            'data' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $token = Auth::attempt($credentials);
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
        return response()->json($user, 200);
    }

    public function refreshToken() {}

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'User logout successfully'
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required'
        ]);

        $user = Auth::user();

        $passwordCheck = Hash::check($request->old_password, $user->password);
        if (!$passwordCheck) {
            return response()->json([
                'message' => 'Incorrect Password!'
            ], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json([
            'message' => 'Password changed successfully.'
        ], 200);
    }
}
