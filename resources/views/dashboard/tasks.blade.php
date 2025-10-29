@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Task Management</h3>
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-outline" onclick="showFilters()">
                    <i class="fas fa-filter"></i>
                    Filters
                </button>
                <button class="btn btn-primary" onclick="showAddTaskModal()">
                    <i class="fas fa-plus"></i>
                    Add Task
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Vehicle</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr class="fade-in">
                                    <td>
                                        <div>
                                            <div
                                                style="font-weight: 600; color: hsl(var(--foreground)); font-size: 14px; margin-bottom: 4px;">
                                                {{ $task->title }}</div>
                                            <div style="font-size: 12px; color: hsl(var(--muted-foreground));">
                                                {{ Str::limit($task->description, 50) }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div
                                                style="width: 32px; height: 32px; background: linear-gradient(135deg, hsl(var(--primary)), hsl(var(--primary) / 0.8)); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px;">
                                                <i class="fas fa-car"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 500; color: hsl(var(--foreground)); font-size: 14px;">
                                                    {{ $task->vehicle->make }} {{ $task->vehicle->model }}</div>
                                                <div style="font-size: 11px; color: hsl(var(--muted-foreground));">
                                                    {{ $task->vehicle->serial_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $task->priority }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $task->status }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div
                                                style="width: 32px; height: 32px; background: hsl(var(--primary)); color: hsl(var(--primary-foreground)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                                {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 500; color: hsl(var(--foreground)); font-size: 14px;">
                                                    {{ $task->assignedUser->name }}</div>
                                                <div style="font-size: 11px; color: hsl(var(--muted-foreground));">
                                                    {{ ucfirst($task->assignedUser->role) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($task->due_date)
                                            <div style="font-size: 12px; color: hsl(var(--foreground)); font-weight: 500;">
                                                {{ $task->due_date->format('M d, Y') }}</div>
                                            <div style="font-size: 11px; color: hsl(var(--muted-foreground));">
                                                @if($task->due_date->isPast())
                                                    <span style="color: #ef4444;">Overdue</span>
                                                @elseif($task->due_date->isToday())
                                                    <span style="color: #f59e0b;">Due today</span>
                                                @elseif($task->due_date->diffInDays() <= 3)
                                                    <span style="color: #f59e0b;">Due soon</span>
                                                @else
                                                    <span style="color: #10b981;">On track</span>
                                                @endif
                                            </div>
                                        @else
                                            <span style="color: hsl(var(--muted-foreground)); font-size: 12px;">No due date</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 4px;">
                                            <button class="btn btn-outline" style="padding: 6px 8px; font-size: 12px;"
                                                onclick="viewTask({{ $task->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline" style="padding: 6px 8px; font-size: 12px;"
                                                onclick="editTask({{ $task->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($task->status === 'pending')
                                                <button class="btn btn-primary" style="padding: 6px 8px; font-size: 12px;"
                                                    onclick="startTask({{ $task->id }})">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @elseif($task->status === 'running')
                                                <button class="btn btn-primary" style="padding: 6px 8px; font-size: 12px;"
                                                    onclick="completeTask({{ $task->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    {{ $tasks->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px; color: hsl(var(--muted-foreground));">
                    <div
                        style="width: 80px; height: 80px; background: hsl(var(--muted)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 32px;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 style="margin-bottom: 8px; color: hsl(var(--foreground));">No tasks found</h3>
                    <p style="margin-bottom: 24px;">Get started by creating your first task.</p>
                    <button class="btn btn-primary" onclick="showAddTaskModal()">
                        <i class="fas fa-plus"></i>
                        Add Task
                    </button>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showFilters() {
            Swal.fire({
                title: 'Filter Tasks',
                html: `
                <div style="text-align: left;">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Status</label>
                        <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="running">Running</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Priority</label>
                        <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Assigned To</label>
                        <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <option value="">All Users</option>
                            <option value="me">Assigned to me</option>
                            <option value="others">Assigned to others</option>
                        </select>
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Apply Filters',
                cancelButtonText: 'Cancel',
                confirmButtonColor: 'hsl(var(--primary))',
                width: '400px'
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast('Filters applied successfully', 'success');
                }
            });
        }

        function showAddTaskModal() {
            Swal.fire({
                title: 'Add New Task',
                html: `
                <div style="text-align: left;">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Task Title *</label>
                        <input type="text" placeholder="Enter task title" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Description *</label>
                        <textarea placeholder="Enter task description" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Priority *</label>
                            <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Due Date</label>
                            <input type="date" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Vehicle *</label>
                        <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <option value="">Select a vehicle</option>
                            <option value="1">Toyota Camry - VH001</option>
                            <option value="2">Honda Civic - VH002</option>
                            <option value="3">Nissan Altima - VH003</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Assign To *</label>
                        <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <option value="">Select a user</option>
                            <option value="1">John Doe (Employee)</option>
                            <option value="2">Jane Smith (Employee)</option>
                            <option value="3">Manager User (Manager)</option>
                        </select>
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Add Task',
                cancelButtonText: 'Cancel',
                confirmButtonColor: 'hsl(var(--primary))',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast('Task added successfully', 'success');
                }
            });
        }

        function viewTask(id) {
            showToast(`Viewing task ${id}`, 'info');
        }

        function editTask(id) {
            showToast(`Editing task ${id}`, 'info');
        }

        function startTask(id) {
            Swal.fire({
                title: 'Start Task?',
                text: 'Are you sure you want to start this task?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'hsl(var(--primary))',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, start it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast('Task started successfully', 'success');
                }
            });
        }

        function completeTask(id) {
            Swal.fire({
                title: 'Complete Task?',
                text: 'Are you sure you want to mark this task as completed?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast('Task completed successfully', 'success');
                }
            });
        }

        // Add row hover effects
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.table tbody tr').forEach(row => {
                row.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateX(4px)';
                    this.style.transition = 'transform 0.2s ease';
                });

                row.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>
@endsection