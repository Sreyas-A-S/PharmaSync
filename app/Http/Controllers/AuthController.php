<?php

namespace App\Http\Controllers;


use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function index() {
        return view('login', ['departments' => Department::all()]);
    }


    public function login(Request $request) {
        try {
            $data = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            return back()->withInput($request->only('email'))
                ->withErrors($e->validator);
        }
        if (Auth::attempt($data)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect('/admin')->with('success', 'Login successful');
            } elseif ($user->role === 'head') {
                return redirect('/head')->with('success', 'Login successful');
            } else {
                return redirect('/staff')->with('success', 'Login successful');
            }
        }
        return back()->withInput($request->only('email'))->with('error', 'Invalid email or password.');
    }

    public function register(Request $request) {
        try {
            $data = $request->validate([
                'name' => 'required|string|min:3',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'department_id' => 'required|exists:departments,id',
            ]);

            if ($request->input('password') !== $request->input('confirm_password')) {
                return back()->withInput()->withErrors(['confirm_password' => 'Passwords do not match.']);
            }
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->validator);
        }
        $data['role'] = 'staff';
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        Auth::login($user);
        return redirect('/')->with('success', 'Registration successful');
    }
}
