<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SendaSnap') }} - @yield('title', 'Dashboard')</title>

    {!! \Devrabiul\ToastMagic\Facades\ToastMagic::styles() !!}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons: Google Material Symbols Rounded (Outlined) -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"
        rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- NProgress -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @if (file_exists(public_path('build/manifest.json')))
    @php($manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true))
        @if (isset($manifest['resources/css/app.css']['file']))
            <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
        @endif
    @else
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>
                <span class="material-symbols-rounded">directions_car</span>
                SendaSnap
            </h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">home</span>
                    Dashboard
                </a></li>
            <li><a href="{{ route('dashboard.vehicles') }}"
                    class="{{ request()->routeIs('dashboard.vehicles') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">directions_car</span>
                    Vehicles
                </a></li>
            <li><a href="{{ route('dashboard.tasks') }}"
                    class="{{ request()->routeIs('dashboard.tasks') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">task_alt</span>
                    Tasks
                </a></li>
            @if(auth()->user()->role === 'admin')
                <li><a href="{{ route('dashboard.users') }}"
                        class="{{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">group</span>
                        Users
                    </a></li>
            @endif
            <li style="margin-top: 24px; padding-top: 24px; border-top: 1px solid hsl(var(--border));">
                <a href="#" onclick="logout()" style="color: #ef4444;">
                    <span class="material-symbols-rounded">logout</span>
                    Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>@yield('title', 'Dashboard')</h1>
            <div class="header-actions" style="position: relative;">
                <div class="user-menu" onclick="toggleUserMenu()">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ auth()->user()->role }}</div>
                    </div>
                    <span class="material-symbols-rounded">expand_more</span>
                </div>

                <!-- User Dropdown Menu -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-item" onclick="viewProfile()">
                        <span class="material-symbols-rounded">person</span>
                        <span>Profile</span>
                    </div>
                    <div class="dropdown-item" onclick="editProfile()">
                        <span class="material-symbols-rounded">settings</span>
                        <span>Settings</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item logout" onclick="logout()">
                        <span class="material-symbols-rounded">logout</span>
                        <span>Logout</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>

    <!-- Chat Widget -->
    <button class="chat-toggle" onclick="toggleChat()">
        <span class="material-symbols-rounded">chat</span>
    </button>

    <div class="chat-widget" id="chatWidget">
        <div class="chat-header">
            <h4>Team Chat</h4>
            <button class="chat-close" onclick="toggleChat()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message received">
                <div>Welcome to SendaSnap team chat!</div>
                <div class="message-time">{{ now()->format('H:i') }}</div>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="chatInput" placeholder="Type a message..." onkeypress="handleChatKeyPress(event)">
        </div>
    </div>

    {!! \Devrabiul\ToastMagic\Facades\ToastMagic::scripts() !!}

    @if (file_exists(public_path('build/manifest.json')))
    @php($manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true))
    @if (isset($manifest['resources/js/app.js']['file']))
        <script defer src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
    @endif
    @endif

    <script>
        // NProgress setup
        document.addEventListener('DOMContentLoaded', function () {
            NProgress.start();
            setTimeout(() => NProgress.done(), 1000);
        });

        // Chat functionality
        function toggleChat() {
            const chatWidget = document.getElementById('chatWidget');
            chatWidget.style.display = chatWidget.style.display === 'none' ? 'block' : 'none';
            if (chatWidget.style.display === 'block') {
                chatWidget.classList.add('slide-in');
            }
        }

        function handleChatKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();

            if (message) {
                addMessage(message, 'sent');
                input.value = '';

                // Simulate response
                setTimeout(() => {
                    addMessage('Thanks for your message!', 'received');
                }, 1000);
            }
        }

        function addMessage(text, type) {
            const messagesContainer = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type} fade-in`;

            const time = new Date().toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit'
            });

            messageDiv.innerHTML = `
                <div>${text}</div>
                <div class="message-time">${time}</div>
            `;

            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // User menu functionality
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('userDropdown');
            const userMenu = document.querySelector('.user-menu');

            if (!userMenu.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        function viewProfile() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.remove('show');

            Swal.fire({
                title: 'User Profile',
                html: `
                    <div style="text-align: left;">
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Name</label>
                            <input type="text" value="{{ auth()->user()->name }}" readonly style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px; background: hsl(var(--muted));">
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email</label>
                            <input type="email" value="{{ auth()->user()->email }}" readonly style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px; background: hsl(var(--muted));">
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Role</label>
                            <input type="text" value="{{ ucfirst(auth()->user()->role) }}" readonly style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px; background: hsl(var(--muted));">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Edit Profile',
                cancelButtonText: 'Close',
                confirmButtonColor: 'hsl(var(--primary))',
                width: '400px'
            }).then((result) => {
                if (result.isConfirmed) {
                    editProfile();
                }
            });
        }

        function editProfile() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.remove('show');

            Swal.fire({
                title: 'Edit Profile',
                html: `
                    <div style="text-align: left;">
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Name</label>
                            <input type="text" value="{{ auth()->user()->name }}" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Phone</label>
                            <input type="tel" value="{{ auth()->user()->phone ?? '' }}" placeholder="Enter phone number" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Current Password</label>
                            <input type="password" placeholder="Enter current password" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">New Password</label>
                            <input type="password" placeholder="Enter new password" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save Changes',
                cancelButtonText: 'Cancel',
                confirmButtonColor: 'hsl(var(--primary))',
                width: '400px'
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast('Profile updated successfully', 'success');
                }
            });
        }

        function logout() {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
            }

            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Logging out...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit logout form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("logout") }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Toast notifications (ToastMagic)
        const toastMagic = new ToastMagic();
        function showToast(message, type = 'success') {
            switch (type) {
                case 'success':
                    toastMagic.success('Success', message);
                    break;
                case 'error':
                    toastMagic.error('Error', message);
                    break;
                case 'warning':
                    toastMagic.warning('Warning', message);
                    break;
                default:
                    toastMagic.info('Info', message);
            }
        }

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });

        // Add loading states to buttons
        document.addEventListener('click', function (e) {
            if (e.target.matches('.btn')) {
                const btn = e.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<div class="loading"></div>';
                btn.disabled = true;

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 1000);
            }
        });
        @if(session('success'))
            showToast(@json(session('success')), 'success');
        @endif
        @if(session('error'))
            showToast(@json(session('error')), 'error');
        @endif
    </script>
</body>

</html>