@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="mb-4">
    <x-card>
        <div class="title-lg mb-1">Welcome, {{ Auth::user()->name }}!</div>
        <p class="text-muted mb-0">Here is a summary of your recent activities.</p>
    </x-card>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">
    <div class="lg:col-span-7">
        <div class="grid grid-cols-2 gap-3">
            <x-card>
                <div class="card-body flex justify-between items-start">
                    <div>
                        <div class="title-xl mb-1">{{ $stats['total_vehicles'] }}</div>
                        <div class="text-muted text-sm">Total Vehicles</div>
                        <div class="flex items-center gap-1 text-sm mt-2" style="color: hsl(var(--secondary));">
                            <span class="material-symbols-rounded" style="font-size: 16px;">trending_up</span>
                            <span>+12% from last month</span>
                        </div>
                    </div>
                    <span class="material-symbols-rounded" style="font-size: 32px; color: hsl(var(--primary) / .6);">directions_car</span>
                </div>
            </x-card>
            <x-card>
                <div class="card-body flex justify-between items-start">
                    <div>
                        <div class="title-xl mb-1">{{ $stats['total_tasks'] }}</div>
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
                        <div class="title-xl mb-1">{{ $stats['pending_tasks'] }}</div>
                        <div class="text-muted text-sm">Pending Tasks</div>
                        <div class="flex items-center gap-1 text-sm mt-2" style="color: hsl(var(--danger));">
                            <span class="material-symbols-rounded" style="font-size: 16px;">trending_down</span>
                            <span>-3 from yesterday</span>
                        </div>
                    </div>
                    <span class="material-symbols-rounded" style="font-size: 32px; color: hsl(var(--primary) / .6);">schedule</span>
                </div>
            </x-card>
            <x-card>
                <div class="card-body flex justify-between items-start">
                    <div>
                        <div class="title-xl mb-1">{{ $stats['completed_tasks'] }}</div>
                        <div class="text-muted text-sm">Completed Tasks</div>
                        <div class="flex items-center gap-1 text-sm mt-2" style="color: hsl(var(--secondary));">
                            <span class="material-symbols-rounded" style="font-size: 16px;">trending_up</span>
                            <span>+15% this week</span>
                        </div>
                    </div>
                    <span class="material-symbols-rounded" style="font-size: 32px; color: hsl(var(--primary) / .6);">check_circle</span>
                </div>
            </x-card>
        </div>
    </div>
    <div class="lg:col-span-5">
        <x-card>
            <x-slot:title>
                <div class="d-flex align-items-center justify-content-between w-100">
                    <span class="title-md">Recent Vehicles</span>
                    <x-button variant="outline" size="sm" href="{{ route('dashboard.vehicles') }}">
                        <span class="material-symbols-rounded" style="font-size: 16px;">open_in_new</span>
                        View All
                    </x-button>
                </div>
            </x-slot:title>
            <div class="card-body">
                @if($recent_vehicles->count() > 0)
                    @foreach($recent_vehicles as $vehicle)
                        <div class="fade-in"
                            style="display: flex; align-items: center; gap: 12px; height: 80px; border: 1px solid var(--border); border-radius: 8px; padding: 8px 8px; margin-bottom: 8px; background: var(--card);">
                            <div
                                style="width: 80px; height: 100%; background: var(--muted); border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 1px;">
                                @php($firstPhoto = optional($vehicle->photos->first())->photo_path)
                                <img src="{{ $firstPhoto ? asset('storage/' . $firstPhoto) : asset('assets/image/car_placeholder.png') }}"
                                    alt="Vehicle" style="width: 100%; height: 100%; object-fit: contain;" />
                            </div>
                            <div
                                style="flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div
                                        style="flex: 1; min-width: 0; font-weight: 600; color: var(--foreground); font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $vehicle->serial_number }}
                                    </div>
                                </div>
                                <div
                                    style="font-size: 12px; color: var(--muted-foreground); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $vehicle->make }} {{ $vehicle->model }} {{ $vehicle->year }}
                                </div>
                                <div style="margin-top: 4px; display: flex; align-items: center; gap: 8px;">
                                    <span
                                        style="display: inline-block; font-size: 10px; font-weight: 600; background: #f59e0b; color: #000; padding: 2px 6px; border-radius: 12px;">
                                        {{ $vehicle->chassis_model }}
                                    </span>
                                </div>
                            </div>
                            <div
                                style="display: flex; flex-direction: column; align-items: flex-end; height: 100%; justify-content: space-between;">
                                <span
                                    style="align-self: flex-end; display: inline-block; font-size: 10px; font-weight: 700; background: #10b981; color: #111827; padding: 4px 8px; border-radius: 12px;">{{ ucfirst($vehicle->status) }}</span>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span
                                        style="font-weight: 700; color: var(--foreground); font-size: 13px;">¥{{ number_format((float) $vehicle->buying_price, 0) }}</span>
                                    <span class="material-symbols-rounded"
                                        style="color: hsl(var(--primary));">chevron_right</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center" style="padding: 40px; color: hsl(var(--muted-foreground));">
                        <span class="material-symbols-rounded"
                            style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;">directions_car</span>
                        <p class="mb-0">No vehicles found.</p>
                    </div>
                @endif
            </div>
        </x-card>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
    <div class="lg:col-span-7">
        <x-card>
            <x-slot:title>
                <div class="d-flex align-items-center justify-content-between w-100">
                    <span class="title-md">Recent Tasks</span>
                    <x-button variant="outline" size="sm" :href="route('schedule.index')">
                        <span class="material-symbols-rounded" style="font-size: 16px;">open_in_new</span>
                        View All
                    </x-button>
                </div>
            </x-slot:title>
            <div class="p-0">
                @if(isset($recent_tasks) && $recent_tasks->count() > 0)
                    <x-table :headers="['Task', 'Vehicle', 'Assigned To', 'Status', 'Created']">
                        @foreach($recent_tasks as $task)
                            <tr>
                                <td style="font-weight: 600;">{{ $task->title ?? ('#' . $task->id) }}</td>
                                <td>{{ optional($task->vehicle)->serial_number }}</td>
                                <td>{{ optional($task->assignedUser)->name ?? '—' }}</td>
                                <td>
                                    <x-badge :variant="match($task->status){'completed'=>'secondary','pending'=>'secondary','running'=>'primary',default=>'primary'}">{{ ucfirst($task->status) }}</x-badge>
                                </td>
                                <td>{{ $task->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </x-table>
                @else
                    <div class="p-4 text-center">
                        <p class="mb-2 text-muted">No recent tasks available on dashboard.</p>
                        <x-button variant="primary" :href="route('schedule.index')">Go to Schedule</x-button>
                    </div>
                @endif
            </div>
        </x-card>
    </div>
    <div class="lg:col-span-5">
        <x-card :title="'Task Status'">
            <div style="height: 240px;">
                <canvas id="taskStatusChart" width="300" height="200"></canvas>
            </div>
        </x-card>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Task Status Chart (Donut)
        const taskStatusEl = document.getElementById('taskStatusChart');
        if (taskStatusEl) {
            const taskStatusCtx = taskStatusEl.getContext('2d');
            new Chart(taskStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Pending', 'Running', 'Cancelled'],
                    datasets: [{
                        data: [
                                {{ $stats['completed_tasks'] }},
                                {{ $stats['pending_tasks'] }},
                                {{ max(0, $stats['total_tasks'] - $stats['completed_tasks'] - $stats['pending_tasks']) }},
                            0
                        ],
                        backgroundColor: [
                            '#10b981',
                            '#f59e0b',
                            '#3b82f6',
                            '#ef4444'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 20, usePointStyle: true }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Add click animations to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', function () {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        });

        // Add hover effects to table rows
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