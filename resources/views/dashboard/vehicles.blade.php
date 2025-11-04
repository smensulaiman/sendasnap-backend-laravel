@extends('layouts.app')

@section('title', 'Vehicles')

@section('content')
    <!-- Header Section (Schedule-style Card) -->
    <div class="card" style="display: flex; flex-direction: column; margin-bottom: 16px;">
        <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="title-lg mb-1">Vehicle Management</div>
                    <p class="text-muted mb-0">View and manage your vehicle inventory</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleVehicleSearch()" id="toggleVehicleSearchBtn">
                        <span class="material-symbols-rounded" id="toggleVehicleSearchIcon">expand_more</span>
                        Filters
                    </button>
                    <a href="{{ route('dashboard.vehicles') }}" class="btn btn-outline btn-sm">
                        <span class="material-symbols-rounded">refresh</span>
                        Reset
                    </a>
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleViewMode()">
                        <span class="material-symbols-rounded" id="viewModeIcon">grid_view</span>
                        <span id="viewModeText">Grid View</span>
                    </button>
                </div>
            </div>

            <!-- Vehicle Filters (collapsible) -->
            <div id="vehicleSearch" class="d-flex align-items-center gap-3 p-3" style="display: none; background: hsl(var(--muted)); border-radius: 8px; position: relative;">
                <div class="d-flex align-items-center justify-content-between" style="flex: 1; gap: 8px;">
                    <div style="flex: 1; min-width: 200px;">
                        <label class="text-sm font-medium mb-1" style="display: block;">Status</label>
                        <select id="statusFilter" class="form-select" style="height: 40px; border: 1px solid hsl(var(--border)); border-radius: 8px; background: hsl(var(--card));">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="in_yard">In Yard</option>
                            <option value="ready">Ready</option>
                            <option value="sold">Sold</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="text-sm font-medium mb-1" style="display: block;">Make</label>
                        <input type="text" id="makeFilter" class="form-control" placeholder="Enter make" style="height: 40px; border: 1px solid hsl(var(--border)); border-radius: 8px; background: hsl(var(--card));">
                    </div>
                    <div class="d-flex align-items-end" style="gap: 8px;">
                        <div style="min-width: 140px;">
                            <label class="text-sm font-medium mb-1" style="display: block;">Year From</label>
                            <input type="number" id="yearFrom" class="form-control" placeholder="From" style="height: 40px; border: 1px solid hsl(var(--border)); border-radius: 8px; background: hsl(var(--card));">
                        </div>
                        <div style="min-width: 140px;">
                            <label class="text-sm font-medium mb-1" style="display: block;">Year To</label>
                            <input type="number" id="yearTo" class="form-control" placeholder="To" style="height: 40px; border: 1px solid hsl(var(--border)); border-radius: 8px; background: hsl(var(--card));">
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 160px;">
                        <label class="text-sm font-medium mb-1" style="display: block;">Color</label>
                        <select id="colorFilter" class="form-select" style="height: 40px; border: 1px solid hsl(var(--border)); border-radius: 8px; background: hsl(var(--card));">
                            <option value="">All Colors</option>
                            <option value="white">White</option>
                            <option value="black">Black</option>
                            <option value="red">Red</option>
                            <option value="blue">Blue</option>
                            <option value="silver">Silver</option>
                            <option value="gray">Gray</option>
                        </select>
                    </div>
                    <div style="padding-top: 24px; white-space: nowrap;">
                        <button type="button" class="btn btn-primary btn-sm" onclick="applyFilters()">
                            <span class="material-symbols-rounded">search</span>
                            Apply
                        </button>
                    </div>
                    <div style="padding-top: 24px; white-space: nowrap;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="clearFilters()">
                            <span class="material-symbols-rounded">clear</span>
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <x-card class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-muted mb-2">Today's Count</div>
                        <div class="title-lg text-3xl">{{ $stats['today_count'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">Vehicles added today</div>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                        <span class="material-symbols-rounded" style="color: hsl(var(--primary)); font-size: 32px;">today</span>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-6">
            <x-card class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-muted mb-2">Total Vehicles</div>
                        <div class="title-lg text-3xl">{{ $stats['total_vehicles'] ?? 0 }}</div>
                        <div class="text-xs text-muted mt-1">All vehicles in inventory</div>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center" style="background: hsl(var(--secondary) / .1);">
                        <span class="material-symbols-rounded" style="color: hsl(var(--secondary)); font-size: 32px;">directions_car</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Vehicles Content -->
    <div class="mt-4">
    @if($vehicles->count() > 0)
        <!-- Grid View -->
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @foreach($vehicles as $vehicle)
                <x-card class="vehicle-card hover-lift overflow-hidden p-0">
                    <div class="relative">
                        <!-- Vehicle Image Carousel -->
                        <div class="h-56 bg-gradient-to-br from-blue-50 to-blue-100 relative overflow-hidden group"
                             style="background: linear-gradient(135deg, hsl(var(--primary) / .1), hsl(var(--primary) / .05));">
                            @if($vehicle->photos->count() > 0)
                                <div class="vehicle-carousel relative h-full" data-vehicle-id="{{ $vehicle->id }}">
                                    <div class="carousel-track flex h-full transition-transform duration-500 ease-in-out" style="transform: translateX(0);">
                                        @foreach($vehicle->photos as $photo)
                                            <div class="carousel-slide flex-shrink-0 w-full h-full">
                                                <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                     alt="Vehicle Photo" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($vehicle->photos->count() > 1)
                                        <!-- Navigation Arrows -->
                                        <button class="carousel-prev absolute left-3 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/80 text-white rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                                                onclick="previousImage({{ $vehicle->id }})">
                                            <span class="material-symbols-rounded text-lg">chevron_left</span>
                                        </button>
                                        <button class="carousel-next absolute right-3 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/80 text-white rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                                                onclick="nextImage({{ $vehicle->id }})">
                                            <span class="material-symbols-rounded text-lg">chevron_right</span>
                                        </button>
                                        
                                        <!-- Image Indicators -->
                                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                                            @foreach($vehicle->photos as $index => $photo)
                                                <button class="carousel-indicator w-2 h-2 rounded-full transition-all {{ $index === 0 ? 'bg-white w-6' : 'bg-white/50' }}"
                                                        onclick="goToImage({{ $vehicle->id }}, {{ $index }})"></button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <span class="material-symbols-rounded" style="font-size: 72px; color: hsl(var(--primary) / .3);">directions_car</span>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                <x-badge :variant="match($vehicle->status){'ready'=>'secondary','sold'=>'primary','in_yard'=>'secondary',default=>'primary'}">
                                    {{ ucfirst($vehicle->status) }}
                                </x-badge>
                            </div>
                        </div>

                        <!-- Vehicle Info -->
                        <div class="p-5 space-y-4">
                            <div>
                                <div class="title-md mb-1">{{ $vehicle->serial_number }}</div>
                                <div class="text-sm text-muted">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }})</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="space-y-1">
                                    <div class="text-muted text-xs">Engine</div>
                                    <div class="font-semibold">{{ $vehicle->cc }}cc</div>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-muted text-xs">Color</div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border-2" 
                                             style="background: {{ strtolower($vehicle->color) === 'white' ? '#ffffff' : (strtolower($vehicle->color) === 'black' ? '#000000' : (strtolower($vehicle->color) === 'red' ? '#ef4444' : (strtolower($vehicle->color) === 'blue' ? '#3b82f6' : '#6b7280'))) }}; border-color: hsl(var(--border));"></div>
                                        <span class="font-semibold text-xs">{{ $vehicle->color }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t" style="border-color: hsl(var(--border));">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                                        <span class="text-xs font-semibold" style="color: hsl(var(--primary));">{{ strtoupper(substr($vehicle->creator->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium">{{ $vehicle->creator->name }}</div>
                                        <div class="text-xs text-muted">{{ ucfirst($vehicle->creator->role) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                <x-button variant="primary" class="w-full" onclick="viewVehicle({{ $vehicle->id }})">
                                    <span class="material-symbols-rounded text-sm mr-1">visibility</span>
                                    View Details
                                </x-button>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <!-- Table View (Hidden by default) -->
        <div id="tableView" class="hidden">
            <x-card class="p-0">
                <div class="p-6 border-b" style="border-color: hsl(var(--border));">
                    <h3 class="title-md">Vehicle List</h3>
                </div>
                <div class="overflow-x-auto">
                    <x-table :headers="['Vehicle', 'Year', 'Color', 'Status', 'Created By', 'Created At', 'Actions']">
                        @foreach($vehicles as $vehicle)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4">
                                    <div class="flex items-center gap-4">
                                        @if($vehicle->photos->count() > 0)
                                            <img src="{{ asset('storage/' . $vehicle->photos->first()->photo_path) }}" 
                                                 alt="Vehicle" 
                                                 class="w-16 h-16 object-cover rounded-lg border" style="border-color: hsl(var(--border));">
                                        @else
                                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white">
                                                <span class="material-symbols-rounded">directions_car</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold mb-1">{{ $vehicle->serial_number }}</div>
                                            <div class="text-sm text-muted">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                                            <div class="text-xs text-muted mt-0.5">{{ $vehicle->chassis_model }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <div class="font-semibold">{{ $vehicle->year }}</div>
                                    <div class="text-sm text-muted">{{ $vehicle->cc }}cc</div>
                                </td>
                                <td class="py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full border-2" 
                                             style="background: {{ strtolower($vehicle->color) === 'white' ? '#ffffff' : (strtolower($vehicle->color) === 'black' ? '#000000' : (strtolower($vehicle->color) === 'red' ? '#ef4444' : (strtolower($vehicle->color) === 'blue' ? '#3b82f6' : '#6b7280'))) }}; border-color: hsl(var(--border));"></div>
                                        <span class="font-medium">{{ $vehicle->color }}</span>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <x-badge :variant="match($vehicle->status){'ready'=>'secondary','sold'=>'primary','in_yard'=>'secondary',default=>'primary'}">
                                        {{ ucfirst($vehicle->status) }}
                                    </x-badge>
                                </td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                                            <span class="text-sm font-semibold" style="color: hsl(var(--primary));">{{ strtoupper(substr($vehicle->creator->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-sm">{{ $vehicle->creator->name }}</div>
                                            <div class="text-xs text-muted">{{ ucfirst($vehicle->creator->role) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-sm text-muted">{{ $vehicle->created_at->format('M d, Y') }}</td>
                                <td class="py-4">
                                    <x-button variant="outline" size="icon" onclick="viewVehicle({{ $vehicle->id }})">
                                        <span class="material-symbols-rounded">visibility</span>
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                </div>
            </x-card>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8">
            {{ $vehicles->links() }}
        </div>
    @else
        <x-empty-state 
            title="No vehicles found" 
            message="No vehicles are available in the system.">
        </x-empty-state>
    @endif
    </div>

    <script>
        let isGridView = true;
        const carouselStates = {};

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
            const status = document.getElementById('statusFilter')?.value || '';
            const make = document.getElementById('makeFilter')?.value || '';
            const yearFrom = document.getElementById('yearFrom')?.value || '';
            const yearTo = document.getElementById('yearTo')?.value || '';
            const color = document.getElementById('colorFilter')?.value || '';

            const params = new URLSearchParams();
            if (status) params.append('status', status);
            if (make) params.append('make', make);
            if (yearFrom) params.append('year_from', yearFrom);
            if (yearTo) params.append('year_to', yearTo);
            if (color) params.append('color', color);

            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }

        function clearFilters() {
            window.location.href = window.location.pathname;
        }

        function toggleVehicleSearch() {
            const panel = document.getElementById('vehicleSearch');
            const icon = document.getElementById('toggleVehicleSearchIcon');
            if (panel.style.display === 'none' || panel.style.display === '') {
                panel.style.display = 'flex';
                icon.textContent = 'expand_less';
            } else {
                panel.style.display = 'none';
                icon.textContent = 'expand_more';
            }
        }

        function viewVehicle(id) {
            window.location.href = `/dashboard/vehicles/${id}`;
        }

        // Carousel functions
        function initializeCarousel(vehicleId, totalImages) {
            if (!carouselStates[vehicleId]) {
                carouselStates[vehicleId] = { currentIndex: 0, totalImages };
            }
        }

        function updateCarousel(vehicleId) {
            const state = carouselStates[vehicleId];
            const carousel = document.querySelector(`.vehicle-carousel[data-vehicle-id="${vehicleId}"]`);
            if (!carousel) return;

            const track = carousel.querySelector('.carousel-track');
            const indicators = carousel.querySelectorAll('.carousel-indicator');
            
            track.style.transform = `translateX(-${state.currentIndex * 100}%)`;
            
            indicators.forEach((indicator, index) => {
                if (index === state.currentIndex) {
                    indicator.classList.remove('bg-white/50');
                    indicator.classList.add('bg-white', 'w-6');
                } else {
                    indicator.classList.remove('bg-white', 'w-6');
                    indicator.classList.add('bg-white/50');
                }
            });
        }

        function nextImage(vehicleId) {
            const state = carouselStates[vehicleId];
            if (!state) return;
            
            state.currentIndex = (state.currentIndex + 1) % state.totalImages;
            updateCarousel(vehicleId);
        }

        function previousImage(vehicleId) {
            const state = carouselStates[vehicleId];
            if (!state) return;
            
            state.currentIndex = (state.currentIndex - 1 + state.totalImages) % state.totalImages;
            updateCarousel(vehicleId);
        }

        function goToImage(vehicleId, index) {
            const state = carouselStates[vehicleId];
            if (!state) return;
            
            state.currentIndex = index;
            updateCarousel(vehicleId);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize carousels
            document.querySelectorAll('.vehicle-carousel').forEach(carousel => {
                const vehicleId = parseInt(carousel.dataset.vehicleId);
                const totalImages = carousel.querySelectorAll('.carousel-slide').length;
                initializeCarousel(vehicleId, totalImages);
            });

            // Auto-advance carousels on hover
            document.querySelectorAll('.vehicle-carousel').forEach(carousel => {
                const vehicleId = parseInt(carousel.dataset.vehicleId);
                let autoSlideInterval;
                
                carousel.closest('.group').addEventListener('mouseenter', function() {
                    const state = carouselStates[vehicleId];
                    if (state && state.totalImages > 1) {
                        autoSlideInterval = setInterval(() => {
                            nextImage(vehicleId);
                        }, 3000);
                    }
                });
                
                carousel.closest('.group').addEventListener('mouseleave', function() {
                    if (autoSlideInterval) {
                        clearInterval(autoSlideInterval);
                    }
                });
            });

            // Add hover effects to vehicle cards
            document.querySelectorAll('.vehicle-card').forEach(card => {
                card.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-6px)';
                    this.style.boxShadow = '0 12px 30px rgba(0, 0, 0, 0.12)';
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
