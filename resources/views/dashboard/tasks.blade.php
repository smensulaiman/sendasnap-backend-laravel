@extends('layouts.app')

@section('title', 'Schedule')

@section('content')
    <!-- Header Section -->
    <div class="mb-4">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="title-lg mb-1">Task Management</div>
                    <p class="text-muted mb-0">Organize and track your tasks efficiently</p>
                </div>
                <div class="flex gap-3">
                    <x-button variant="outline" onclick="showFilters()">
                        <span class="material-symbols-rounded">tune</span>
                        Filters
                    </x-button>
                    <x-button variant="primary" onclick="openDrawer('taskDrawer')">
                        <span class="material-symbols-rounded">add</span>
                        Add Task
                    </x-button>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Add Task Drawer -->
    <x-side-drawer id="taskDrawer" title="Create Task" width="320px">
        <form id="createTaskFormDashboard" onsubmit="return false;" class="d-flex flex-column" style="gap: 12px;">
            <div>
                <label class="text-sm font-medium mb-1" style="display:block;">Task Title</label>
                <input id="taskTitleDash" type="text" class="input" placeholder="Enter task title" />
            </div>
            <div>
                <label class="text-sm font-medium mb-1" style="display:block;">Description</label>
                <textarea id="taskDescriptionDash" class="input" rows="5" placeholder="Add more details" style="resize: vertical;"></textarea>
            </div>

            <div class="d-flex" style="gap: 10px;">
                <div style="flex:1;">
                    <label class="text-sm font-medium mb-1" style="display:block;">Select Date</label>
                    <input id="taskDateDash" type="date" class="input" />
                </div>
                <div style="flex:1;">
                    <label class="text-sm font-medium mb-1" style="display:block;">Select Time</label>
                    <input id="taskTimeDash" type="time" class="input" />
                </div>
            </div>

            <div>
                <label class="text-sm font-medium mb-1" style="display:block;">Task Assignee</label>
                <select id="taskAssigneeDash" class="input">
                    <option value="">Select assignee</option>
                    @foreach(\App\Models\User::query()->orderBy('name')->get(['id','name']) as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium mb-1" style="display:block;">Task Status</label>
                <select id="taskStatusDash" name="status" class="input">
                    <option value="running" selected>In Progress</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div style="border:1px dashed hsl(var(--border)); border-radius:8px; padding:10px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="font-medium" style="font-size:14px;">File Attachments</div>
                        <div class="text-xs text-muted">Add files, images, or documents</div>
                    </div>
                    <div>
                        <input id="taskDrawerFilesDashboard" type="file" multiple style="display:none;" />
                        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('taskDrawerFilesDashboard').click()">
                            <span class="material-symbols-rounded" style="font-size:16px;">attach_file_add</span>
                            Add File
                        </button>
                    </div>
                </div>
                <ul id="taskDrawerFilesListDashboard" style="margin:10px 0 0; padding:0; list-style:none; display:none;"></ul>
            </div>

            <div class="d-flex align-items-center justify-content-end" style="gap: 8px; margin-top: 6px;">
                <x-button variant="outline" onclick="closeDrawer('taskDrawer')">Cancel</x-button>
                <x-button variant="primary" onclick="submitDashboardTask()">Create Task</x-button>
            </div>
        </form>
    </x-side-drawer>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <x-card>
            <div class="card-body flex justify-between items-start">
                <div>
                    <div class="title-xl mb-1">{{ $stats['total_tasks'] ?? 0 }}</div>
                    <div class="text-muted text-sm">Total Tasks</div>
                    <div class="flex items-center gap-1 text-sm mt-2" style="color: hsl(var(--secondary));">
                        <span class="material-symbols-rounded" style="font-size: 16px;">trending_up</span>
                        <span>+8% from last week</span>
                    </div>
                </div>
                <span class="material-symbols-rounded" style="font-size: 32px; color: hsl(var(--primary) / .6);">task_alt</span>
            </div>
        </x-card>
        <x-card>
            <div class="card-body flex justify-between items-start">
                <div>
                    <div class="title-xl mb-1">{{ $stats['pending_tasks'] ?? 0 }}</div>
                    <div class="text-muted text-sm">Pending Tasks</div>
                    <div class="flex items-center gap-1 text-sm mt-2" style="color: hsl(var(--danger));">
                        <span class="material-symbols-rounded" style="font-size: 16px;">schedule</span>
                        <span>Awaiting action</span>
                    </div>
                </div>
                <span class="material-symbols-rounded" style="font-size: 32px; color: hsl(var(--primary) / .6);">schedule</span>
            </div>
        </x-card>
        <x-card>
            <div class="card-body flex justify-between items-start">
                <div>
                    <div class="title-xl mb-1">{{ $stats['completed_tasks'] ?? 0 }}</div>
                    <div class="text-muted text-sm">Completed Tasks</div>
                    <div class="flex items-center gap-1 text-sm mt-2" style="color: hsl(var(--secondary));">
                        <span class="material-symbols-rounded" style="font-size: 16px;">trending_up</span>
                        <span>+15% this week</span>
                    </div>
                </div>
                <span class="material-symbols-rounded" style="font-size: 32px; color: hsl(var(--primary) / .6);">check_circle</span>
            </div>
        </x-card>
        <x-card>
            <div class="card-body flex justify-between items-start">
                <div>
                    <div class="title-xl mb-1">{{ $stats['running_tasks'] ?? 0 }}</div>
                    <div class="text-muted text-sm">Running Tasks</div>
                    <div class="flex items-center gap-1 text-sm mt-2" style="color: hsl(var(--secondary));">
                        <span class="material-symbols-rounded" style="font-size: 16px;">play_arrow</span>
                        <span>Active now</span>
                    </div>
                </div>
                <span class="material-symbols-rounded" style="font-size: 32px; color: hsl(var(--primary) / .6);">play_circle</span>
            </div>
        </x-card>
    </div>

    <!-- Chart and Recent Tasks -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4 items-stretch">
        <x-card :title="'Task Status Distribution'" style="height: 100%; min-height: 420px;">
            <x-donut-chart id="taskStatusChart"
                :labels="['Pending', 'Running', 'Completed']"
                :data="[($stats['pending_tasks'] ?? 0), ($stats['running_tasks'] ?? 0), ($stats['completed_tasks'] ?? 0)]"
                :colors="['hsl(0 84% 60%)', 'hsl(220 90% 40%)', 'hsl(160 75% 40%)']"
                :legend="true"
                height="100%"
                minHeight="420px" />
        </x-card>
        <x-card :title="'Recent Tasks'">
            <div class="p-0">
                @if(isset($recent_tasks) && $recent_tasks->count() > 0)
                    @foreach($recent_tasks as $task)
                        <div class="p-3 border-b border-border last:border-b-0">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-medium">{{ $task->title }}</div>
                                <x-badge variant="{{ in_array($task->priority, ['urgent','high']) ? 'danger' : ($task->priority === 'medium' ? 'secondary' : 'primary') }}">
                                    {{ ucfirst($task->priority) }}
                                </x-badge>
                            </div>
                            <div class="text-sm text-muted mb-2">
                                {{ Str::limit($task->description, 60) }}
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-xs font-medium">
                                        {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                    </div>
                                    <span class="text-muted">{{ $task->assignedUser->name }}</span>
                                </div>
                                <x-badge variant="{{ $task->status === 'completed' ? 'secondary' : ($task->status === 'running' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst($task->status) }}
                                </x-badge>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-8 text-center">
                        <span class="material-symbols-rounded text-muted" style="font-size: 48px; opacity: 0.3;">task_alt</span>
                        <p class="text-muted mt-2">No recent tasks found.</p>
                    </div>
                @endif
            </div>
        </x-card>
    </div>

    <!-- My Tasks Section (for non-admin users) -->
    @if(isset($my_tasks) && $my_tasks->isNotEmpty())
        <x-card class="mb-4">
            <x-slot:title>
                <div class="d-flex align-items-center justify-content-between w-100">
                    <span class="title-md">My Tasks</span>
                    <x-badge variant="primary">{{ $my_tasks->count() }} tasks</x-badge>
                </div>
            </x-slot:title>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($my_tasks as $task)
                        <div class="p-4 border border-border rounded-lg hover:shadow-sm transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div class="font-semibold">{{ $task->title }}</div>
                                <div class="flex gap-2">
                                    <x-badge variant="{{ in_array($task->priority, ['urgent','high']) ? 'danger' : ($task->priority === 'medium' ? 'secondary' : 'primary') }}">
                                        {{ ucfirst($task->priority) }}
                                    </x-badge>
                                    <x-badge variant="{{ $task->status === 'completed' ? 'secondary' : ($task->status === 'running' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($task->status) }}
                                    </x-badge>
                                </div>
                            </div>
                            <div class="text-sm text-muted mb-4">
                                {{ Str::limit($task->description, 100) }}
                            </div>
                            <div class="flex justify-between items-center mb-4 text-xs text-muted">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">directions_car</span>
                                    {{ $task->vehicle->make }} {{ $task->vehicle->model }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">calendar_month</span>
                                    {{ $task->due_date ? $task->due_date->format('M d') : 'No due date' }}
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <x-button variant="outline" size="sm" class="flex-1">
                                    <span class="material-symbols-rounded mr-1" style="font-size: 16px;">visibility</span>
                                    View
                                </x-button>
                                <x-button variant="outline" size="sm">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                                </x-button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-card>
    @endif

    <!-- All Tasks Table -->
    <x-card>
        <x-slot:title>
            <div class="d-flex align-items-center justify-content-between w-100">
                <span class="title-md">All Tasks</span>
                <div class="d-flex gap-3">
                    <x-button variant="outline" onclick="showFilters()">
                        <span class="material-symbols-rounded">tune</span>
                        Filters
                    </x-button>
                    <x-button variant="primary" onclick="showAddTaskModal()">
                        <span class="material-symbols-rounded">add</span>
                        Add Task
                    </x-button>
                </div>
            </div>
        </x-slot:title>
        <div class="p-0">
            @if($tasks->count() > 0)
                <x-table :headers="['Task', 'Vehicle', 'Priority', 'Status', 'Assigned To', 'Due Date', 'Actions']">
                    @foreach($tasks as $task)
                        <tr>
                            <td>
                                <div>
                                    <div class="font-semibold">{{ $task->title }}</div>
                                    <div class="text-sm text-muted">{{ Str::limit($task->description, 50) }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-muted" style="font-size: 16px;">directions_car</span>
                                    <div>
                                        <div class="font-medium">{{ $task->vehicle->make }} {{ $task->vehicle->model }}</div>
                                        <div class="text-xs text-muted">{{ $task->vehicle->serial_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <x-badge variant="{{ in_array($task->priority, ['urgent','high']) ? 'danger' : ($task->priority === 'medium' ? 'secondary' : 'primary') }}">
                                    {{ ucfirst($task->priority) }}
                                </x-badge>
                            </td>
                            <td>
                                <x-badge variant="{{ $task->status === 'completed' ? 'secondary' : ($task->status === 'running' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst($task->status) }}
                                </x-badge>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold">
                                        {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $task->assignedUser->name }}</div>
                                        <div class="text-xs text-muted">{{ ucfirst($task->assignedUser->role) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($task->due_date)
                                    <div class="font-medium">{{ $task->due_date->format('M d, Y') }}</div>
                                    <div class="text-xs">
                                        @if($task->due_date->isPast())
                                            <span class="text-red-500">Overdue</span>
                                        @elseif($task->due_date->isToday())
                                            <span style="color: #f59e0b;">Due today</span>
                                        @elseif($task->due_date->diffInDays() <= 3)
                                            <span style="color: #f59e0b;">Due soon</span>
                                        @else
                                            <span style="color: #10b981;">On track</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted text-sm">No due date</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-1">
                                    <x-button variant="outline" size="sm" onclick="viewTask({{ $task->id }})">
                                        <span class="material-symbols-rounded" style="font-size: 16px;">visibility</span>
                                    </x-button>
                                    <x-button variant="outline" size="sm" onclick="editTask({{ $task->id }})">
                                        <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                                    </x-button>
                                    @if($task->status === 'pending')
                                        <x-button variant="primary" size="sm" onclick="startTask({{ $task->id }})">
                                            <span class="material-symbols-rounded" style="font-size: 16px;">play_arrow</span>
                                        </x-button>
                                    @elseif($task->status === 'running')
                                        <x-button variant="primary" size="sm" onclick="completeTask({{ $task->id }})">
                                            <span class="material-symbols-rounded" style="font-size: 16px;">check</span>
                                        </x-button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="p-4">
                    {{ $tasks->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <span class="material-symbols-rounded text-muted" style="font-size: 48px; opacity: 0.3;">task_alt</span>
                    <h3 class="title-md mt-4 mb-2">No tasks found</h3>
                    <p class="text-muted mb-4">Get started by creating your first task.</p>
                    <x-button variant="primary" onclick="showAddTaskModal()">
                        <span class="material-symbols-rounded mr-2">add</span>
                        Add Task
                    </x-button>
                </div>
            @endif
        </div>
    </x-card>

    @verbatim
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
            // donut chart rendered by <x-donut-chart>

            // Add click animations to stat cards
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('click', function () {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });

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

        // File attachments preview - Dashboard drawer
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('taskDrawerFilesDashboard');
            const list = document.getElementById('taskDrawerFilesListDashboard');
            if (fileInput && list) {
                fileInput.addEventListener('change', () => {
                    list.innerHTML = '';
                    if (fileInput.files.length === 0) {
                        list.style.display = 'none';
                        return;
                    }
                    Array.from(fileInput.files).forEach((f, idx) => {
                        const li = document.createElement('li');
                        li.style.display = 'flex';
                        li.style.alignItems = 'center';
                        li.style.justifyContent = 'space-between';
                        li.style.padding = '6px 8px';
                        li.style.border = '1px solid var(--border)';
                        li.style.borderRadius = '6px';
                        li.style.marginBottom = '6px';
                        li.innerHTML = `<span style="font-size:13px;">${f.name}</span><button type="button" class="btn btn-outline btn-sm" data-index="${idx}"><span class="material-symbols-rounded" style="font-size:16px;">delete</span></button>`;
                        list.appendChild(li);
                    });
                    list.style.display = '';

                    list.querySelectorAll('button').forEach(btn => {
                        btn.addEventListener('click', () => {
                            fileInput.value = '';
                            list.innerHTML = '';
                            list.style.display = 'none';
                        });
                    });
                });
            }
        });

        async function submitDashboardTask() {
            const getCookie = (name) => {
                const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
                return match ? decodeURIComponent(match[1]) : null;
            };
            const title = document.getElementById('taskTitleDash')?.value?.trim();
            const description = document.getElementById('taskDescriptionDash')?.value?.trim();
            const workDate = document.getElementById('taskDateDash')?.value;
            const workTime = document.getElementById('taskTimeDash')?.value;
            const assigneeId = document.getElementById('taskAssigneeDash')?.value;
            const status = document.getElementById('taskStatusDash')?.value || 'running';
            const filesInput = document.getElementById('taskDrawerFilesDashboard');

            if (!title) { Swal.fire('Title required', 'Please enter a task title', 'warning'); return; }

            const formData = new FormData();
            formData.append('title', title);
            if (description) formData.append('description', description);
            if (workDate) formData.append('work_date', workDate);
            if (workTime) formData.append('work_time', workTime);
            if (assigneeId) formData.append('assigned_user_id', assigneeId);
            formData.append('status', status);
            if (filesInput && filesInput.files && filesInput.files.length) {
                Array.from(filesInput.files).forEach((f) => formData.append('attachments[]', f));
            }

            try {
                await fetch('/sanctum/csrf-cookie', { credentials: 'include' });
                let response = await fetch('/api/v1/tasks', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: formData,
                });
                if (response.status === 401) {
                    const tokRes = await fetch(@json(route('tokens.create')), {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
                        credentials: 'include'
                    });
                    if (tokRes.ok) {
                        const tok = await tokRes.json();
                        if (tok?.token) {
                            response = await fetch('/api/v1/tasks', {
                                method: 'POST',
                                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${tok.token}` },
                                body: formData,
                            });
                        }
                    }
                }
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data?.message || 'Failed to create task');
                Swal.fire('Created', 'Task created successfully', 'success');
                closeDrawer('taskDrawer');
                window.location.reload();
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
    </script>
    @endverbatim
@endsection
