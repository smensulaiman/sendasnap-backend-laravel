@extends('layouts.app')

@section('title', 'Vehicle Details')

@section('content')
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-4 mb-3">
                <x-button variant="outline" size="icon" onclick="window.location.href='{{ route('dashboard.vehicles') }}'" class="hover-lift">
                    <span class="material-symbols-rounded">arrow_back</span>
                </x-button>
                <div>
                    <div class="title-lg mb-1">Vehicle Details</div>
                    <p class="text-muted text-sm">{{ $vehicle->serial_number }}</p>
                </div>
            </div>
        </div>
        <div>
            <x-badge :variant="match($vehicle->status){'ready'=>'secondary','sold'=>'primary','in_yard'=>'secondary',default=>'primary'}" class="text-base px-4 py-2">
                {{ ucfirst($vehicle->status) }}
            </x-badge>
        </div>
    </div>

    <!-- Main Vehicle Image Section -->
    @if($vehicle->photos->count() > 0)
        <x-card class="mb-8 p-0 overflow-hidden">
            <div class="relative h-96 md:h-[500px] bg-gradient-to-br from-gray-100 to-gray-200">
                <img id="mainVehicleImage" 
                     src="{{ asset('storage/' . $vehicle->photos->first()->photo_path) }}" 
                     alt="Main Vehicle Image"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <div class="text-white">
                        <div class="text-3xl font-bold mb-2">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                        <div class="text-lg opacity-90">{{ $vehicle->year }} • {{ $vehicle->serial_number }}</div>
                    </div>
                </div>
            </div>
        </x-card>
    @endif

    <!-- Vehicle Information Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Basic Information -->
        <x-card class="p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                    <span class="material-symbols-rounded" style="color: hsl(var(--primary));">info</span>
                </div>
                <h3 class="title-md">Basic Information</h3>
            </div>
            <div class="space-y-5">
                <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Serial Number</div>
                    <div class="font-semibold text-lg">{{ $vehicle->serial_number }}</div>
                </div>
                <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Make & Model</div>
                    <div class="font-semibold text-lg">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                </div>
                <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Year</div>
                    <div class="font-semibold text-lg">{{ $vehicle->year }}</div>
                </div>
                <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Chassis Model</div>
                    <div class="font-semibold">{{ $vehicle->chassis_model }}</div>
                </div>
                <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Engine</div>
                    <div class="font-semibold text-lg">{{ $vehicle->cc }}cc</div>
                </div>
                <div>
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Color</div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full border-2 shadow-sm" 
                             style="background: {{ strtolower($vehicle->color) === 'white' ? '#ffffff' : (strtolower($vehicle->color) === 'black' ? '#000000' : (strtolower($vehicle->color) === 'red' ? '#ef4444' : (strtolower($vehicle->color) === 'blue' ? '#3b82f6' : '#6b7280'))) }}; border-color: hsl(var(--border));"></div>
                        <span class="font-semibold text-lg">{{ $vehicle->color }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Additional Details -->
        <x-card class="p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: hsl(var(--secondary) / .1);">
                    <span class="material-symbols-rounded" style="color: hsl(var(--secondary));">description</span>
                </div>
                <h3 class="title-md">Additional Details</h3>
            </div>
            <div class="space-y-5">
                @if($vehicle->plate_number)
                    <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                        <div class="text-xs text-muted mb-2 uppercase tracking-wide">Plate Number</div>
                        <div class="font-semibold">{{ $vehicle->plate_number }}</div>
                    </div>
                @endif
                @if($vehicle->net_weight)
                    <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                        <div class="text-xs text-muted mb-2 uppercase tracking-wide">Net Weight</div>
                        <div class="font-semibold text-lg">{{ number_format($vehicle->net_weight, 2) }} <span class="text-sm text-muted">kg</span></div>
                    </div>
                @endif
                @if($vehicle->area)
                    <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                        <div class="text-xs text-muted mb-2 uppercase tracking-wide">Area</div>
                        <div class="font-semibold">{{ $vehicle->area }}</div>
                    </div>
                @endif
                @if($vehicle->length && $vehicle->width && $vehicle->height)
                    <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                        <div class="text-xs text-muted mb-2 uppercase tracking-wide">Dimensions</div>
                        <div class="font-semibold">{{ number_format($vehicle->length, 2) }}m × {{ number_format($vehicle->width, 2) }}m × {{ number_format($vehicle->height, 2) }}m</div>
                    </div>
                @endif
                @if($vehicle->buying_price)
                    <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                        <div class="text-xs text-muted mb-2 uppercase tracking-wide">Buying Price</div>
                        <div class="font-semibold text-lg" style="color: hsl(var(--primary));">${{ number_format($vehicle->buying_price, 2) }}</div>
                    </div>
                @endif
                @if($vehicle->vehicle_buy_date)
                    <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                        <div class="text-xs text-muted mb-2 uppercase tracking-wide">Vehicle Buy Date</div>
                        <div class="font-semibold">{{ $vehicle->vehicle_buy_date->format('M d, Y') }}</div>
                    </div>
                @endif
                @if($vehicle->expected_yard_date)
                    <div>
                        <div class="text-xs text-muted mb-2 uppercase tracking-wide">Expected Yard Date</div>
                        <div class="font-semibold">{{ $vehicle->expected_yard_date->format('M d, Y') }}</div>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Metadata -->
        <x-card class="p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                    <span class="material-symbols-rounded" style="color: hsl(var(--primary));">person</span>
                </div>
                <h3 class="title-md">Metadata</h3>
            </div>
            <div class="space-y-5">
                <div class="pb-4 border-b" style="border-color: hsl(var(--border));">
                    <div class="text-xs text-muted mb-3 uppercase tracking-wide">Created By</div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                            <span class="text-base font-semibold" style="color: hsl(var(--primary));">{{ strtoupper(substr($vehicle->creator->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="font-semibold">{{ $vehicle->creator->name }}</div>
                            <div class="text-sm text-muted">{{ ucfirst($vehicle->creator->role) }}</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Created At</div>
                    <div class="font-semibold">{{ $vehicle->created_at->format('M d, Y') }}</div>
                    <div class="text-sm text-muted mt-1">{{ $vehicle->created_at->format('h:i A') }}</div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Consignee Details -->
    @if($vehicle->consigneeDetails)
        <x-card class="mb-8 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: hsl(var(--secondary) / .1);">
                    <span class="material-symbols-rounded" style="color: hsl(var(--secondary));">business</span>
                </div>
                <h3 class="title-md">Consignee Details</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 rounded-lg" style="background: hsl(var(--background) / .5);">
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Name</div>
                    <div class="font-semibold text-lg">{{ $vehicle->consigneeDetails->name }}</div>
                </div>
                <div class="p-4 rounded-lg" style="background: hsl(var(--background) / .5);">
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Phone</div>
                    <div class="font-semibold text-lg">{{ $vehicle->consigneeDetails->phone }}</div>
                </div>
                <div class="p-4 rounded-lg" style="background: hsl(var(--background) / .5);">
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Email</div>
                    <div class="font-semibold">{{ $vehicle->consigneeDetails->email }}</div>
                </div>
                <div class="p-4 rounded-lg md:col-span-2" style="background: hsl(var(--background) / .5);">
                    <div class="text-xs text-muted mb-2 uppercase tracking-wide">Address</div>
                    <div class="font-semibold">{{ $vehicle->consigneeDetails->address }}</div>
                </div>
            </div>
        </x-card>
    @endif

    <!-- Image Gallery Section -->
    @if($vehicle->photos->count() > 0)
        <x-card class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                        <span class="material-symbols-rounded" style="color: hsl(var(--primary));">photo_library</span>
                    </div>
                    <div>
                        <h3 class="title-md">Vehicle Images</h3>
                        <p class="text-sm text-muted mt-1">{{ $vehicle->photos->count() }} {{ $vehicle->photos->count() === 1 ? 'photo' : 'photos' }} available</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($vehicle->photos as $index => $photo)
                    <div class="relative group cursor-pointer image-gallery-item rounded-lg overflow-hidden border-2 transition-all duration-300 hover:scale-105 hover:shadow-lg" 
                         onclick="openImageModal({{ $index }})"
                         style="aspect-ratio: 1; background: hsl(var(--border)); border-color: hsl(var(--border));">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                             alt="Vehicle Photo {{ $index + 1 }}"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="absolute bottom-0 left-0 right-0 p-3">
                                @if($photo->photo_type)
                                    <span class="text-xs bg-white/90 text-gray-900 px-2 py-1 rounded font-medium">{{ ucfirst($photo->photo_type) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg">
                                <span class="material-symbols-rounded text-gray-900">zoom_in</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @else
        <x-card class="p-12">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: hsl(var(--primary) / .1);">
                    <span class="material-symbols-rounded" style="font-size: 48px; color: hsl(var(--primary) / .5);">image</span>
                </div>
                <h4 class="font-semibold text-lg mb-2">No Images Available</h4>
                <p class="text-muted">No images have been uploaded for this vehicle yet.</p>
            </div>
        </x-card>
    @endif

    <!-- Image Zoom Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black/95 z-50 hidden flex items-center justify-center backdrop-blur-sm">
        <button class="absolute top-6 right-6 text-white hover:text-gray-300 z-10 transition-colors bg-black/50 hover:bg-black/70 rounded-full p-3" onclick="closeImageModal()">
            <span class="material-symbols-rounded text-3xl">close</span>
        </button>
        
        @if($vehicle->photos->count() > 1)
            <button class="absolute left-6 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-10 bg-black/60 hover:bg-black/80 rounded-full p-3 transition-all shadow-lg" 
                    onclick="previousModalImage()">
                <span class="material-symbols-rounded text-4xl">chevron_left</span>
            </button>
            <button class="absolute right-6 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-10 bg-black/60 hover:bg-black/80 rounded-full p-3 transition-all shadow-lg" 
                    onclick="nextModalImage()">
                <span class="material-symbols-rounded text-4xl">chevron_right</span>
            </button>
        @endif

        <div class="relative max-w-7xl max-h-[90vh] w-full h-full flex items-center justify-center p-8">
            <img id="modalImage" 
                 src="" 
                 alt="Vehicle Photo"
                 class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
        </div>

        @if($vehicle->photos->count() > 1)
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white">
                <div class="bg-black/60 backdrop-blur-sm px-4 py-2 rounded-full">
                    <span id="imageCounter" class="text-sm font-medium"></span>
                </div>
            </div>
        @endif
    </div>

    <script>
        const vehiclePhotos = @json($vehicle->photos->map(function($photo) {
            return asset('storage/' . $photo->photo_path);
        })->values()->all());
        
        let currentImageIndex = 0;

        function openImageModal(index) {
            currentImageIndex = index;
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const imageCounter = document.getElementById('imageCounter');
            
            modalImage.src = vehiclePhotos[currentImageIndex];
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            if (vehiclePhotos.length > 1) {
                imageCounter.textContent = `${currentImageIndex + 1} / ${vehiclePhotos.length}`;
            }
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function nextModalImage() {
            if (vehiclePhotos.length > 1) {
                currentImageIndex = (currentImageIndex + 1) % vehiclePhotos.length;
                const modalImage = document.getElementById('modalImage');
                const imageCounter = document.getElementById('imageCounter');
                
                modalImage.src = vehiclePhotos[currentImageIndex];
                imageCounter.textContent = `${currentImageIndex + 1} / ${vehiclePhotos.length}`;
            }
        }

        function previousModalImage() {
            if (vehiclePhotos.length > 1) {
                currentImageIndex = (currentImageIndex - 1 + vehiclePhotos.length) % vehiclePhotos.length;
                const modalImage = document.getElementById('modalImage');
                const imageCounter = document.getElementById('imageCounter');
                
                modalImage.src = vehiclePhotos[currentImageIndex];
                imageCounter.textContent = `${currentImageIndex + 1} / ${vehiclePhotos.length}`;
            }
        }

        // Update main image on thumbnail click
        @if($vehicle->photos->count() > 0)
            document.querySelectorAll('.image-gallery-item').forEach((item, index) => {
                item.addEventListener('click', function() {
                    const mainImage = document.getElementById('mainVehicleImage');
                    if (mainImage) {
                        mainImage.src = vehiclePhotos[index];
                    }
                });
            });
        @endif

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            } else if (e.key === 'ArrowLeft') {
                previousModalImage();
            } else if (e.key === 'ArrowRight') {
                nextModalImage();
            }
        });

        // Close modal on background click
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
    </script>
@endsection
