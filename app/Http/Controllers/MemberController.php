<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Don't forget this!
use Illuminate\Support\Facades\Hash; // Don't forget this!
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * Get all staff members (Managers and Chefs)
     * For the Super Admin's "Member List" page
     */
    public function index()
    {
        // We only want users who are NOT customers and NOT the super_admin
        $members = User::whereIn('role', ['manager', 'chef'])
            ->with('restaurant') // Assuming you have a 'restaurant' relationship in User model
            ->get();

        return response()->json([
            'success' => true,
            'members' => $members
        ]);
    }

    /**
     * Store a new staff member
     */
    public function storeMember(Request $request) 
    {
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:manager,chef',
            'restaurant_id' => 'required|exists:restaurants,id'
        ]);

        // 2. Creation
        $member = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password for new staff
            'role' => $request->role,
            'restaurant_id' => $request->restaurant_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Staff member added successfully',
            'member' => $member
        ]);
    }
}