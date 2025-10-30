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

        <div class="user-dropdown" id="userDropdown">
            <a class="dropdown-item" href="{{ route('profile.show') }}">
                <span class="material-symbols-rounded">person</span>
                <span>Profile</span>
            </a>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <span class="material-symbols-rounded">settings</span>
                <span>Edit Profile</span>
            </a>
            <div class="dropdown-divider"></div>
            <div class="dropdown-item logout" onclick="logout()">
                <span class="material-symbols-rounded">logout</span>
                <span>Logout</span>
            </div>
        </div>
    </div>
</div>