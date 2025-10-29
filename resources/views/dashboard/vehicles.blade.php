@extends('layouts.dashboard')

@section('title', 'Vehicles')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Vehicle Management</h3>
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-outline" onclick="showFilters()">
                    <span class="material-symbols-rounded">filter_list</span>
                    Filters
                </button>
                <button class="btn btn-primary" onclick="showAddVehicleModal()">
                    <span class="material-symbols-rounded">add</span>
                    Add Vehicle
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($vehicles->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th>Year</th>
                                <th>Color</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                                <tr class="fade-in">
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div
                                                style="width: 48px; height: 48px; background: linear-gradient(135deg, hsl(var(--primary)), hsl(var(--primary) / 0.8)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                                                <span class="material-symbols-rounded">directions_car</span>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: hsl(var(--foreground)); font-size: 14px;">
                                                    {{ $vehicle->serial_number }}</div>
                                                <div style="font-size: 12px; color: hsl(var(--muted-foreground));">
                                                    {{ $vehicle->make }} {{ $vehicle->model }}</div>
                                                <div style="font-size: 11px; color: hsl(var(--muted-foreground));">
                                                    {{ $vehicle->chassis_model }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 500; color: hsl(var(--foreground));">{{ $vehicle->year }}</div>
                                        <div style="font-size: 11px; color: hsl(var(--muted-foreground));">{{ $vehicle->cc }}cc
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div
                                                style="width: 16px; height: 16px; background: {{ strtolower($vehicle->color) === 'white' ? '#ffffff' : (strtolower($vehicle->color) === 'black' ? '#000000' : (strtolower($vehicle->color) === 'red' ? '#ef4444' : (strtolower($vehicle->color) === 'blue' ? '#3b82f6' : '#6b7280'))) }}; border-radius: 50%; border: 1px solid hsl(var(--border));">
                                            </div>
                                            {{ $vehicle->color }}
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
                                                style="width: 32px; height: 32px; background: hsl(var(--primary)); color: hsl(var(--primary-foreground)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                                {{ strtoupper(substr($vehicle->creator->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 500; color: hsl(var(--foreground)); font-size: 14px;">
                                                    {{ $vehicle->creator->name }}</div>
                                                <div style="font-size: 11px; color: hsl(var(--muted-foreground));">
                                                    {{ ucfirst($vehicle->creator->role) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-size: 12px; color: hsl(var(--muted-foreground));">
                                        {{ $vehicle->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 4px;">
                                            <button class="btn btn-outline" style="padding: 6px 8px; font-size: 12px;"
                                                onclick="viewVehicle({{ $vehicle->id }})">
                                                <span class="material-symbols-rounded">visibility</span>
                                            </button>
                                            <button class="btn btn-outline" style="padding: 6px 8px; font-size: 12px;"
                                                onclick="editVehicle({{ $vehicle->id }})">
                                                <span class="material-symbols-rounded">edit</span>
                                            </button>
                                            <button class="btn btn-outline"
                                                style="padding: 6px 8px; font-size: 12px; color: #ef4444;"
                                                onclick="deleteVehicle({{ $vehicle->id }})">
                                                <span class="material-symbols-rounded">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    {{ $vehicles->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px; color: hsl(var(--muted-foreground));">
                    <div
                        style="width: 80px; height: 80px; background: hsl(var(--muted)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 32px;">
                        <span class="material-symbols-rounded">directions_car</span>
                    </div>
                    <h3 style="margin-bottom: 8px; color: hsl(var(--foreground));">No vehicles found</h3>
                    <p style="margin-bottom: 24px;">Get started by adding your first vehicle to the system.</p>
                    <button class="btn btn-primary" onclick="showAddVehicleModal()">
                        <span class="material-symbols-rounded">add</span>
                        Add Vehicle
                    </button>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showFilters() {
            Swal.fire({
                title: 'Filter Vehicles',
                html: `
                <div style="text-align: left;">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Status</label>
                        <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="in_yard">In Yard</option>
                            <option value="ready">Ready</option>
                            <option value="sold">Sold</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Make</label>
                        <input type="text" placeholder="Enter make" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Year Range</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="number" placeholder="From" style="flex: 1; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <input type="number" placeholder="To" style="flex: 1; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
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

        function showAddVehicleModal() {
            Swal.fire({
                title: 'Add New Vehicle',
                html: `
                <div style="text-align: left;">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Serial Number *</label>
                        <input type="text" placeholder="Enter serial number" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Make *</label>
                            <input type="text" placeholder="Toyota" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Model *</label>
                            <input type="text" placeholder="Camry" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Year *</label>
                            <input type="number" placeholder="2020" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Color *</label>
                            <input type="text" placeholder="White" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Add Vehicle',
                cancelButtonText: 'Cancel',
                confirmButtonColor: 'hsl(var(--primary))',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast('Vehicle added successfully', 'success');
                }
            });
        }

        function viewVehicle(id) {
            showToast(`Viewing vehicle ${id}`, 'info');
        }

        function editVehicle(id) {
            showToast(`Editing vehicle ${id}`, 'info');
        }

        function deleteVehicle(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast('Vehicle deleted successfully', 'success');
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