<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_vehicles' => Vehicle::count(),
            'total_tasks' => Task::count(),
            'total_users' => User::count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'vehicles_in_yard' => Vehicle::where('status', 'in_yard')->count(),
            'vehicles_ready' => Vehicle::where('status', 'ready')->count(),
        ];

        // Get recent vehicles
        $recent_vehicles = Vehicle::with(['creator', 'photos'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recent_vehicles'));
    }

    public function vehicles()
    {
        $vehicles = Vehicle::with(['creator', 'photos', 'consigneeDetails'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.vehicles', compact('vehicles'));
    }

    public function tasks()
    {
        $user = Auth::user();

        // Get task statistics
        $stats = [
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'running_tasks' => Task::where('status', 'running')->count(),
        ];

        // Get recent tasks for the chart
        $recent_tasks = Task::with(['vehicle', 'assignedUser', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get my tasks if user is not admin
        $my_tasks = collect();
        if ($user->role !== 'admin') {
            $my_tasks = Task::with(['vehicle', 'assignedUser', 'creator'])
                ->where('assigned_to', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        if ($user->role === 'admin') {
            $tasks = Task::with(['vehicle', 'assignedUser', 'creator'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $tasks = Task::with(['vehicle', 'assignedUser', 'creator'])
                ->where('assigned_to', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('dashboard.tasks', compact('tasks', 'stats', 'recent_tasks', 'my_tasks'));
    }

    public function users()
    {
        $user = Auth::user();
        if (! in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'This action is unauthorized.');
        }

        $users = User::orderBy('created_at', 'desc')->paginate(15);

        return view('dashboard.users', compact('users'));
    }
}
