<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function dashboard()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => 'Login successful',
            'users' => $users,
        ]);
    }

    public function showLogin()
    {
       return view('login');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:users,email',
            'name'     => 'required|string|max:255|unique:users,name',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }
        
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'customer', // Default role for new signups
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful',
            'token'   => $token,
            'user'    => [
                'name' => $user->name,
                'role' => $user->role,
                'restaurant_id' => null
            ],
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('name', $request->name)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Log to Redis
            \App\Jobs\LogUserActivity::dispatch([
                'user_id' => $user->id,
                'name'    => $user->name,
                'action'  => 'Logged In',
                'time'    => now()->toDateTimeString()
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'status'     => 'success',
                'message'    => 'Login successful',
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => [
                    'id'            => $user->id,
                    'name'          => $user->name,
                    'role'          => $user->role, // super_admin, manager, chef, customer
                    'restaurant_id' => $user->restaurant_id // assigned shop ID
                ],
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Invalid name or password',
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        \App\Jobs\LogUserActivity::dispatch([
            'user_id' => $user->id,
            'name'    => $user->name,
            'action'  => 'Logged Out',
            'time'    => now()->toDateTimeString()
        ]);

        $user->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully',
        ]);
    }
}
