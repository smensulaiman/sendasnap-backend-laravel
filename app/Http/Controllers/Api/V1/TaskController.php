<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::with(['vehicle', 'assignedUser', 'creator', 'attachments']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        // Filter by assigned user
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->get('assigned_to'));
        }

        // Filter by vehicle
        if ($request->has('vehicle_id')) {
            $query->where('vehicle_id', $request->get('vehicle_id'));
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('work_date', '>=', $request->get('date_from'));
        }
        if ($request->has('date_to')) {
            $query->where('work_date', '<=', $request->get('date_to'));
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->successResponse('Tasks retrieved successfully', [
            'tasks' => $tasks->items(),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        ]);
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'work_date' => 'required|date',
            'work_time' => 'required|date_format:H:i',
            'priority' => 'required|in:low,medium,high,urgent',
            'vehicle_id' => 'required|exists:vehicles,id',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after_or_equal:work_date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'work_date' => $request->work_date,
            'work_time' => $request->work_time,
            'priority' => $request->priority,
            'vehicle_id' => $request->vehicle_id,
            'assigned_to' => $request->assigned_to,
            'created_by' => auth()->id(),
            'due_date' => $request->due_date,
        ]);

        $task->load(['vehicle', 'assignedUser', 'creator', 'attachments']);

        return $this->successResponse('Task created successfully', [
            'task' => $task,
        ], 201);
    }

    /**
     * Display the specified task
     */
    public function show(Task $task): JsonResponse
    {
        $task->load(['vehicle', 'assignedUser', 'creator', 'attachments']);

        return $this->successResponse('Task retrieved successfully', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified task
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'work_date' => 'sometimes|date',
            'work_time' => 'sometimes|date_format:H:i',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'assigned_to' => 'sometimes|exists:users,id',
            'due_date' => 'nullable|date|after_or_equal:work_date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        $task->update($request->only([
            'title',
            'description',
            'work_date',
            'work_time',
            'priority',
            'vehicle_id',
            'assigned_to',
            'due_date'
        ]));

        $task->load(['vehicle', 'assignedUser', 'creator', 'attachments']);

        return $this->successResponse('Task updated successfully', [
            'task' => $task,
        ]);
    }

    /**
     * Remove the specified task
     */
    public function destroy(Task $task): JsonResponse
    {
        // Delete associated attachments
        foreach ($task->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $task->delete();

        return $this->successResponse('Task deleted successfully');
    }

    /**
     * Assign task to user
     */
    public function assign(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        $task->update(['assigned_to' => $request->assigned_to]);

        $task->load(['vehicle', 'assignedUser', 'creator', 'attachments']);

        return $this->successResponse('Task assigned successfully', [
            'task' => $task,
        ]);
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,running,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        $updateData = ['status' => $request->status];

        if ($request->status === 'completed') {
            $updateData['completed_at'] = now();
        } else {
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);

        $task->load(['vehicle', 'assignedUser', 'creator', 'attachments']);

        return $this->successResponse('Task status updated successfully', [
            'task' => $task,
        ]);
    }

    /**
     * Upload attachment for task
     */
    public function uploadAttachment(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240',
            'file_name' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        $file = $request->file('file');
        $filePath = $file->store('task-attachments', 'public');
        $fileName = $request->file_name ?? $file->getClientOriginalName();
        $fileType = $file->getClientMimeType();

        $attachment = $task->attachments()->create([
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'uploaded_by' => auth()->id(),
        ]);

        return $this->successResponse('Attachment uploaded successfully', [
            'attachment' => $attachment,
        ], 201);
    }

    /**
     * Delete attachment from task
     */
    public function deleteAttachment(Task $task, TaskAttachment $attachment): JsonResponse
    {
        if ($attachment->task_id !== $task->id) {
            return $this->errorResponse('Attachment not found for this task', [], 404);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return $this->successResponse('Attachment deleted successfully');
    }

    /**
     * Get tasks created by current user
     */
    public function myTasks(Request $request): JsonResponse
    {
        $query = Task::with(['vehicle', 'assignedUser', 'creator', 'attachments'])
            ->where('created_by', auth()->id());

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->has('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->successResponse('My tasks retrieved successfully', [
            'tasks' => $tasks->items(),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        ]);
    }

    /**
     * Get tasks assigned to current user
     */
    public function assignedToMe(Request $request): JsonResponse
    {
        $query = Task::with(['vehicle', 'assignedUser', 'creator', 'attachments'])
            ->where('assigned_to', auth()->id());

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->has('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return $this->successResponse('Assigned tasks retrieved successfully', [
            'tasks' => $tasks->items(),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        ]);
    }

    /**
     * Success response helper
     */
    private function successResponse(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'timestamp' => now()->toISOString(),
            ],
        ], $status);
    }

    /**
     * Error response helper
     */
    private function errorResponse(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'timestamp' => now()->toISOString(),
            ],
        ], $status);
    }
}
