<?php

namespace App\Http\Controllers;


use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index() {
        $departments = Department::all();
        return view('login', ['departments' => $departments]);
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(['success' => true, 'message' => 'Login successful']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password.'
        ], 401);
    }

    public function register(Request $request) {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'min:3'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'min:6', 'confirmed'],
                'role' => ['required', 'in:admin,head,staff'],
                'department_id' => ['required', 'exists:departments,id'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'department_id' => $validated['department_id'],
        ]);

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
        ]);
    }
}
