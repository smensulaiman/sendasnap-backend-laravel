@extends('layouts.app')

@section('title', 'Vehicles')

@section('content')
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="title-lg mb-1">Vehicle Management</div>
            <p class="text-muted">Manage your vehicle inventory and track status</p>
        </div>
        <div class="flex gap-3">
            <x-button variant="outline" onclick="toggleViewMode()">
                <span class="material-symbols-rounded" id="viewModeIcon">grid_view</span>
                <span id="viewModeText">Grid View</span>
            </x-button>
            <x-button variant="outline" onclick="showFilters()">
                <span class="material-symbols-rounded">tune</span>
                Filters
            </x-button>
            <x-button variant="primary" onclick="showAddVehicleModal()">
                <span class="material-symbols-rounded">add</span>
                Add Vehicle
            </x-button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-muted">Total Vehicles</div>
                    <div class="title-lg">{{ $vehicles->total() }}</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                    <span class="material-symbols-rounded" style="color: hsl(var(--primary)); font-size: 24px;">directions_car</span>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-muted">Available</div>
                    <div class="title-lg">{{ $vehicles->where('status', 'ready')->count() }}</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center" style="background: hsl(var(--secondary) / .1);">
                    <span class="material-symbols-rounded" style="color: hsl(var(--secondary)); font-size: 24px;">check_circle</span>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-muted">In Yard</div>
                    <div class="title-lg">{{ $vehicles->where('status', 'in_yard')->count() }}</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center" style="background: hsl(45 100% 90%);">
                    <span class="material-symbols-rounded" style="color: hsl(45 100% 40%); font-size: 24px;">warehouse</span>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-muted">Sold</div>
                    <div class="title-lg">{{ $vehicles->where('status', 'sold')->count() }}</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center" style="background: hsl(280 100% 90%);">
                    <span class="material-symbols-rounded" style="color: hsl(280 100% 40%); font-size: 24px;">sell</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Vehicles Content -->
    @if($vehicles->count() > 0)
        <!-- Grid View -->
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($vehicles as $vehicle)
                <x-card class="vehicle-card hover-lift">
                    <div class="relative">
                        <!-- Vehicle Image/Placeholder -->
                        <div class="h-48 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg mb-4 flex items-center justify-center relative overflow-hidden"
                             style="background: linear-gradient(135deg, hsl(var(--primary) / .1), hsl(var(--primary) / .05));">
                            @php($firstPhoto = optional($vehicle->photos->first())->photo_path)
                            @if($firstPhoto)
                                <img src="{{ asset('storage/' . $firstPhoto) }}" alt="Vehicle" class="w-full h-full object-cover rounded-lg">
                            @else
                                <span class="material-symbols-rounded" style="font-size: 64px; color: hsl(var(--primary) / .3);">directions_car</span>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                <x-badge :variant="match($vehicle->status){'ready'=>'secondary','sold'=>'primary','in_yard'=>'secondary',default=>'primary'}">
                                    {{ ucfirst($vehicle->status) }}
                                </x-badge>
                            </div>
                        </div>

                        <!-- Vehicle Info -->
                        <div class="space-y-3">
                            <div>
                                <div class="title-md">{{ $vehicle->serial_number }}</div>
                                <div class="text-sm text-muted">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }})</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-muted">Engine</div>
                                    <div class="font-medium">{{ $vehicle->cc }}cc</div>
                                </div>
                                <div>
                                    <div class="text-muted">Color</div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full border" 
                                             style="background: {{ strtolower($vehicle->color) === 'white' ? '#ffffff' : (strtolower($vehicle->color) === 'black' ? '#000000' : (strtolower($vehicle->color) === 'red' ? '#ef4444' : (strtolower($vehicle->color) === 'blue' ? '#3b82f6' : '#6b7280'))) }}; border-color: hsl(var(--border));"></div>
                                        <span class="font-medium">{{ $vehicle->color }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t" style="border-color: hsl(var(--border));">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                                        <span class="text-xs font-medium" style="color: hsl(var(--primary));">{{ strtoupper(substr($vehicle->creator->name, 0, 1)) }}</span>
                                    </div>
                                    <span class="text-xs text-muted">{{ $vehicle->creator->name }}</span>
                                </div>
                                <div class="flex gap-1">
                                    <x-button variant="outline" size="icon" onclick="viewVehicle({{ $vehicle->id }})" title="View">
                                        <span class="material-symbols-rounded">visibility</span>
                                    </x-button>
                                    <x-button variant="outline" size="icon" onclick="editVehicle({{ $vehicle->id }})" title="Edit">
                                        <span class="material-symbols-rounded">edit</span>
                                    </x-button>
                                    <x-button variant="danger" size="icon" onclick="deleteVehicle({{ $vehicle->id }})" title="Delete">
                                        <span class="material-symbols-rounded">delete</span>
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <!-- Table View (Hidden by default) -->
        <div id="tableView" class="hidden">
            <x-card>
                <x-table :headers="['Vehicle', 'Year', 'Color', 'Status', 'Created By', 'Created At', 'Actions']">
                    @foreach($vehicles as $vehicle)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white">
                                        <span class="material-symbols-rounded">directions_car</span>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $vehicle->serial_number }}</div>
                                        <div class="text-sm text-muted">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                                        <div class="text-xs text-muted">{{ $vehicle->chassis_model }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-medium">{{ $vehicle->year }}</div>
                                <div class="text-sm text-muted">{{ $vehicle->cc }}cc</div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-full border" 
                                         style="background: {{ strtolower($vehicle->color) === 'white' ? '#ffffff' : (strtolower($vehicle->color) === 'black' ? '#000000' : (strtolower($vehicle->color) === 'red' ? '#ef4444' : (strtolower($vehicle->color) === 'blue' ? '#3b82f6' : '#6b7280'))) }}; border-color: hsl(var(--border));"></div>
                                    {{ $vehicle->color }}
                                </div>
                            </td>
                            <td>
                                <x-badge :variant="match($vehicle->status){'ready'=>'secondary','sold'=>'primary','in_yard'=>'secondary',default=>'primary'}">
                                    {{ ucfirst($vehicle->status) }}
                                </x-badge>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                                        <span class="text-xs font-medium" style="color: hsl(var(--primary));">{{ strtoupper(substr($vehicle->creator->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-sm">{{ $vehicle->creator->name }}</div>
                                        <div class="text-xs text-muted">{{ ucfirst($vehicle->creator->role) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-sm text-muted">{{ $vehicle->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <x-button variant="outline" size="icon" onclick="viewVehicle({{ $vehicle->id }})">
                                        <span class="material-symbols-rounded">visibility</span>
                                    </x-button>
                                    <x-button variant="outline" size="icon" onclick="editVehicle({{ $vehicle->id }})">
                                        <span class="material-symbols-rounded">edit</span>
                                    </x-button>
                                    <x-button variant="danger" size="icon" onclick="deleteVehicle({{ $vehicle->id }})">
                                        <span class="material-symbols-rounded">delete</span>
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-table>
            </x-card>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6">
            {{ $vehicles->links() }}
        </div>
    @else
        <x-empty-state 
            title="No vehicles found" 
            message="Get started by adding your first vehicle to the system.">
            <x-button variant="primary" class="mt-4" onclick="showAddVehicleModal()">
                <span class="material-symbols-rounded">add</span>
                Add Vehicle
            </x-button>
        </x-empty-state>
    @endif

    <script>
        let isGridView = true;

        function toggleViewMode() {
            const gridView = document.getElementById('gridView');
            const tableView = document.getElementById('tableView');
            const icon = document.getElementById('viewModeIcon');
            const text = document.getElementById('viewModeText');

            if (isGridView) {
                gridView.classList.add('hidden');
                tableView.classList.remove('hidden');
                icon.textContent = 'view_list';
                text.textContent = 'Table View';
                isGridView = false;
            } else {
                gridView.classList.remove('hidden');
                tableView.classList.add('hidden');
                icon.textContent = 'grid_view';
                text.textContent = 'Grid View';
                isGridView = true;
            }
        }

        function showFilters() {
            Swal.fire({
                title: 'Filter Vehicles',
                html: `
                <div style="text-align: left;">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Status</label>
                        <select id="statusFilter" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="in_yard">In Yard</option>
                            <option value="ready">Ready</option>
                            <option value="sold">Sold</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Make</label>
                        <input type="text" id="makeFilter" placeholder="Enter make" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Year Range</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="number" id="yearFrom" placeholder="From" style="flex: 1; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <input type="number" id="yearTo" placeholder="To" style="flex: 1; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Color</label>
                        <select id="colorFilter" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            <option value="">All Colors</option>
                            <option value="white">White</option>
                            <option value="black">Black</option>
                            <option value="red">Red</option>
                            <option value="blue">Blue</option>
                            <option value="silver">Silver</option>
                            <option value="gray">Gray</option>
                        </select>
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Apply Filters',
                cancelButtonText: 'Clear Filters',
                confirmButtonColor: 'hsl(var(--primary))',
                width: '450px'
            }).then((result) => {
                if (result.isConfirmed) {
                    applyFilters();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    clearFilters();
                }
            });
        }

        function applyFilters() {
            // Get filter values
            const status = document.getElementById('statusFilter')?.value || '';
            const make = document.getElementById('makeFilter')?.value || '';
            const yearFrom = document.getElementById('yearFrom')?.value || '';
            const yearTo = document.getElementById('yearTo')?.value || '';
            const color = document.getElementById('colorFilter')?.value || '';

            // Build query parameters
            const params = new URLSearchParams();
            if (status) params.append('status', status);
            if (make) params.append('make', make);
            if (yearFrom) params.append('year_from', yearFrom);
            if (yearTo) params.append('year_to', yearTo);
            if (color) params.append('color', color);

            // Redirect with filters
            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }

        function clearFilters() {
            window.location.href = window.location.pathname;
        }

        function showAddVehicleModal() {
            Swal.fire({
                title: 'Add New Vehicle',
                html: `
                <div style="text-align: left;">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Serial Number *</label>
                        <input type="text" id="serialNumber" placeholder="Enter serial number" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Make *</label>
                            <input type="text" id="make" placeholder="Toyota" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Model *</label>
                            <input type="text" id="model" placeholder="Camry" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Year *</label>
                            <input type="number" id="year" placeholder="2020" min="1900" max="2030" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Color *</label>
                            <select id="color" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                                <option value="">Select Color</option>
                                <option value="white">White</option>
                                <option value="black">Black</option>
                                <option value="red">Red</option>
                                <option value="blue">Blue</option>
                                <option value="silver">Silver</option>
                                <option value="gray">Gray</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Engine CC</label>
                            <input type="number" id="cc" placeholder="1500" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Chassis Model</label>
                            <input type="text" id="chassisModel" placeholder="Enter chassis model" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Add Vehicle',
                cancelButtonText: 'Cancel',
                confirmButtonColor: 'hsl(var(--primary))',
                width: '550px',
                preConfirm: () => {
                    const serialNumber = document.getElementById('serialNumber').value;
                    const make = document.getElementById('make').value;
                    const model = document.getElementById('model').value;
                    const year = document.getElementById('year').value;
                    const color = document.getElementById('color').value;

                    if (!serialNumber || !make || !model || !year || !color) {
                        Swal.showValidationMessage('Please fill in all required fields');
                        return false;
                    }

                    return {
                        serial_number: serialNumber,
                        make: make,
                        model: model,
                        year: year,
                        color: color,
                        cc: document.getElementById('cc').value || null,
                        chassis_model: document.getElementById('chassisModel').value || null
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would normally send the data to your backend
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
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would normally send delete request to your backend
                    showToast('Vehicle deleted successfully', 'success');
                }
            });
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            // Add hover effects to vehicle cards
            document.querySelectorAll('.vehicle-card').forEach(card => {
                card.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-4px)';
                    this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
                    this.style.transition = 'all 0.3s ease';
                });

                card.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '';
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
