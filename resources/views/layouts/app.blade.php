<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SendaSnap') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- NProgress -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 84% 4.9%;
            --primary: 221.2 83.2% 53.3%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96%;
            --secondary-foreground: 222.2 84% 4.9%;
            --muted: 210 40% 96%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --accent: 210 40% 96%;
            --accent-foreground: 222.2 84% 4.9%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 210 40% 98%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 221.2 83.2% 53.3%;
            --radius: 0.5rem;
        }

        .dark {
            --background: 222.2 84% 4.9%;
            --foreground: 210 40% 98%;
            --card: 222.2 84% 4.9%;
            --card-foreground: 210 40% 98%;
            --popover: 222.2 84% 4.9%;
            --popover-foreground: 210 40% 98%;
            --primary: 217.2 91.2% 59.8%;
            --primary-foreground: 222.2 84% 4.9%;
            --secondary: 217.2 32.6% 17.5%;
            --secondary-foreground: 210 40% 98%;
            --muted: 217.2 32.6% 17.5%;
            --muted-foreground: 215 20.2% 65.1%;
            --accent: 217.2 32.6% 17.5%;
            --accent-foreground: 210 40% 98%;
            --destructive: 0 62.8% 30.6%;
            --destructive-foreground: 210 40% 98%;
            --border: 217.2 32.6% 17.5%;
            --input: 217.2 32.6% 17.5%;
            --ring: 224.3 76.3% 94.1%;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
            line-height: 1.5;
            font-size: 14px;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: hsl(var(--card));
            border-right: 1px solid hsl(var(--border));
            padding: 24px 0;
            z-index: 50;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 0 24px 32px;
            border-bottom: 1px solid hsl(var(--border));
            margin-bottom: 24px;
        }

        .sidebar-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: hsl(var(--foreground));
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0 16px;
        }

        .sidebar-nav li {
            margin-bottom: 4px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: hsl(var(--muted-foreground));
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .sidebar-nav a:hover {
            background-color: hsl(var(--accent));
            color: hsl(var(--accent-foreground));
        }

        .sidebar-nav a.active {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }

        .sidebar-nav i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background-color: hsl(var(--background));
        }

        .header {
            background: hsl(var(--card));
            border-bottom: 1px solid hsl(var(--border));
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: hsl(var(--foreground));
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 8px;
            background: hsl(var(--accent));
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-menu:hover {
            background: hsl(var(--muted));
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            min-width: 200px;
            z-index: 50;
            display: none;
            margin-top: 8px;
            overflow: hidden;
        }

        .user-dropdown.show {
            display: block;
            animation: fadeInDown 0.2s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: hsl(var(--foreground));
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .dropdown-item:hover {
            background: hsl(var(--accent));
            color: hsl(var(--accent-foreground));
        }

        .dropdown-item.logout {
            color: #ef4444;
        }

        .dropdown-item.logout:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        .dropdown-divider {
            height: 1px;
            background: hsl(var(--border));
            margin: 4px 0;
        }

        .dropdown-item i {
            width: 16px;
            text-align: center;
            font-size: 14px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, hsl(var(--primary)), hsl(var(--primary) / 0.8));
            color: hsl(var(--primary-foreground));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 500;
            font-size: 14px;
            color: hsl(var(--foreground));
        }

        .user-role {
            font-size: 12px;
            color: hsl(var(--muted-foreground));
            text-transform: capitalize;
        }

        .content {
            padding: 32px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 12px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, hsl(var(--primary)), hsl(var(--primary) / 0.7));
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: hsl(var(--primary) / 0.1);
            color: hsl(var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: hsl(var(--foreground));
            margin-bottom: 4px;
        }

        .stat-label {
            color: hsl(var(--muted-foreground));
            font-size: 14px;
            font-weight: 500;
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-top: 8px;
        }

        .stat-change.positive {
            color: #10b981;
        }

        .stat-change.negative {
            color: #ef4444;
        }

        .card {
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 12px;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            padding: 24px;
            border-bottom: 1px solid hsl(var(--border));
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: hsl(var(--foreground));
        }

        .card-body {
            padding: 24px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid hsl(var(--border));
        }

        .table th {
            background-color: hsl(var(--muted));
            font-weight: 600;
            color: hsl(var(--foreground));
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr:hover {
            background-color: hsl(var(--accent));
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-running {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-low {
            background-color: #f3f4f6;
            color: #374151;
        }

        .badge-medium {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-high {
            background-color: #fed7aa;
            color: #ea580c;
        }

        .badge-urgent {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }

        .btn-primary:hover {
            background-color: hsl(var(--primary) / 0.9);
        }

        .btn-secondary {
            background-color: hsl(var(--secondary));
            color: hsl(var(--secondary-foreground));
        }

        .btn-secondary:hover {
            background-color: hsl(var(--secondary) / 0.8);
        }

        .btn-outline {
            background-color: transparent;
            color: hsl(var(--foreground));
            border: 1px solid hsl(var(--border));
        }

        .btn-outline:hover {
            background-color: hsl(var(--accent));
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
        }

        .pagination a {
            padding: 8px 12px;
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            color: hsl(var(--foreground));
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .pagination a:hover {
            background: hsl(var(--accent));
        }

        .pagination .active {
            background: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
            border-color: hsl(var(--primary));
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .chat-widget {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 320px;
            height: 400px;
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            z-index: 1000;
            display: none;
        }

        .chat-header {
            padding: 16px;
            border-bottom: 1px solid hsl(var(--border));
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h4 {
            font-size: 16px;
            font-weight: 600;
            color: hsl(var(--foreground));
        }

        .chat-close {
            background: none;
            border: none;
            color: hsl(var(--muted-foreground));
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
        }

        .chat-close:hover {
            background: hsl(var(--accent));
        }

        .chat-messages {
            height: 280px;
            overflow-y: auto;
            padding: 16px;
        }

        .chat-input {
            padding: 16px;
            border-top: 1px solid hsl(var(--border));
        }

        .chat-input input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid hsl(var(--border));
            border-radius: 8px;
            font-size: 14px;
        }

        .chat-toggle {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 56px;
            height: 56px;
            background: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            transition: all 0.3s ease;
        }

        .chat-toggle:hover {
            transform: scale(1.1);
        }

        .message {
            margin-bottom: 12px;
            padding: 8px 12px;
            border-radius: 8px;
            max-width: 80%;
            font-size: 14px;
        }

        .message.sent {
            background: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
            margin-left: auto;
        }

        .message.received {
            background: hsl(var(--muted));
            color: hsl(var(--muted-foreground));
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 4px;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid hsl(var(--border));
            border-radius: 50%;
            border-top-color: hsl(var(--primary));
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .content {
                padding: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>
                <i class="fas fa-car"></i>
                SendaSnap
            </h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a></li>
            <li><a href="{{ route('dashboard.vehicles') }}"
                    class="{{ request()->routeIs('dashboard.vehicles') ? 'active' : '' }}">
                    <i class="fas fa-car"></i>
                    Vehicles
                </a></li>
            <li><a href="{{ route('dashboard.tasks') }}"
                    class="{{ request()->routeIs('dashboard.tasks') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i>
                    Tasks
                </a></li>
            @if(auth()->user()->role === 'admin')
                <li><a href="{{ route('dashboard.users') }}"
                        class="{{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        Users
                    </a></li>
            @endif
            <li style="margin-top: 24px; padding-top: 24px; border-top: 1px solid hsl(var(--border));">
                <a href="#" onclick="logout()" style="color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i>
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
                    <i class="fas fa-chevron-down"></i>
                </div>

                <!-- User Dropdown Menu -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-item" onclick="viewProfile()">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </div>
                    <div class="dropdown-item" onclick="editProfile()">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item logout" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success fade-in">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error fade-in">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Chat Widget -->
    <button class="chat-toggle" onclick="toggleChat()">
        <i class="fas fa-comments"></i>
    </button>

    <div class="chat-widget" id="chatWidget">
        <div class="chat-header">
            <h4>Team Chat</h4>
            <button class="chat-close" onclick="toggleChat()">
                <i class="fas fa-times"></i>
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

        // Toast notifications
        function showToast(message, type = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
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
    </script>
</body>

</html>