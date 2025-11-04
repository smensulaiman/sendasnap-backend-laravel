<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->load(['assignedTasks', 'createdTasks']);

        // If it's an API request, return JSON
        if (request()->expectsJson()) {
            // Calculate stats
            $assignedTasksCount = $user->assignedTasks()->count();
            $createdTasksCount = $user->createdTasks()->count();
            $completedTasksCount = $user->assignedTasks()->where('status', 'completed')->count();
            $pendingTasksCount = $user->assignedTasks()->where('status', 'pending')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'avatar_url' => $user->avatar_url,
                        'email_verified_at' => $user->email_verified_at,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ],
                    'stats' => [
                        'assigned_tasks' => $assignedTasksCount,
                        'created_tasks' => $createdTasksCount,
                        'completed_tasks' => $completedTasksCount,
                        'pending_tasks' => $pendingTasksCount,
                    ],
                    'recent_tasks' => $user->assignedTasks()
                        ->with(['vehicle', 'creator'])
                        ->latest()
                        ->take(5)
                        ->get()
                        ->map(function ($task) {
                            return [
                                'id' => $task->id,
                                'title' => $task->title,
                                'status' => $task->status,
                                'priority' => $task->priority,
                                'work_date' => $task->work_date,
                                'vehicle' => $task->vehicle ? [
                                    'id' => $task->vehicle->id,
                                    'serial_number' => $task->vehicle->serial_number,
                                    'make' => $task->vehicle->make,
                                    'model' => $task->vehicle->model,
                                ] : null,
                                'creator' => [
                                    'id' => $task->creator->id,
                                    'name' => $task->creator->name,
                                ],
                            ];
                        }),
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        }

        return view('profile.show', ['user' => $user]);
    }

    public function edit()
    {
        $user = Auth::user();

        // If it's an API request, return user data for editing
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'avatar_url' => $user->avatar_url,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ],
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        }

        return view('profile.edit', ['user' => $user]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $data = $request->safe()->only(['name', 'phone']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle password change
        if ($request->filled('password')) {
            if (! $request->filled('current_password') || ! Hash::check($request->current_password, $user->password)) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect',
                        'errors' => ['current_password' => ['Current password is incorrect.']],
                    ], 422);
                }

                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // If it's an API request, return JSON response
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'avatar_url' => $user->avatar_url,
                        'updated_at' => $user->updated_at,
                    ],
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Create a personal access token for the currently authenticated user.
     */
    public function createApiToken(): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $token = $user->createToken('ui-task-create')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
}
