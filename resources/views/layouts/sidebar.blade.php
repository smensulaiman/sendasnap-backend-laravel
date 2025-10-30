<div class="sidebar">
    <div class="sidebar-header">
        <h2>
            <span class="material-symbols-rounded">directions_car</span>
            SendaSnap
        </h2>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="material-symbols-rounded">home</span>
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('dashboard.vehicles') }}"
                class="{{ request()->routeIs('dashboard.vehicles') ? 'active' : '' }}">
                <span class="material-symbols-rounded">directions_car</span>
                Vehicles
            </a>
        </li>
        <li>
            <a href="{{ route('dashboard.tasks') }}"
                class="{{ request()->routeIs('dashboard.tasks') || request()->routeIs('schedule.*') ? 'active' : '' }}">
                <span class="material-symbols-rounded">task_alt</span>
                Tasks
            </a>
            <ul style="list-style:none;margin:6px 0 0 34px;padding:0;display:flex;flex-direction:column;gap:6px;">
                <li>
                    <a href="{{ route('dashboard.tasks') }}"
                        style="font-size: 13px; padding: 6px 12px; border-radius: 6px; display: block; transition: all 0.2s;"
                        class="{{ request()->routeIs('dashboard.tasks') ? 'active' : '' }}">
                        <span class="material-symbols-rounded" style="font-size: 16px; margin-right: 6px;">list</span>
                        All Tasks
                    </a>
                </li>
                <li>
                    <a href="{{ route('schedule.index') }}"
                        style="font-size: 13px; padding: 6px 12px; border-radius: 6px; display: block; transition: all 0.2s;"
                        class="{{ request()->routeIs('schedule.index') ? 'active' : '' }}">
                        <span class="material-symbols-rounded"
                            style="font-size: 16px; margin-right: 6px;">calendar_month</span>
                        Schedule Calendar
                    </a>
                </li>
                <li>
                    <a href="{{ route('schedule.kanban') }}"
                        style="font-size: 13px; padding: 6px 12px; border-radius: 6px; display: block; transition: all 0.2s;"
                        class="{{ request()->routeIs('schedule.kanban') ? 'active' : '' }}">
                        <span class="material-symbols-rounded"
                            style="font-size: 16px; margin-right: 6px;">view_kanban</span>
                        Kanban Board
                    </a>
                </li>
            </ul>
        </li>
        @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <li>
                <a href="{{ route('dashboard.users') }}"
                    class="{{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">group</span>
                    Users
                </a>
            </li>
        @endif
        <li style="margin-top: 24px; padding-top: 24px; border-top: 1px solid hsl(var(--border));">
            <a href="{{ route('profile.show') }}">
                <span class="material-symbols-rounded">account_circle</span>
                Profile
            </a>
        </li>
        <li>
            <a href="#" onclick="logout()" style="color: #ef4444;">
                <span class="material-symbols-rounded">logout</span>
                Logout
            </a>
        </li>
    </ul>
</div>