<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalDepartments = Department::count();
        $totalUpdates = Update::count();

        return view('admin_dashboard', compact('totalUsers', 'totalDepartments', 'totalUpdates'));
    }

    public function getUsers(Request $request)
    {
        try {
            $users = User::with('department')->get();
            return response()->json($users);
        } catch (Exception $e) {
            Log::error('Failed to fetch users: ' . $e->getMessage());
            return response()->json(['message' => 'Could not fetch users.'], 500);
        }
    }

    public function storeUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:staff,head,admin',
                'department_id' => 'nullable|exists:departments,id',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'department_id' => $validated['department_id'],
            ]);

            return response()->json(['message' => 'User created successfully.', 'user' => $user]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Catch specific QueryException for foreign key constraint violations
            if ($e->getCode() === '23000') { // SQLSTATE for Integrity Constraint Violation
                Log::error('Foreign key constraint violation when creating user: ' . $e->getMessage());
                return response()->json(['message' => 'Invalid department selected. Please ensure the department exists.', 'error' => $e->getMessage()], 422);
            }
            Log::error('Failed to create user: ' . $e->getMessage());
            return response()->json(['message' => 'Could not create user.', 'error' => $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            return response()->json(['message' => 'Could not create user.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateUser(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
                'role' => 'required|string|in:staff,head,admin',
                'department_id' => 'nullable|exists:departments,id',
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->role = $validated['role'];
            $user->department_id = $validated['department_id'];
            $user->save();

            return response()->json(['message' => 'User updated successfully.', 'user' => $user]);
        } catch (Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            return response()->json(['message' => 'Could not update user.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroyUser(User $user)
    {
        try {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully.']);
        } catch (Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            return response()->json(['message' => 'Could not delete user.'], 500);
        }
    }

    public function getDepartments()
    {
        try {
            $departments = Department::all();
            return response()->json($departments);
        } catch (Exception $e) {
            Log::error('Failed to fetch departments: ' . $e->getMessage());
            return response()->json(['message' => 'Could not fetch departments.'], 500);
        }
    }

    public function storeDepartment(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:departments',
            ]);

            $department = Department::create([
                'name' => $validated['name'],
            ]);

            return response()->json(['message' => 'Department created successfully.', 'department' => $department]);
        } catch (Exception $e) {
            Log::error('Failed to create department: ' . $e->getMessage());
            return response()->json(['message' => 'Could not create department.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateDepartment(Request $request, Department $department)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            ]);

            $department->name = $validated['name'];
            $department->save();

            return response()->json(['message' => 'Department updated successfully.', 'department' => $department]);
        } catch (Exception $e) {
            Log::error('Failed to update department: ' . $e->getMessage());
            return response()->json(['message' => 'Could not update department.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroyDepartment(Department $department)
    {
        try {
            $department->delete();
            return response()->json(['message' => 'Department deleted successfully.']);
        } catch (Exception $e) {
            Log::error('Failed to delete department: ' . $e->getMessage());
            return response()->json(['message' => 'Could not delete department.'], 500);
        }
    }

    public function getAllUpdates()
    {
        try {
            $updates = Update::with('user.department', 'attachments')->orderBy('created_at', 'desc')->get();
            return response()->json($updates);
        } catch (Exception $e) {
            Log::error('Failed to fetch all updates: ' . $e->getMessage());
            return response()->json(['message' => 'Could not fetch updates.'], 500);
        }
    }
}
