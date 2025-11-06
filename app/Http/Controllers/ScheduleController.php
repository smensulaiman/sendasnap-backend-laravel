<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ramsey\Uuid\Math\RoundingMode;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        // Default to today's date if no date is provided
        $date = $request->query('date', now()->format('Y-m-d'));

        $query = Task::with(['vehicle', 'assignedUser', 'creator'])
            ->whereDate('work_date', $date)
            ->orderBy('work_time');

        $tasks = $query->limit(50)->get();

        // Summary for cards - only for the selected date
        $stats = [
            'total' => Task::whereDate('work_date', $date)->count(),
            'pending' => Task::whereDate('work_date', $date)->where('status', 'pending')->count(),
            'running' => Task::whereDate('work_date', $date)->where('status', 'running')->count(),
            'completed' => Task::whereDate('work_date', $date)->where('status', 'completed')->count(),
        ];

        return view('schedule.index', compact('tasks', 'date', 'stats'));
    }

    public function fetch(Request $request): JsonResponse
    {
        $date = $request->query('date');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Task::with(['vehicle', 'assignedUser', 'creator']);

        // Check if it's a date range request
        if ($startDate && $endDate) {
            $query->whereBetween('work_date', [$startDate, $endDate]);
        } elseif ($date) {
            $query->whereDate('work_date', $date);
        } else {
            // Default to today's date
            $query->whereDate('work_date', now()->format('Y-m-d'));
        }

        $query->orderBy('work_date')->orderBy('work_time');

        $tasks = $query->get();

        // Transform tasks for schedule display
        $schedules = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'time' => $task->work_time ? $task->work_time->format('H:i') : null,
                'status' => $task->status,
                'priority' => $task->priority,
                'vehicle' => $task->vehicle ? [
                    'id' => $task->vehicle->id,
                    'display_name' => $task->vehicle->make.' '.$task->vehicle->model.' - '.$task->vehicle->serial_number,
                    'serial_number' => $task->vehicle->serial_number,
                    'make' => $task->vehicle->make,
                    'model' => $task->vehicle->model,
                ] : null,
                'assigned_to' => $task->assignedUser ? [
                    'id' => $task->assignedUser->id,
                    'name' => $task->assignedUser->name,
                    'role' => $task->assignedUser->role,
                ] : null,
                'created_by' => $task->creator ? [
                    'id' => $task->creator->id,
                    'name' => $task->creator->name,
                ] : null,
                'work_date' => $task->work_date,
                'due_date' => $task->due_date,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ];
        });

        // Calculate stats for the selected date
        $stats = [
            'total' => $schedules->count(),
            'pending' => $schedules->where('status', 'pending')->count(),
            'running' => $schedules->where('status', 'running')->count(),
            'completed' => $schedules->where('status', 'completed')->count(),
            'cancelled' => $schedules->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'schedules' => $schedules->values(),
                'stats' => $stats,
                'date' => $date,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_range' => ($startDate && $endDate),
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
            ],
        ]);
    }

    public function kanban()
    {
        $columns = [
            'pending' => Task::with(['assignedUser', 'vehicle', 'creator'])->where('status', 'pending')->latest()->limit(50)->get(),
            'running' => Task::with(['assignedUser', 'vehicle', 'creator'])->where('status', 'running')->latest()->limit(50)->get(),
            'completed' => Task::with(['assignedUser', 'vehicle', 'creator'])->where('status', 'completed')->latest()->limit(50)->get(),
            'cancelled' => Task::with(['assignedUser', 'vehicle', 'creator'])->where('status', 'cancelled')->latest()->limit(50)->get(),
        ];

        // If it's an API request, return JSON
        if (request()->expectsJson()) {
            $kanbanData = [
                'pending' => $columns['pending']->map(function ($task) {
                    return $this->transformTaskForKanban($task);
                }),
                'running' => $columns['running']->map(function ($task) {
                    return $this->transformTaskForKanban($task);
                }),
                'completed' => $columns['completed']->map(function ($task) {
                    return $this->transformTaskForKanban($task);
                }),
                'cancelled' => $columns['cancelled']->map(function ($task) {
                    return $this->transformTaskForKanban($task);
                }),
            ];

            return response()->json([
                'success' => true,
                'data' => $kanbanData,
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        }

        return view('schedule.kanban', compact('columns'));
    }

    /**
     * Get schedule statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));

        $query = Task::whereBetween('work_date', [$dateFrom, $dateTo]);

        $stats = [
            'total_tasks' => $query->count(),
            'by_status' => [
                'pending' => $query->clone()->where('status', 'pending')->count(),
                'running' => $query->clone()->where('status', 'running')->count(),
                'completed' => $query->clone()->where('status', 'completed')->count(),
                'cancelled' => $query->clone()->where('status', 'cancelled')->count(),
            ],
            'by_priority' => [
                'low' => $query->clone()->where('priority', 'low')->count(),
                'medium' => $query->clone()->where('priority', 'medium')->count(),
                'high' => $query->clone()->where('priority', 'high')->count(),
                'urgent' => $query->clone()->where('priority', 'urgent')->count(),
            ],
            'completion_rate' => $query->count() > 0
                ? round(($query->clone()->where('status', 'completed')->count() / $query->count()) * 100, 2, RoundingMode::FLOOR)
                : 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'date_range' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
            ],
        ]);
    }

    /**
     * Transform task for kanban display
     */
    private function transformTaskForKanban($task): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'priority' => $task->priority,
            'work_date' => $task->work_date,
            'work_time' => $task->work_time ? $task->work_time->format('H:i') : null,
            'due_date' => $task->due_date,
            'vehicle' => $task->vehicle ? [
                'id' => $task->vehicle->id,
                'display_name' => $task->vehicle->make.' '.$task->vehicle->model,
                'serial_number' => $task->vehicle->serial_number,
            ] : null,
            'assigned_to' => $task->assignedUser ? [
                'id' => $task->assignedUser->id,
                'name' => $task->assignedUser->name,
                'avatar_url' => $task->assignedUser->avatar_url,
            ] : null,
            'created_by' => $task->creator ? [
                'id' => $task->creator->id,
                'name' => $task->creator->name,
            ] : null,
        ];
    }
}
