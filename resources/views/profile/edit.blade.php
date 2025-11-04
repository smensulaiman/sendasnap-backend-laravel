@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Profile Picture Section -->
        <div class="lg:col-span-1">
            <x-card>
                <x-slot:title>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded" style="color: hsl(var(--primary));">account_circle</span>
                        <span>Profile Picture</span>
                    </div>
                </x-slot:title>
                <div class="text-center">
                    <div class="relative inline-block mb-4">
                        <img id="avatarPreview" src="{{ $user->avatar_url }}" alt="Avatar preview" 
                             class="w-32 h-32 object-cover rounded-full border-4 border-white shadow-lg mx-auto"
                             style="border-color: hsl(var(--border));" />
                        <div class="absolute bottom-0 right-0 bg-white rounded-full p-2 shadow-lg border"
                             style="border-color: hsl(var(--border)); background: hsl(var(--surface));">
                            <span class="material-symbols-rounded text-sm" style="color: hsl(var(--primary));">photo_camera</span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="avatarInput" class="btn btn-outline cursor-pointer">
                            <span class="material-symbols-rounded" style="font-size: 18px;">upload</span>
                            Choose Photo
                        </label>
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" class="hidden">
                    </div>
                    <p class="text-xs text-muted">JPG, PNG or GIF. Max size 2MB.</p>
                    @error('avatar')
                        <div class="text-sm mt-2" style="color:hsl(var(--danger));">{{ $message }}</div>
                    @enderror
                </div>
            </x-card>

            <!-- Account Info -->
            <x-card class="mt-4">
                <x-slot:title>Account Information</x-slot:title>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Email</span>
                        <span class="text-sm font-medium">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Role</span>
                        <x-badge>{{ ucfirst($user->role) }}</x-badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Member since</span>
                        <span class="text-sm">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Profile Form Section -->
        <div class="lg:col-span-2">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <x-card>
                    <x-slot:title>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-rounded" style="color: hsl(var(--primary));">edit</span>
                            <span>Personal Information</span>
                        </div>
                    </x-slot:title>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium mb-2">Full Name</label>
                            <input class="input" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                   required placeholder="Enter your full name">
                            @error('name')
                                <div class="text-sm mt-1" style="color:hsl(var(--danger));">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="phone" class="block text-sm font-medium mb-2">Phone Number</label>
                            <input class="input" type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                   placeholder="Enter your phone number">
                            @error('phone')
                                <div class="text-sm mt-1" style="color:hsl(var(--danger));">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </x-card>

                <!-- Password Change Section -->
                <x-card class="mt-4">
                    <x-slot:title>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-rounded" style="color: hsl(var(--secondary));">lock</span>
                            <span>Change Password</span>
                        </div>
                    </x-slot:title>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium mb-2">Current Password</label>
                            <input class="input" type="password" id="current_password" name="current_password" 
                                   placeholder="Enter current password">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium mb-2">New Password</label>
                            <input class="input" type="password" id="password" name="password" 
                                   placeholder="Enter new password">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirm New Password</label>
                            <input class="input" type="password" id="password_confirmation" name="password_confirmation" 
                                   placeholder="Confirm new password">
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3" style="background: hsl(var(--primary) / .05); border-color: hsl(var(--primary) / .2);">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-rounded text-sm mt-0.5" style="color: hsl(var(--primary));">info</span>
                                <div class="text-sm" style="color: hsl(var(--primary));">
                                    <strong>Password Requirements:</strong>
                                    <ul class="mt-1 space-y-1 text-xs">
                                        <li>• At least 8 characters long</li>
                                        <li>• Leave blank to keep current password</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-6">
                    <x-button variant="outline" :href="route('profile.show')">
                        <span class="material-symbols-rounded" style="font-size: 18px;">arrow_back</span>
                        Cancel
                    </x-button>
                    <x-button variant="primary" type="submit">
                        <span class="material-symbols-rounded" style="font-size: 18px;">save</span>
                        Save Changes
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const input = document.getElementById('avatarInput');
            const preview = document.getElementById('avatarPreview');
            
            if (input && preview) {
                input.addEventListener('change', function() {
                    const file = this.files && this.files[0];
                    if (file) {
                        // Validate file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('File size must be less than 2MB');
                            this.value = '';
                            return;
                        }
                        
                        // Validate file type
                        if (!file.type.match(/^image\/(jpeg|jpg|png|gif)$/)) {
                            alert('Please select a valid image file (JPG, PNG, or GIF)');
                            this.value = '';
                            return;
                        }
                        
                        const reader = new FileReader();
                        reader.onload = e => {
                            preview.src = e.target.result;
                            preview.style.transform = 'scale(1.05)';
                            setTimeout(() => {
                                preview.style.transform = 'scale(1)';
                            }, 200);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Password strength indicator (optional enhancement)
            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    // Add visual feedback for password strength if needed
                });
            }
        });
    </script>

    <style>
        .w-32 { width: 8rem; }
        .h-32 { height: 8rem; }
        .rounded-full { border-radius: 9999px; }
        .border-4 { border-width: 4px; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .relative { position: relative; }
        .absolute { position: absolute; }
        .bottom-0 { bottom: 0; }
        .right-0 { right: 0; }
        .hidden { display: none; }
        .cursor-pointer { cursor: pointer; }
        .space-y-1 > * + * { margin-top: 0.25rem; }
        .space-y-3 > * + * { margin-top: 0.75rem; }
        .mt-0.5 { margin-top: 0.125rem; }
        input { transition: all 0.2s ease; }
        img { transition: transform 0.2s ease; }
    </style>
@endsection


