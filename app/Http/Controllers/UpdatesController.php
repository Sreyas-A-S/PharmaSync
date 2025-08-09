<?php

namespace App\Http\Controllers;

use App\Models\Update;
use App\Models\User;
use App\Models\Attachment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdatesController extends Controller
{
    public function index() {
        $user_id = Auth::id();
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $existingUpdate = Update::where('user_id', $user_id)
                                ->whereDate('created_at', '>=', $startOfWeek)
                                ->first();
        return view('staff', compact('existingUpdate'));
    }

    public function updates() {
        try {
            $user_id = Auth::user()->id;
            $updates = Update::with('attachments')->where('user_id', $user_id)->orderBy('id', 'desc')->get();
            return response()->json($updates);
        } catch (Exception $e) {
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
                'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'delete_attachments' => 'nullable|array',
                'delete_attachments.*' => 'exists:attachments,id',
            ]);

            $update->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
            ]);

            // Handle attachment deletions
            if (isset($validated['delete_attachments'])) {
                foreach ($validated['delete_attachments'] as $attachmentId) {
                    $attachment = Attachment::find($attachmentId);
                    if ($attachment && $attachment->update_id === $update->id) {
                        Storage::disk('public')->delete($attachment->file_path);
                        $attachment->delete();
                    }
                }
            }

            // Handle new attachment uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    $update->attachments()->create(['file_path' => $path]);
                }
            }

            return response()->json(['message' => 'Update successful.']);
        } catch (\Exception $e) {
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
            foreach ($update->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $update->delete();
            return response()->json(['message' => 'Delete successful.']);
        } catch (Exception $e) {
            Log::error('Failed to delete: ' . $e->getMessage());
            return response()->json(['message' => 'Could not delete.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $update = Update::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'user_id' => Auth::id(),
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    $update->attachments()->create(['file_path' => $path]);
                }
            }

            return response()->json(['message' => 'Update created successfully.']);
        } catch (Exception $e) {
            Log::error('Failed to create update: ' . $e->getMessage());
            return response()->json(['message' => 'Could not create update.', 'error' => $e->getMessage()], 500);
        }
    }

    public function dashboard(Request $request) {
        $department_id = Auth::user()->department_id;
        $users = User::where('department_id', $department_id)->where('role', 'staff')->get();
        $updatesCount = [];
        foreach ($users as $user) {
            $updatesCount[$user->id] = $user->updates()->count();
        }

        $selectedUserId = $request->query('user_id');

        return view('dashboard', compact('users', 'updatesCount', 'selectedUserId'));
    }

    public function departmentUpdates(Request $request) {
        try {
            $department_id = Auth::user()->department_id;
            $query = Update::with('attachments', 'user')
                           ->whereHas('user', function ($q) use ($department_id) {
                               $q->where('department_id', $department_id);
                           });

            if ($request->has('user_id') && $request->user_id !== 'all') {
                $query->where('user_id', $request->user_id);
            }

            $updates = $query->orderBy('created_at', 'desc')->get();

            return response()->json($updates);
        } catch (Exception $e) {
            Log::error('Failed to fetch department updates: ' . $e->getMessage());
            return response()->json(['message' => 'Could not fetch updates.'], 500);
        }
    }
}
