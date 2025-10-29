@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">{{ $stats['total_vehicles'] }}</div>
                    <div class="stat-label">Total Vehicles</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        +12% from last month
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">{{ $stats['total_tasks'] }}</div>
                    <div class="stat-label">Total Tasks</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        +8% from last week
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">{{ $stats['pending_tasks'] }}</div>
                    <div class="stat-label">Pending Tasks</div>
                    <div class="stat-change negative">
                        <i class="fas fa-arrow-down"></i>
                        -3 from yesterday
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">{{ $stats['completed_tasks'] }}</div>
                    <div class="stat-label">Completed Tasks</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        +15% this week
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">{{ $stats['vehicles_in_yard'] }}</div>
                    <div class="stat-label">In Yard</div>
                    <div class="stat-change">
                        <i class="fas fa-minus"></i>
                        No change
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">{{ $stats['vehicles_ready'] }}</div>
                    <div class="stat-label">Ready for Sale</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        +5 this week
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 32px;">
        <div class="card">
            <div class="card-header">
                <h3>Recent Vehicles</h3>
                <a href="{{ route('dashboard.vehicles') }}" class="btn btn-outline">
                    <i class="fas fa-external-link-alt"></i>
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($recent_vehicles->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_vehicles as $vehicle)
                                    <tr class="fade-in">
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div
                                                    style="width: 40px; height: 40px; background: hsl(var(--muted)); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-car" style="color: hsl(var(--muted-foreground));"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 500; color: hsl(var(--foreground));">
                                                        {{ $vehicle->serial_number }}</div>
                                                    <div style="font-size: 12px; color: hsl(var(--muted-foreground));">
                                                        {{ $vehicle->make }} {{ $vehicle->model }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $vehicle->status }}">
                                                {{ ucfirst($vehicle->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <div
                                                    style="width: 24px; height: 24px; background: hsl(var(--primary)); color: hsl(var(--primary-foreground)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600;">
                                                    {{ strtoupper(substr($vehicle->creator->name, 0, 1)) }}
                                                </div>
                                                {{ $vehicle->creator->name }}
                                            </div>
                                        </td>
                                        <td style="font-size: 12px; color: hsl(var(--muted-foreground));">
                                            {{ $vehicle->created_at->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <button class="btn btn-outline" style="padding: 4px 8px; font-size: 12px;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: hsl(var(--muted-foreground));">
                        <i class="fas fa-car" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                        <p>No vehicles found.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Task Status</h3>
            </div>
            <div class="card-body">
                <canvas id="taskStatusChart" width="300" height="200"></canvas>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
        <div class="card">
            <div class="card-header">
                <h3>Recent Tasks</h3>
                <a href="{{ route('dashboard.tasks') }}" class="btn btn-outline">
                    <i class="fas fa-external-link-alt"></i>
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($recent_tasks->count() > 0)
                    <div style="space-y: 12px;">
                        @foreach($recent_tasks as $task)
                            <div class="fade-in"
                                style="padding: 16px; border: 1px solid hsl(var(--border)); border-radius: 8px; margin-bottom: 12px;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <div style="font-weight: 500; color: hsl(var(--foreground));">{{ $task->title }}</div>
                                    <span class="badge badge-{{ $task->priority }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                                <div style="font-size: 12px; color: hsl(var(--muted-foreground)); margin-bottom: 8px;">
                                    {{ Str::limit($task->description, 60) }}
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div
                                            style="width: 20px; height: 20px; background: hsl(var(--primary)); color: hsl(var(--primary-foreground)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 600;">
                                            {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                        </div>
                                        <span
                                            style="font-size: 12px; color: hsl(var(--muted-foreground));">{{ $task->assignedUser->name }}</span>
                                    </div>
                                    <span class="badge badge-{{ $task->status }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: hsl(var(--muted-foreground));">
                        <i class="fas fa-tasks" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                        <p>No tasks found.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Vehicle Status Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="vehicleStatusChart" width="300" height="200"></canvas>
            </div>
        </div>
    </div>

    @if(isset($my_tasks) && $my_tasks->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h3>My Tasks</h3>
                <span class="badge badge-primary">{{ $my_tasks->count() }} tasks</span>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px;">
                    @foreach($my_tasks as $task)
                        <div class="fade-in"
                            style="padding: 20px; border: 1px solid hsl(var(--border)); border-radius: 12px; background: hsl(var(--card));">
                            <div
                                style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                <div style="font-weight: 600; color: hsl(var(--foreground)); font-size: 16px;">{{ $task->title }}
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <span class="badge badge-{{ $task->priority }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    <span class="badge badge-{{ $task->status }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </div>
                            </div>
                            <div style="font-size: 14px; color: hsl(var(--muted-foreground)); margin-bottom: 16px;">
                                {{ Str::limit($task->description, 100) }}
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <div style="font-size: 12px; color: hsl(var(--muted-foreground));">
                                    <i class="fas fa-car"></i> {{ $task->vehicle->make }} {{ $task->vehicle->model }}
                                </div>
                                <div style="font-size: 12px; color: hsl(var(--muted-foreground));">
                                    <i class="fas fa-calendar"></i>
                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-primary" style="flex: 1; padding: 8px 12px; font-size: 12px;">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn btn-outline" style="padding: 8px 12px; font-size: 12px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Task Status Chart
            const taskStatusCtx = document.getElementById('taskStatusChart').getContext('2d');
            new Chart(taskStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Pending', 'Running', 'Cancelled'],
                    datasets: [{
                        data: [{{ $stats['completed_tasks'] }}, {{ $stats['pending_tasks'] }}, {{ $stats['total_tasks'] - $stats['completed_tasks'] - $stats['pending_tasks'] }}, 0],
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
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            // Vehicle Status Chart
            const vehicleStatusCtx = document.getElementById('vehicleStatusChart').getContext('2d');
            new Chart(vehicleStatusCtx, {
                type: 'bar',
                data: {
                    labels: ['Pending', 'In Yard', 'Ready', 'Sold'],
                    datasets: [{
                        data: [{{ $stats['total_vehicles'] - $stats['vehicles_in_yard'] - $stats['vehicles_ready'] }}, {{ $stats['vehicles_in_yard'] }}, {{ $stats['vehicles_ready'] }}, 0],
                        backgroundColor: [
                            '#f59e0b',
                            '#3b82f6',
                            '#10b981',
                            '#8b5cf6'
                        ],
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

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