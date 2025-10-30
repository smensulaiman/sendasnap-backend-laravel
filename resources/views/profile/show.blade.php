@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Profile Overview Card -->
        <div class="lg:col-span-1">
            <x-card>
                <div class="text-center">
                    <div class="relative inline-block mb-4">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                             class="w-32 h-32 object-cover rounded-full border-4 border-white shadow-lg mx-auto"
                             style="border-color: hsl(var(--border));" />
                        <div class="absolute bottom-0 right-0 bg-green-500 w-6 h-6 rounded-full border-2 border-white"
                             style="background: hsl(var(--secondary)); border-color: hsl(var(--surface));"
                             title="Active"></div>
                    </div>
                    <div class="title-lg mb-2">{{ $user->name }}</div>
                    <div class="text-muted mb-3">{{ $user->email }}</div>
                    <x-badge :variant="'primary'" class="mb-4">{{ ucfirst($user->role) }}</x-badge>
                    
                    <div class="flex gap-2 justify-center">
                        <x-button variant="primary" :href="route('profile.edit')">
                            <span class="material-symbols-rounded" style="font-size: 18px;">edit</span>
                            Edit Profile
                        </x-button>
                    </div>
                </div>
            </x-card>

            <!-- Quick Stats -->
            <x-card class="mt-4">
                <x-slot:title>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded" style="color: hsl(var(--primary));">analytics</span>
                        <span>Quick Stats</span>
                    </div>
                </x-slot:title>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="title-md" style="color: hsl(var(--primary));">{{ $user->assignedTasks->count() }}</div>
                        <div class="text-xs text-muted">Tasks Assigned</div>
                    </div>
                    <div>
                        <div class="title-md" style="color: hsl(var(--secondary));">{{ $user->createdTasks->count() }}</div>
                        <div class="text-xs text-muted">Tasks Created</div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Profile Details -->
        <div class="lg:col-span-2">
            <!-- Personal Information -->
            <x-card>
                <x-slot:title>
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded" style="color: hsl(var(--primary));">person</span>
                            <span>Personal Information</span>
                        </div>
                        <x-button variant="outline" size="sm" :href="route('profile.edit')">
                            <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                            Edit
                        </x-button>
                    </div>
                </x-slot:title>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Full Name</label>
                            <div class="p-3 bg-gray-50 rounded-lg border" style="background: hsl(var(--muted)); border-color: hsl(var(--border));">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-sm" style="color: hsl(var(--primary));">badge</span>
                                    <span class="font-medium">{{ $user->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Email Address</label>
                            <div class="p-3 bg-gray-50 rounded-lg border" style="background: hsl(var(--muted)); border-color: hsl(var(--border));">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-sm" style="color: hsl(var(--primary));">email</span>
                                    <span class="font-medium">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Phone Number</label>
                            <div class="p-3 bg-gray-50 rounded-lg border" style="background: hsl(var(--muted)); border-color: hsl(var(--border));">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-sm" style="color: hsl(var(--primary));">phone</span>
                                    <span class="font-medium">{{ $user->phone ?: 'Not provided' }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Role</label>
                            <div class="p-3 bg-gray-50 rounded-lg border" style="background: hsl(var(--muted)); border-color: hsl(var(--border));">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-sm" style="color: hsl(var(--primary));">admin_panel_settings</span>
                                    <x-badge :variant="'primary'">{{ ucfirst($user->role) }}</x-badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Account Activity -->
            <x-card class="mt-4">
                <x-slot:title>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded" style="color: hsl(var(--secondary));">history</span>
                        <span>Account Activity</span>
                    </div>
                </x-slot:title>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg" style="background: hsl(var(--muted));">
                        <div class="flex items-center justify-center mb-2">
                            <span class="material-symbols-rounded" style="color: hsl(var(--primary)); font-size: 24px;">calendar_today</span>
                        </div>
                        <div class="text-sm font-medium">Member Since</div>
                        <div class="text-xs text-muted mt-1">{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg" style="background: hsl(var(--muted));">
                        <div class="flex items-center justify-center mb-2">
                            <span class="material-symbols-rounded" style="color: hsl(var(--secondary)); font-size: 24px;">update</span>
                        </div>
                        <div class="text-sm font-medium">Last Updated</div>
                        <div class="text-xs text-muted mt-1">{{ $user->updated_at->diffForHumans() }}</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg" style="background: hsl(var(--muted));">
                        <div class="flex items-center justify-center mb-2">
                            <span class="material-symbols-rounded" style="color: hsl(var(--primary)); font-size: 24px;">verified</span>
                        </div>
                        <div class="text-sm font-medium">Account Status</div>
                        <div class="text-xs mt-1">
                            <x-badge :variant="'secondary'">Active</x-badge>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Recent Activity -->
            @if($user->assignedTasks->count() > 0)
            <x-card class="mt-4">
                <x-slot:title>
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded" style="color: hsl(var(--secondary));">task_alt</span>
                            <span>Recent Tasks</span>
                        </div>
                        <x-button variant="outline" size="sm" :href="route('schedule.index')">
                            <span class="material-symbols-rounded" style="font-size: 16px;">open_in_new</span>
                            View All
                        </x-button>
                    </div>
                </x-slot:title>
                <div class="space-y-3">
                    @foreach($user->assignedTasks->take(3) as $task)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border" style="background: hsl(var(--muted)); border-color: hsl(var(--border));">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-rounded text-sm" style="color: hsl(var(--primary));">task</span>
                                <div>
                                    <div class="font-medium text-sm">{{ $task->title }}</div>
                                    <div class="text-xs text-muted">{{ $task->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <x-badge :variant="match($task->status){'completed'=>'secondary','pending'=>'primary','running'=>'secondary',default=>'primary'}">
                                {{ ucfirst($task->status) }}
                            </x-badge>
                        </div>
                    @endforeach
                </div>
            </x-card>
            @endif
        </div>
    </div>

    <style>
        .space-y-3 > * + * { margin-top: 0.75rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .w-6 { width: 1.5rem; }
        .h-6 { height: 1.5rem; }
    </style>
@endsection


