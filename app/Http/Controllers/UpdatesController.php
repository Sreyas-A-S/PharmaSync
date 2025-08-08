<?php

namespace App\Http\Controllers;

use App\Models\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdatesController extends Controller
{
    public function index() {
        return view('staff');
    }

    public function updates() {
        $user_id = Auth::user()->id;
        $updates = Update::where('user_id', $user_id)->orderBy('id', 'desc')->get();
        return response()->json($updates);
    }

    public function update(Request $request, Update $update)
    {
        if ($update->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $update->update($validated);

        return response()->json(['message' => 'Update successful.']);
    }

    public function destroy(Update $update)
    {
        if ($update->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $update->delete();

        return response()->json(['message' => 'Delete successful.']);
    }
}
