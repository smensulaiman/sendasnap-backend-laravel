@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(10, 109, 58, 0.1);
        color: #0a6d3a;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
    }

    .stat-label {
        color: #6c757d;
        font-weight: 500;
        font-size: 12px;
    }
</style>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-value">{{ $stats['total_vehicles'] }}</div>
                <div class="stat-label">Total Vehicles</div>
                <div class="stat-change positive">
                    <span class="material-symbols-rounded">trending_up</span>
                    +12% from last month
                </div>
            </div>
            <div class="stat-icon">
                <span class="material-symbols-rounded">directions_car</span>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-value">{{ $stats['total_tasks'] }}</div>
                <div class="stat-label">Total Tasks</div>
                <div class="stat-change positive">
                    <span class="material-symbols-rounded">trending_up</span>
                    +8% from last week
                </div>
            </div>
            <div class="stat-icon">
                <span class="material-symbols-rounded">task_alt</span>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-value">{{ $stats['pending_tasks'] }}</div>
                <div class="stat-label">Pending Tasks</div>
                <div class="stat-change negative">
                    <span class="material-symbols-rounded">trending_down</span>
                    -3 from yesterday
                </div>
            </div>
            <div class="stat-icon">
                <span class="material-symbols-rounded">schedule</span>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-value">{{ $stats['completed_tasks'] }}</div>
                <div class="stat-label">Completed Tasks</div>
                <div class="stat-change positive">
                    <span class="material-symbols-rounded">trending_up</span>
                    +15% this week
                </div>
            </div>
            <div class="stat-icon">
                <span class="material-symbols-rounded">check_circle</span>
            </div>
        </div>
    </div>


</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4>Recent Vehicles</h4>
            <a href="{{ route('dashboard.vehicles') }}" class="btn btn-outline">
                <span class="material-symbols-rounded">open_in_new</span>
                View All
            </a>
        </div>
        <div class="card-body">
            @if($recent_vehicles->count() > 0)
            <div>
                @foreach($recent_vehicles as $vehicle)
                <div class="fade-in"
                    style="display: flex; align-items: center; gap: 12px; height: 80px; border: 1px solid var(--border); border-radius: 8px; padding: 8px 8px; margin-bottom: 8px; background: var(--card);">
                    <div
                        style="width: 80px; height: 100%; background: var(--muted); border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 1px;">
                        @php($firstPhoto = optional($vehicle->photos->first())->photo_path)
                                        <img src="{{ $firstPhoto ? asset('storage/' . $firstPhoto) : asset('assets/image/car_placeholder.png') }}"
                                            alt="Vehicle" style="width: 100%; height: 100%; object-fit: contain;" />
                                    </div>

                                    <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center;">
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
                            </div>
                        @else
            <div style="text-align: center; padding: 40px; color: hsl(var(--muted-foreground));">
                <span class="material-symbols-rounded"
                    style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;">directions_car</span>
                <p>No vehicles found.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Task Status</h3>
        </div>
        <div class="card-body" style="height: 240px;">
            <canvas id="taskStatusChart" width="300" height="200"></canvas>
        </div>
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