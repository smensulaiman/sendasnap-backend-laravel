@extends('layouts.dashboard')

@section('title', 'Users')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>User Management</h3>
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-outline" onclick="showFilters()">
                    <span class="material-symbols-rounded">filter_list</span>
                    Filters
                </button>
                <button class="btn btn-primary" onclick="showAddUserModal()">
                    <span class="material-symbols-rounded">add</span>
                    Add User
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="fade-in">
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div
                                                style="width: 48px; height: 48px; background: linear-gradient(135deg, hsl(var(--primary)), hsl(var(--primary) / 0.8)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 18px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: hsl(var(--foreground)); font-size: 14px;">
                                                    {{ $user->name }}
                                                </div>
                                                <div style="font-size: 11px; color: hsl(var(--muted-foreground));">ID:
                                                    {{ $user->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 500; color: hsl(var(--foreground)); font-size: 14px;">
                                            {{ $user->email }}
                                        </div>
                                        @if($user->email_verified_at)
                                            <div style="font-size: 11px; color: #10b981; display: flex; align-items: center; gap: 4px;">
                                                <span class="material-symbols-rounded">check_circle</span>
                                                Verified
                                            </div>
                                        @else
                                            <div style="font-size: 11px; color: #f59e0b; display: flex; align-items: center; gap: 4px;">
                                                <span class="material-symbols-rounded">error</span>
                                                Unverified
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->role }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->phone)
                                            <div style="font-size: 14px; color: hsl(var(--foreground));">{{ $user->phone }}</div>
                                        @else
                                            <span style="color: hsl(var(--muted-foreground)); font-size: 12px;">No phone</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                                            <span style="font-size: 12px; color: #10b981; font-weight: 500;">Active</span>
                                        </div>
                                    </td>
                                    <td style="font-size: 12px; color: hsl(var(--muted-foreground));">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 4px;">
                                            <button class="btn btn-outline" style="padding: 6px 8px; font-size: 12px;"
                                                onclick="viewUser({{ $user->id }})">
                                                <span class="material-symbols-rounded">visibility</span>
                                            </button>
                                            <button class="btn btn-outline" style="padding: 6px 8px; font-size: 12px;"
                                                onclick="editUser({{ $user->id }})">
                                                <span class="material-symbols-rounded">edit</span>
                                            </button>
                                            <button class="btn btn-outline" style="padding: 6px 8px; font-size: 12px;"
                                                onclick="changeRole({{ $user->id }})">
                                                <span class="material-symbols-rounded">badge</span>
                                            </button>
                                            @if($user->role !== 'admin')
                                                <button class="btn btn-outline"
                                                    style="padding: 6px 8px; font-size: 12px; color: #ef4444;"
                                                    onclick="deleteUser({{ $user->id }})">
                                                    <span class="material-symbols-rounded">delete</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    {{ $users->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px; color: hsl(var(--muted-foreground));">
                    <div
                        style="width: 80px; height: 80px; background: hsl(var(--muted)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 32px;">
                        <span class="material-symbols-rounded">group</span>
                    </div>
                    <h3 style="margin-bottom: 8px; color: hsl(var(--foreground));">No users found</h3>
                    <p style="margin-bottom: 24px;">Get started by adding your first user to the system.</p>
                    <button class="btn btn-primary" onclick="showAddUserModal()">
                        <span class="material-symbols-rounded">add</span>
                        Add User
                    </button>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showFilters() {
            Swal.fire({
                title: 'Filter Users',
                html: `
                        <div style="text-align: left;">
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Role</label>
                                <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                    <option value="employee">Employee</option>
                                    <option value="client">Client</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Status</label>
                                <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="verified">Verified</option>
                                    <option value="unverified">Unverified</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Search</label>
                                <input type="text" placeholder="Search by name or email" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
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

        function showAddUserModal() {
            Swal.fire({
                title: 'Add New User',
                html: `
                        <div style="text-align: left;">
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Full Name *</label>
                                <input type="text" placeholder="Enter full name" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            </div>
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email Address *</label>
                                <input type="email" placeholder="Enter email address" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Password *</label>
                                    <input type="password" placeholder="Enter password" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Confirm Password *</label>
                                    <input type="password" placeholder="Confirm password" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Role *</label>
                                    <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                                        <option value="client">Client</option>
                                        <option value="employee">Employee</option>
                                        <option value="manager">Manager</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Phone Number</label>
                                    <input type="tel" placeholder="Enter phone number" style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                                </div>
                            </div>
                        </div>
                    `,
                showCancelButton: true,
                confirmButtonText: 'Add User',
                cancelButtonText: 'Cancel',
                confirmButtonColor: 'hsl(var(--primary))',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast('User added successfully', 'success');
                }
            });
        }

        function viewUser(id) {
            showToast(`Viewing user ${id}`, 'info');
        }

        function editUser(id) {
            showToast(`Editing user ${id}`, 'info');
        }

        function changeRole(id) {
            Swal.fire({
                title: 'Change User Role',
                html: `
                        <div style="text-align: left;">
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Select New Role</label>
                                <select style="width: 100%; padding: 8px 12px; border: 1px solid hsl(var(--border)); border-radius: 6px;">
                                    <option value="client">Client</option>
                                    <option value="employee">Employee</option>
                                    <option value="manager">Manager</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                    `,
                showCancelButton: true,
                confirmButtonText: 'Change Role',
                cancelButtonText: 'Cancel',
                confirmButtonColor: 'hsl(var(--primary))',
                width: '400px'
            }).then((result) => {
                if (result.isConfirmed) {
                    showToast('User role changed successfully', 'success');
                }
            });
        }

        function deleteUser(id) {
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
                    showToast('User deleted successfully', 'success');
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