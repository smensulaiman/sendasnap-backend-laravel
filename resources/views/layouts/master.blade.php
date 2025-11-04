<!DOCTYPE html>
<html lang="en">

<!-- Head -->
@include('layouts.head')

<body>
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="navbar">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline d-md-none me-3" id="sidebarToggle">
                        <span class="material-symbols-rounded">menu</span>
                    </button>
                    <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                    <button class="btn btn-outline dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <span class="material-symbols-rounded me-2">person</span>{{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                <span class="material-symbols-rounded me-2">person</span>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <span class="material-symbols-rounded me-2">settings</span>
                                <span>Edit Profile</span>
                            </a>
                        </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <span class="material-symbols-rounded me-2">logout</span><span>Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span class="material-symbols-rounded me-2">check_circle</span>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span class="material-symbols-rounded me-2">error</span>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {!! \Devrabiul\ToastMagic\Facades\ToastMagic::scripts() !!}

    @if (file_exists(public_path('build/manifest.json')))
    @php($manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true))
    @if (isset($manifest['resources/js/app.js']['file']))
        <script defer src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
    @endif
    @endif

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>