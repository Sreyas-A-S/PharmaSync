<?php

namespace App\Http\Controllers;

use App\Models\Update;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdatesController extends Controller
{
    public function index() {
        return view('staff');
    }

    public function updates() {
        try {
            $user_id = Auth::user()->id;
            $updates = Update::where('user_id', $user_id)->orderBy('id', 'desc')->get();
            return response()->json($updates);
        } catch (\Exception $e) {
            Log::error('Failed to fetch updates: ' . $e->getMessage());
            return response()->json(['message' => 'Could not fetch updates.'], 500);
        }
    }

    public function update(Request $request, Update $update)
    {
        if ($update->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $update->update($validated);

            return response()->json(['message' => 'Update successful.']);
        } catch (Exception $e) {
            Log::error('Failed to update: ' . $e->getMessage());
            return response()->json(['message' => 'Could not update.'], 500);
        }
    }

    public function destroy(Update $update)
    {
        if ($update->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $update->delete();
            return response()->json(['message' => 'Delete successful.']);
        } catch (Exception $e) {
            Log::error('Failed to delete: ' . $e->getMessage());
            return response()->json(['message' => 'Could not delete.'], 500);
        }
    }
}
