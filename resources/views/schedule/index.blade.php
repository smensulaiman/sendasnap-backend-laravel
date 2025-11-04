@extends('layouts.app')

@section('title', 'Schedule')

@section('content')
    <div class="mb-4">
        <x-card>
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="title-lg mb-1">Schedule Calendar</div>
                    <p class="text-muted mb-0">Select a date or date range to view tasks and schedules</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <x-button variant="outline" size="sm" onclick="toggleScheduleSearch()" id="toggleSearchBtn">
                        <span class="material-symbols-rounded" id="toggleSearchIcon">expand_more</span>
                        Filters
                    </x-button>
                    <x-button variant="outline" size="sm" :href="route('schedule.index')">
                        <span class="material-symbols-rounded">refresh</span>
                        Reset
                    </x-button>
                </div>
            </div>

            <!-- Date Range Filter (Flight-style) -->
            <div id="scheduleSearch" class="d-flex align-items-center gap-3 p-3"
                style="display: none; background: hsl(var(--muted)); border-radius: 8px; position: relative;">
                <!-- Hidden real inputs for values -->
                <input type="hidden" id="startDate" value="{{ $date ?? now()->format('Y-m-d') }}">
                <input type="hidden" id="endDate" value="{{ $date ?? now()->format('Y-m-d') }}">

                <div class="d-flex align-items-center justify-content-between" style="flex: 1; gap: 8px;">
                    <div style="flex: 1;">
                        <label class="text-sm font-medium mb-1" style="display: block;">Date Range</label>
                        <div id="dateRangeDisplay" class="d-flex align-items-center justify-content-between"
                            style="width: 100%; height: 40px; border: 1px solid hsl(var(--border)); border-radius: 8px; padding: 0 10px; background: hsl(var(--card)); cursor: pointer;">
                            <span id="dateRangeText" class="text-sm">{{ now()->format('M d, Y') }} -
                                {{ now()->format('M d, Y') }}</span>
                            <span class="material-symbols-rounded"
                                style="font-size: 18px; color: hsl(var(--muted-foreground));">date_range</span>
                        </div>
                    </div>
                    <div style="padding-top: 24px; white-space: nowrap;">
                        <x-button variant="primary" onclick="applyDateRange()">
                            <span class="material-symbols-rounded">search</span>
                            Apply Range
                        </x-button>
                    </div>
                    <div style="padding-top: 24px; white-space: nowrap;">
                        <x-button variant="outline" onclick="clearDateRange()">
                            <span class="material-symbols-rounded">clear</span>
                            Clear
                        </x-button>
                    </div>
                </div>

                <!-- Popup range picker -->
                <div id="rangePicker"
                    style="display: none; position: absolute; top: 70px; left: 16px; z-index: 50; background: hsl(var(--surface)); border: 1px solid hsl(var(--border)); border-radius: 10px; box-shadow: 0 16px 40px rgba(0,0,0,.12); padding: 12px;">
                    <div class="d-flex align-items-stretch" style="gap: 12px;">
                        <!-- Left calendar -->
                        <div style="width: 280px;">
                            <div class="d-flex align-items-center justify-content-between" style="padding: 8px 6px;">
                                <button class="btn-icon" onclick="rangePrevMonth()"><span
                                        class="material-symbols-rounded">chevron_left</span></button>
                                <div id="rangeMonthLeft" class="text-sm"
                                    style="font-weight:700; font-family: 'Montserrat', sans-serif;"></div>
                                <span style="width: 36px;"></span>
                            </div>
                            <div id="rangeCalLeft" class="calendar-container"></div>
                        </div>
                        <!-- Right calendar -->
                        <div style="width: 280px;">
                            <div class="d-flex align-items-center justify-content-between" style="padding: 8px 6px;">
                                <span style="width: 36px;"></span>
                                <div id="rangeMonthRight" class="text-sm"
                                    style="font-weight:700; font-family: 'Montserrat', sans-serif;"></div>
                                <button class="btn-icon" onclick="rangeNextMonth()"><span
                                        class="material-symbols-rounded">chevron_right</span></button>
                            </div>
                            <div id="rangeCalRight" class="calendar-container"></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end" style="gap: 8px; padding-top: 8px;">
                        <x-button variant="outline" size="sm" onclick="closeRangePicker()">Cancel</x-button>
                        <x-button variant="primary" size="sm" onclick="applyRangeFromPicker()">Apply</x-button>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Add Task Drawer -->
    <x-side-drawer id="taskDrawer" title="Create Task" width="320px">
        <form id="createTaskForm" onsubmit="return false;" class="d-flex flex-column" style="gap: 12px;">
            <div>
                <label class="text-sm font-medium mb-1" style="display:block;">Task Title</label>
                <input id="taskTitleSched" type="text" class="input" placeholder="Enter task title" />
            </div>
            <div>
                <label class="text-sm font-medium mb-1" style="display:block;">Description</label>
                <textarea id="taskDescriptionSched" class="input" rows="5" placeholder="Add more details"
                    style="resize: vertical;"></textarea>
            </div>

            <div class="d-flex" style="gap: 10px;">
                <div style="flex:1;">
                    <label class="text-sm font-medium mb-1" style="display:block;">Select Date</label>
                    <input id="taskDateSched" type="date" class="input" />
                </div>
                <div style="flex:1;">
                    <label class="text-sm font-medium mb-1" style="display:block;">Select Time</label>
                    <input id="taskTimeSched" type="time" class="input" />
                </div>
            </div>

            <div>
                <label class="text-sm font-medium mb-1" style="display:block;">Task Assignee</label>
                <select id="taskAssigneeSched" class="input">
                    <option value="">Select assignee</option>
                    @foreach(\App\Models\User::query()->orderBy('name')->get(['id', 'name']) as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium mb-1" style="display:block;">Task Status</label>
                <select id="taskStatusSched" name="status" class="input">
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
                        <input id="taskDrawerFiles" type="file" multiple style="display:none;" />
                        <button type="button" class="btn btn-outline btn-sm"
                            onclick="document.getElementById('taskDrawerFiles').click()">
                            <span class="material-symbols-rounded" style="font-size:16px;">attach_file_add</span>
                            Add File
                        </button>
                    </div>
                </div>
                <ul id="taskDrawerFilesList" style="margin:10px 0 0; padding:0; list-style:none; display:none;"></ul>
            </div>

            <div class="d-flex align-items-center justify-content-end" style="gap: 8px; margin-top: 6px;">
                <x-button variant="outline" onclick="closeDrawer('taskDrawer')">Cancel</x-button>
                <x-button variant="primary" onclick="submitScheduleTask()">Create Task</x-button>
            </div>
        </form>
    </x-side-drawer>

    <!-- Calendar View -->
    <div class="schedule-grid">
        <!-- Column 1: Calendar (rowspan 2) -->
        <div class="calendar-col">
            <x-card>
                <x-slot:title>
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span class="title-md">Calendar</span>
                        <div class="d-flex gap-2">
                            <button id="prevMonth" class="btn-icon" title="Previous Month">
                                <span class="material-symbols-rounded">chevron_left</span>
                            </button>
                            <button id="nextMonth" class="btn-icon" title="Next Month">
                                <span class="material-symbols-rounded">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </x-slot:title>
                <div class="p-0">
                    <div id="calendar" class="calendar-container" style="padding: 12px;"></div>
                </div>
            </x-card>
        </div>

        <!-- Right Column (col-8 equivalent) -->
        <div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3" style="--rtH: 360px;">
                <!-- Column 2: Stats Cards (2x2) -->
                <div>
                    <div class="grid"
                        style="height: var(--rtH); display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; gap: 12px;">
                        <x-card style="height: 100%;">
                            <div>
                                <div class="text-sm text-muted mb-2">Total Tasks</div>
                                <div class="title-xl" data-stat="total">{{ $stats['total'] }}</div>
                                <div class="text-xs text-muted mt-1">All scheduled</div>
                            </div>
                        </x-card>
                        <x-card style="height: 100%;">
                            <div>
                                <div class="text-sm text-muted mb-2">Pending</div>
                                <div class="title-xl" data-stat="pending" style="color: hsl(var(--danger));">
                                    {{ $stats['pending'] }}
                                </div>
                                <div class="text-xs text-muted mt-1">Awaiting action</div>
                            </div>
                        </x-card>
                        <x-card style="height: 100%;">
                            <div>
                                <div class="text-sm text-muted mb-2">Running</div>
                                <div class="title-xl" data-stat="running" style="color: hsl(var(--primary));">
                                    {{ $stats['running'] }}
                                </div>
                                <div class="text-xs text-muted mt-1">In progress</div>
                            </div>
                        </x-card>
                        <x-card style="height: 100%;">
                            <div>
                                <div class="text-sm text-muted mb-2">Completed</div>
                                <div class="title-xl" data-stat="completed" style="color: hsl(var(--secondary));">
                                    {{ $stats['completed'] }}
                                </div>
                                <div class="text-xs text-muted mt-1">Done today</div>
                            </div>
                        </x-card>
                    </div>
                </div>

                <!-- Column 3: Task Distribution -->
                <div>
                    <x-card style="height: var(--rtH);">
                        <x-donut-chart id="scheduleChart" :labels="['Pending', 'Running', 'Completed']"
                            :data="[$stats['pending'], $stats['running'], $stats['completed']]" :colors="['hsl(0 84% 60%)', 'hsl(220 90% 40%)', 'hsl(160 75% 40%)']" :legend="true" height="100%" />
                    </x-card>
                </div>
            </div>

            <!-- Row 2: Tasks List inside right column -->
            <x-card class="mt-4">
                <x-slot:title>
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <span class="title-md">Tasks</span>
                            <span class="text-sm text-muted ml-2" id="selectedDateDisplay">
                                {{ $date ? \Carbon\Carbon::parse($date)->toFormattedDateString() : 'All dates' }}
                            </span>
                        </div>
                        <x-button variant="primary" onclick="openDrawer('taskDrawer')">
                            <span class="material-symbols-rounded">add</span>
                            Add Task
                        </x-button>
                    </div>
                </x-slot:title>
                <div class="p-0">

                    <div id="scheduleTable">
                        <x-table :headers="['Date & Time', 'Title', 'Assignee', 'Vehicle', 'Priority', 'Status', 'Actions']">
                            @forelse($tasks as $task)
                                <tr>
                                    <td>
                                        @if($task->work_date)
                                            <div class="font-medium">{{ $task->work_date->format('M d, Y') }}</div>
                                            <div class="text-sm text-muted">
                                                {{ optional($task->work_time)->format('h:i A') ?? 'Time not set' }}
                                            </div>
                                        @else
                                            <div class="text-muted">Not scheduled</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-medium">{{ $task->title }}</div>
                                        @if($task->description)
                                            <div class="text-sm text-muted">{{ Str::limit($task->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->assignedUser)
                                            <div class="d-flex align-items-center gap-2">
                                                <div
                                                    style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; background: hsl(var(--muted)); display: flex; align-items: center; justify-content: center;">
                                                    @if($task->assignedUser->avatar)
                                                        <img src="{{ asset('storage/' . $task->assignedUser->avatar) }}"
                                                            alt="{{ $task->assignedUser->name }}"
                                                            style="width: 100%; height: 100%; object-fit: cover;">
                                                    @else
                                                        <div
                                                            style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: hsl(var(--primary) / 0.1); color: hsl(var(--primary)); font-weight: 700; font-size: 14px;">
                                                            {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-medium" style="font-size: 14px;">
                                                        {{ $task->assignedUser->name }}
                                                    </div>
                                                    <div class="text-xs text-muted">{{ ucfirst($task->assignedUser->role) }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($task->vehicle)->serial_number ?? 'No vehicle' }}</td>
                                    <td>
                                        <x-badge :variant="match ($task->priority) { 'high' => 'danger', 'urgent' => 'danger', 'medium' => 'secondary', 'low' => 'primary', default => 'primary'}">
                                            {{ ucfirst($task->priority) }}
                                        </x-badge>
                                    </td>
                                    <td>
                                        <x-badge :variant="match ($task->status) { 'completed' => 'secondary', 'running' => 'primary', 'cancelled' => 'danger', default => 'secondary'}">
                                            {{ ucfirst($task->status) }}
                                        </x-badge>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <x-button variant="outline" size="sm" onclick="viewTask({{ $task->id }})">
                                                <span class="material-symbols-rounded"
                                                    style="font-size: 16px;">visibility</span>
                                            </x-button>
                                            <x-button variant="outline" size="sm" onclick="editTask({{ $task->id }})">
                                                <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                                            </x-button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center p-4">
                                        <x-empty-state title="No tasks found"
                                            message="Select a date from the calendar to view tasks." />
                                    </td>
                                </tr>
                            @endforelse
                        </x-table>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <style>
        .calendar-container {
            padding: 16px;
        }

        /* Full-width schedule grid with col-4 / col-8 on large screens */
        .schedule-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        @media (min-width: 1024px) {
            .schedule-grid {
                grid-template-columns: 1fr 2fr;
            }

            .schedule-grid .calendar-col {
                grid-row: span 2;
            }
        }

        /* Force solid background for range picker (no transparency) */
        #rangePicker {
            background: #ffffff !important;
            background: hsl(var(--surface)) !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
        }

        /* Range picker day styles */
        .rp-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .rp-day,
        .rp-dow {
            text-align: center;
            font-size: 12px;
            font-weight: 600;
        }

        .rp-dow {
            color: hsl(var(--text-muted));
            padding: 6px 0;
        }

        .rp-day {
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            color: hsl(var(--text));
            position: relative;
        }

        .rp-day.disabled {
            opacity: .35;
            cursor: default;
        }

        .rp-day:hover {
            background: hsl(var(--muted));
        }

        .rp-day.selected {
            background: hsl(var(--primary));
            color: white;
            font-weight: 700;
        }

        .rp-day.in-range {
            background: hsl(var(--primary) / .12);
        }

        .rp-day.range-start {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .rp-day.range-end {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .calendar-header {
            text-align: center;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            margin-bottom: 16px;
            color: hsl(var(--text));
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .calendar-day-header {
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            color: hsl(var(--text-muted));
            padding: 8px 4px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            color: hsl(var(--text));
        }

        .calendar-day:hover {
            background: hsl(var(--muted));
            border-color: hsl(var(--primary) / 0.3);
        }

        .calendar-day.other-month {
            color: hsl(var(--text-muted));
            opacity: 0.4;
        }

        .calendar-day.today {
            background: hsl(var(--primary) / 0.1);
            color: hsl(var(--primary));
            font-weight: 700;
        }

        .calendar-day.selected {
            background: hsl(var(--primary));
            color: white;
            font-weight: 700;
        }

        .calendar-day.has-tasks::after {
            content: '';
            position: absolute;
            bottom: 4px;
            width: 4px;
            height: 4px;
            background: hsl(var(--secondary));
            border-radius: 50%;
        }

        .calendar-day {
            position: relative;
        }
    </style>

    <!-- Chart.js (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        let scheduleChart;
        let currentMonth = new Date();

        // Range picker state
        let rpBaseMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
        let rangeStart = null;
        let rangeEnd = null;

        // Set selected date to today by default or use the passed date
        const dateStr = '{{ $date }}' || new Date().toISOString().split('T')[0];
        const parts = dateStr.split('-');
        let selectedDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        selectedDate.setHours(0, 0, 0, 0);

        let isDateRangeMode = false;

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize calendar with today's date selected
            renderCalendar();

            // Initialize chart
            initializeChart();

            // Setup range picker toggle
            const drDisplay = document.getElementById('dateRangeDisplay');
            const rp = document.getElementById('rangePicker');
            if (drDisplay && rp) {
                drDisplay.addEventListener('click', () => {
                    openRangePicker();
                });
            }
            // Initialize default display text
            updateDateRangeText();
            // Collapse search by default
            const search = document.getElementById('scheduleSearch');
            if (search) search.style.display = 'none';

            // Update the selected date display to show today's date
            const displayEl = document.getElementById('selectedDateDisplay');
            if (displayEl) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                displayEl.textContent = selectedDate.toLocaleDateString('en-US', options);
            }

            // Calendar navigation
            document.getElementById('prevMonth').addEventListener('click', () => {
                currentMonth.setMonth(currentMonth.getMonth() - 1);
                renderCalendar();
            });

            document.getElementById('nextMonth').addEventListener('click', () => {
                currentMonth.setMonth(currentMonth.getMonth() + 1);
                renderCalendar();
            });
        });

        function toggleScheduleSearch() {
            const search = document.getElementById('scheduleSearch');
            const icon = document.getElementById('toggleSearchIcon');
            if (!search) return;
            const isHidden = search.style.display === 'none';
            search.style.display = isHidden ? 'flex' : 'none';
            if (icon) icon.textContent = isHidden ? 'expand_less' : 'expand_more';
        }

        // File attachments preview - Schedule drawer
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('taskDrawerFiles');
            const list = document.getElementById('taskDrawerFilesList');
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

                    // Remove handler: reset input to remove all files for simplicity
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

        async function submitScheduleTask() {
            const getCookie = (name) => {
                const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
                return match ? decodeURIComponent(match[1]) : null;
            };
            const title = document.getElementById('taskTitleSched')?.value?.trim();
            const description = document.getElementById('taskDescriptionSched')?.value?.trim();
            const workDate = document.getElementById('taskDateSched')?.value;
            const workTime = document.getElementById('taskTimeSched')?.value;
            const assigneeId = document.getElementById('taskAssigneeSched')?.value;
            const status = document.getElementById('taskStatusSched')?.value || 'running';
            const filesInput = document.getElementById('taskDrawerFiles');

            if (!title) { Swal.fire('Title required', 'Please enter a task title', 'warning'); return; }

            const formData = new FormData();
            formData.append('title', title);
            if (description) formData.append('description', description);
            if (workDate) formData.append('work_date', workDate);
            if (workTime) formData.append('work_time', workTime);
            if (assigneeId) formData.append('assigned_user_id', assigneeId);
            formData.append('status', status);

            if (filesInput && filesInput.files && filesInput.files.length) {
                Array.from(filesInput.files).forEach((f, i) => formData.append('attachments[]', f));
            }

            try {
                // Ensure Sanctum CSRF cookie is set
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
                // Fallback: if 401, ask for API token and retry with Bearer token
                if (response.status === 401) {
                    // Try to auto-generate a token via web route (authenticated by session)
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
                if (!response.ok) {
                    const msg = data?.message || 'Failed to create task';
                    throw new Error(msg);
                }
                Swal.fire('Created', 'Task created successfully', 'success');
                closeDrawer('taskDrawer');
                // Refresh today or current selected date
                if (selectedDate) {
                    const y = selectedDate.getFullYear();
                    const m = String(selectedDate.getMonth() + 1).padStart(2, '0');
                    const d = String(selectedDate.getDate()).padStart(2, '0');
                    fetchScheduleData(`${y}-${m}-${d}`);
                } else {
                    fetchScheduleData();
                }
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }

        function applyDateRange() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            console.log('Applying date range:', startDate, 'to', endDate);
            isDateRangeMode = true;

            // Update display
            const displayEl = document.getElementById('selectedDateDisplay');
            if (displayEl) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                displayEl.textContent = `${start.toLocaleDateString('en-US', options)} - ${end.toLocaleDateString('en-US', options)}`;
            }

            // Fetch data for date range
            fetchScheduleDataRange(startDate, endDate);
        }

        function clearDateRange() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            isDateRangeMode = false;
            rangeStart = null;
            rangeEnd = null;
            updateDateRangeText();

            // Reset to today's data
            const today = new Date();
            const todayStr = today.toISOString().split('T')[0];
            selectDate(todayStr);
        }

        function openRangePicker() {
            const rp = document.getElementById('rangePicker');
            if (!rp) return;
            rp.style.display = 'block';
            // Initialize base month to current month
            rpBaseMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
            renderRangeCalendars();
        }

        function closeRangePicker() {
            const rp = document.getElementById('rangePicker');
            if (!rp) return;
            rp.style.display = 'none';
        }

        function rangePrevMonth() {
            rpBaseMonth.setMonth(rpBaseMonth.getMonth() - 1);
            renderRangeCalendars();
        }

        function rangeNextMonth() {
            rpBaseMonth.setMonth(rpBaseMonth.getMonth() + 1);
            renderRangeCalendars();
        }

        function renderRangeCalendars() {
            const leftMonth = new Date(rpBaseMonth.getFullYear(), rpBaseMonth.getMonth(), 1);
            const rightMonth = new Date(rpBaseMonth.getFullYear(), rpBaseMonth.getMonth() + 1, 1);

            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];

            document.getElementById('rangeMonthLeft').textContent = `${monthNames[leftMonth.getMonth()]} ${leftMonth.getFullYear()}`;
            document.getElementById('rangeMonthRight').textContent = `${monthNames[rightMonth.getMonth()]} ${rightMonth.getFullYear()}`;

            document.getElementById('rangeCalLeft').innerHTML = buildRangeCalendar(leftMonth);
            document.getElementById('rangeCalRight').innerHTML = buildRangeCalendar(rightMonth);
        }

        function buildRangeCalendar(firstOfMonth) {
            const year = firstOfMonth.getFullYear();
            const month = firstOfMonth.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startIndex = firstDay.getDay();
            const lastDate = lastDay.getDate();
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            let html = '<div class="rp-grid">';
            dayNames.forEach(d => html += `<div class="rp-dow">${d}</div>`);
            for (let i = 0; i < startIndex; i++) {
                html += `<div class="rp-day disabled"></div>`;
            }
            for (let d = 1; d <= lastDate; d++) {
                const dateObj = new Date(year, month, d);
                dateObj.setHours(0, 0, 0, 0);
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

                let cls = 'rp-day';
                const inRange = (rangeStart && rangeEnd && dateObj >= rangeStart && dateObj <= rangeEnd);
                const isStart = (rangeStart && dateObj.getTime() === rangeStart.getTime());
                const isEnd = (rangeEnd && dateObj.getTime() === rangeEnd.getTime());
                if (isStart || isEnd) cls += ' selected';
                if (inRange) cls += ' in-range';
                if (isStart) cls += ' range-start';
                if (isEnd) cls += ' range-end';

                html += `<div class="${cls}" data-date="${dateStr}" onclick="onRangeDayClick('${dateStr}')">${d}</div>`;
            }
            const filled = startIndex + lastDate;
            const remainder = 7 - (filled % 7);
            if (remainder < 7) {
                for (let i = 0; i < remainder; i++) html += `<div class=\"rp-day disabled\"></div>`;
            }
            html += '</div>';
            return html;
        }

        function onRangeDayClick(dateStr) {
            const [y, m, d] = dateStr.split('-').map(n => parseInt(n));
            const clicked = new Date(y, m - 1, d);
            clicked.setHours(0, 0, 0, 0);

            if (!rangeStart || (rangeStart && rangeEnd)) {
                rangeStart = clicked;
                rangeEnd = null;
            } else if (clicked < rangeStart) {
                rangeEnd = rangeStart;
                rangeStart = clicked;
            } else {
                rangeEnd = clicked;
            }
            renderRangeCalendars();
        }

        function applyRangeFromPicker() {
            if (!rangeStart || !rangeEnd) {
                alert('Please select a start and end date');
                return;
            }
            const startStr = `${rangeStart.getFullYear()}-${String(rangeStart.getMonth() + 1).padStart(2, '0')}-${String(rangeStart.getDate()).padStart(2, '0')}`;
            const endStr = `${rangeEnd.getFullYear()}-${String(rangeEnd.getMonth() + 1).padStart(2, '0')}-${String(rangeEnd.getDate()).padStart(2, '0')}`;

            document.getElementById('startDate').value = startStr;
            document.getElementById('endDate').value = endStr;
            isDateRangeMode = true;
            updateDateRangeText();
            closeRangePicker();
            fetchScheduleDataRange(startStr, endStr);
        }

        function updateDateRangeText() {
            const el = document.getElementById('dateRangeText');
            const s = document.getElementById('startDate').value;
            const e = document.getElementById('endDate').value;
            if (!el) return;
            if (s && e) {
                const sd = new Date(s); const ed = new Date(e);
                const opts = { year: 'numeric', month: 'short', day: 'numeric' };
                el.textContent = `${sd.toLocaleDateString('en-US', opts)} - ${ed.toLocaleDateString('en-US', opts)}`;
            } else {
                const today = new Date();
                const opts = { year: 'numeric', month: 'short', day: 'numeric' };
                el.textContent = `${today.toLocaleDateString('en-US', opts)} - ${today.toLocaleDateString('en-US', opts)}`;
            }
        }

        async function fetchScheduleDataRange(startDate, endDate) {
            console.log('Fetching schedule data for range:', startDate, 'to', endDate);

            try {
                const url = new URL(@json(route('schedule.fetch')));
                url.searchParams.append('start_date', startDate);
                url.searchParams.append('end_date', endDate);

                console.log('Fetching from URL:', url.toString());

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Received data:', data);

                if (data.success) {
                    console.log('Updating table with schedules:', data.data.schedules);
                    updateScheduleTable(data.data.schedules);
                    updateStatsCards(data.data.stats);
                    updateChart(data.data.stats);
                } else {
                    console.error('API returned error:', data.message);
                    alert('Error: ' + (data.message || 'Failed to load schedule data'));
                }
            } catch (error) {
                console.error('Error fetching schedule data:', error);
                alert('Failed to load schedule data: ' + error.message);
            }
        }

        function renderCalendar() {
            const year = currentMonth.getFullYear();
            const month = currentMonth.getMonth();

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const prevLastDay = new Date(year, month, 0);

            const firstDayIndex = firstDay.getDay();
            const lastDayDate = lastDay.getDate();
            const prevLastDayDate = prevLastDay.getDate();

            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            let calendarHTML = `
                                                                                                                                                                                                <div class="calendar-header">
                                                                                                                                                                                                    ${monthNames[month]} ${year}
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="calendar-grid">
                                                                                                                                                                                            `;

            // Day headers
            dayNames.forEach(day => {
                calendarHTML += `<div class="calendar-day-header">${day}</div>`;
            });

            // Previous month days
            for (let i = firstDayIndex; i > 0; i--) {
                const date = prevLastDayDate - i + 1;
                calendarHTML += `<div class="calendar-day other-month">${date}</div>`;
            }

            // Current month days
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time for accurate comparison

            for (let date = 1; date <= lastDayDate; date++) {
                const fullDate = new Date(year, month, date);
                fullDate.setHours(0, 0, 0, 0); // Reset time

                // Format date as YYYY-MM-DD without timezone issues
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;

                const isToday = fullDate.getTime() === today.getTime();
                const isSelected = selectedDate &&
                    fullDate.getFullYear() === selectedDate.getFullYear() &&
                    fullDate.getMonth() === selectedDate.getMonth() &&
                    fullDate.getDate() === selectedDate.getDate();

                let classes = 'calendar-day';
                if (isToday) classes += ' today';
                if (isSelected) classes += ' selected';

                calendarHTML += `<div class="${classes}" onclick="selectDate('${dateStr}')">${date}</div>`;
            }

            // Next month days
            const remainingDays = 42 - (firstDayIndex + lastDayDate); // 6 rows * 7 days
            for (let date = 1; date <= remainingDays; date++) {
                calendarHTML += `<div class="calendar-day other-month">${date}</div>`;
            }

            calendarHTML += '</div>';

            document.getElementById('calendar').innerHTML = calendarHTML;
        }

        function selectDate(dateStr) {
            console.log('=== Date Selected ===');
            console.log('Date string:', dateStr);

            // Parse date string as local date (YYYY-MM-DD)
            const parts = dateStr.split('-');
            selectedDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            selectedDate.setHours(0, 0, 0, 0);

            console.log('Parsed date:', selectedDate);

            // Re-render calendar to show selection
            renderCalendar();

            // Fetch data for this date
            fetchScheduleData(dateStr);

            // Update selected date display
            const dateDisplay = document.getElementById('selectedDateDisplay');
            if (dateDisplay) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                dateDisplay.textContent = selectedDate.toLocaleDateString('en-US', options);
                console.log('Updated date display');
            } else {
                console.error('selectedDateDisplay element not found!');
            }
        }

        // Fetch schedule data from API
        async function fetchScheduleData(date = null) {
            console.log('Fetching schedule data for date:', date);

            try {
                const url = new URL(@json(route('schedule.fetch')));
                if (date) {
                    url.searchParams.append('date', date);
                }

                console.log('Fetching from URL:', url.toString());

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Received data:', data);

                if (data.success) {
                    console.log('Updating table with schedules:', data.data.schedules);
                    updateScheduleTable(data.data.schedules);
                    updateStatsCards(data.data.stats);
                    updateChart(data.data.stats);
                } else {
                    console.error('API returned error:', data.message);
                    alert('Error: ' + (data.message || 'Failed to load schedule data'));
                }
            } catch (error) {
                console.error('Error fetching schedule data:', error);
                alert('Failed to load schedule data: ' + error.message);
            }
        }

        function updateScheduleTable(schedules) {
            console.log('updateScheduleTable called with:', schedules);

            // The ID is on the wrapper div, need to find tbody inside it
            const tableWrapper = document.getElementById('scheduleTable');
            if (!tableWrapper) {
                console.error('Table wrapper #scheduleTable not found');
                alert('Error: Table element not found');
                return;
            }

            const tbody = tableWrapper.querySelector('tbody');
            if (!tbody) {
                console.error('Table tbody not found inside wrapper');
                alert('Error: Table body not found');
                return;
            }

            console.log('Found tbody element');
            console.log('Number of schedules to display:', schedules.length);

            // Clear existing content
            tbody.innerHTML = '';

            // Check if there are no tasks
            if (!schedules || schedules.length === 0) {
                console.log('No tasks found, showing empty state');
                tbody.innerHTML = `
                                                                                                                                                                                <tr>
                                                                                                                                                                                    <td colspan="7" style="text-align: center; padding: 48px 16px;">
                                                                                                                                                                                        <span class="material-symbols-rounded text-muted" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 16px;">event_busy</span>
                                                                                                                                                                                        <div class="title-md mb-2">No Tasks Found</div>
                                                                                                                                                                                        <p class="text-muted">There are no tasks scheduled for this date.</p>
                                                                                                                                                                                    </td>
                                                                                                                                                                                </tr>
                                                                                                                                                                            `;
                return;
            }

            console.log('Building table rows...');

            // Build all rows
            let rowsHtml = '';
            schedules.forEach((schedule, index) => {
                console.log(`Processing schedule ${index + 1}:`, schedule);

                const statusVariant = getStatusVariant(schedule.status);
                const priorityVariant = getPriorityVariant(schedule.priority);

                // Safely access nested properties
                const assignee = schedule.assigned_to || schedule.assignee;
                const vehicleName = schedule.vehicle?.serial_number || schedule.vehicle?.display_name || 'No vehicle';
                const description = schedule.description ? schedule.description.substring(0, 50) : '';

                // Build assignee display with avatar
                let assigneeDisplay = '';
                if (assignee) {
                    const initial = assignee.name.charAt(0).toUpperCase();
                    const avatarUrl = assignee.avatar ? `/storage/${assignee.avatar}` : null;

                    assigneeDisplay = `
                                                                                                                                                                <div class="d-flex align-items-center gap-2">
                                                                                                                                                                    <div style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; background: hsl(var(--muted)); display: flex; align-items: center; justify-content: center;">
                                                                                                                                                                        ${avatarUrl ?
                            `<img src="${avatarUrl}" alt="${assignee.name}" style="width: 100%; height: 100%; object-fit: cover;">` :
                            `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: hsl(var(--primary) / 0.1); color: hsl(var(--primary)); font-weight: 700; font-size: 14px;">${initial}</div>`
                        }
                                                                                                                                                                    </div>
                                                                                                                                                                    <div>
                                                                                                                                                                        <div class="font-medium" style="font-size: 14px;">${assignee.name}</div>
                                                                                                                                                                        <div class="text-xs text-muted">${assignee.role ? assignee.role.charAt(0).toUpperCase() + assignee.role.slice(1) : 'User'}</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            `;
                } else {
                    assigneeDisplay = '<span class="text-muted">Unassigned</span>';
                }

                // Format date and time
                let dateTimeDisplay = '';
                if (schedule.work_date) {
                    const date = new Date(schedule.work_date);
                    const dateStr = date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                    const timeStr = schedule.time || schedule.work_time || 'Time not set';
                    dateTimeDisplay = `
                                                                                                                                                                    <div class="font-medium">${dateStr}</div>
                                                                                                                                                                    <div class="text-sm text-muted">${timeStr}</div>
                                                                                                                                                                `;
                } else {
                    dateTimeDisplay = '<div class="text-muted">Not scheduled</div>';
                }

                rowsHtml += `
                                                                                                                                                                <tr>
                                                                                                                                                                    <td>${dateTimeDisplay}</td>
                                                                                                                                                                                    <td>
                                                                                                                                                                                    <div class="font-medium">${schedule.title || 'Untitled'}</div>
                                                                                                                                                                                    ${description ? `<div class="text-sm text-muted">${description}${schedule.description && schedule.description.length > 50 ? '...' : ''}</div>` : ''}
                                                                                                                                                                                </td>
                                                                                                                                                                                <td>${assigneeDisplay}</td>
                                                                                                                                                                                <td>${vehicleName}</td>
                                                                                                                                                                                    <td>
                                                                                                                                                                                        <span class="badge badge-${priorityVariant}">${schedule.priority ? schedule.priority.charAt(0).toUpperCase() + schedule.priority.slice(1) : 'Normal'}</span>
                                                                                                                                                                                    </td>
                                                                                                                                                                                    <td>
                                                                                                                                                                                        <span class="badge badge-${statusVariant}">${schedule.status ? schedule.status.charAt(0).toUpperCase() + schedule.status.slice(1) : 'Pending'}</span>
                                                                                                                                                                                    </td>
                                                                                                                                                                                    <td>
                                                                                                                                                                                        <div class="d-flex gap-1">
                                                                                                                                                                                            <button class="btn btn-outline btn-sm" onclick="viewTask(${schedule.id})">
                                                                                                                                                                                                <span class="material-symbols-rounded" style="font-size: 16px;">visibility</span>
                                                                                                                                                                                            </button>
                                                                                                                                                                                            <button class="btn btn-outline btn-sm" onclick="editTask(${schedule.id})">
                                                                                                                                                                                                <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                                                                                                                                                                                            </button>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </td>
                                                                                                                                                                                </tr>
                                                                                                                                                                            `;
            });

            tbody.innerHTML = rowsHtml;
            console.log(`✓ Successfully updated table with ${schedules.length} tasks`);
        }

        function updateStatsCards(stats) {
            const totalEl = document.querySelector('[data-stat="total"]');
            const pendingEl = document.querySelector('[data-stat="pending"]');
            const runningEl = document.querySelector('[data-stat="running"]');
            const completedEl = document.querySelector('[data-stat="completed"]');

            if (totalEl) totalEl.textContent = stats.total;
            if (pendingEl) pendingEl.textContent = stats.pending;
            if (runningEl) runningEl.textContent = stats.running;
            if (completedEl) completedEl.textContent = stats.completed;
        }

        function initializeChart() {
            const ctx = document.getElementById('scheduleChart');
            if (ctx && window.Chart) {
                scheduleChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Running', 'Completed'],
                        datasets: [{
                            label: 'Tasks',
                            data: [{{ $stats['pending'] }}, {{ $stats['running'] }}, {{ $stats['completed'] }}],
                            backgroundColor: [
                                'hsl(0 84% 60%)',        // Danger red for pending
                                'hsl(220 90% 40%)',       // Primary blue for running
                                'hsl(160 75% 40%)',       // Secondary green for completed
                            ],
                            borderWidth: 3,
                            borderColor: 'hsl(var(--surface))',
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: 8 },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        family: "'Montserrat', sans-serif",
                                        weight: '600'
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'hsl(var(--text))',
                                titleFont: {
                                    family: "'Montserrat', sans-serif",
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    family: "'Montserrat', sans-serif"
                                },
                                padding: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });

                // Keep chart sized to container
                window.addEventListener('resize', () => {
                    if (scheduleChart) scheduleChart.resize();
                });
            }
        }

        function updateChart(stats) {
            if (scheduleChart) {
                scheduleChart.data.datasets[0].data = [stats.pending, stats.running, stats.completed];
                scheduleChart.update('active');
            }
        }

        function getStatusVariant(status) {
            switch (status) {
                case 'completed': return 'secondary';
                case 'running': return 'primary';
                case 'cancelled': return 'danger';
                default: return 'primary';
            }
        }

        function getPriorityVariant(priority) {
            switch (priority) {
                case 'urgent': return 'danger';
                case 'high': return 'danger';
                case 'medium': return 'secondary';
                default: return 'primary';
            }
        }

        function showAddTaskModal() {
            Swal.fire({
                title: 'Add New Task',
                html: `
                                                                                                                                                                                                            <div style="text-align: left;">
                                                                                                                                                                                                                <div style="margin-bottom: 16px;">
                                                                                                                                                                                                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Task Title *</label>
                                                                                                                                                                                                                    <input type="text" id="taskTitle" class="swal2-input" placeholder="Enter task title" style="width: 100%; margin: 0;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="margin-bottom: 16px;">
                                                                                                                                                                                                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Description</label>
                                                                                                                                                                                                                    <textarea id="taskDescription" class="swal2-textarea" placeholder="Enter description" style="width: 100%; margin: 0;"></textarea>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="margin-bottom: 16px;">
                                                                                                                                                                                                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Due Date</label>
                                                                                                                                                                                                                    <input type="date" id="taskDueDate" class="swal2-input" style="width: 100%; margin: 0;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        `,
                showCancelButton: true,
                confirmButtonText: 'Add Task',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline'
                },
                buttonsStyling: false
            });
        }

        function viewTask(id) {
            // Redirect to task detail page or show modal
            window.location.href = `/tasks/${id}`;
        }

        function editTask(id) {
            // Redirect to task edit page or show modal
            window.location.href = `/tasks/${id}/edit`;
        }
    </script>
@endsection