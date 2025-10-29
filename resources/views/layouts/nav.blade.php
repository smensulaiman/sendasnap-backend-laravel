<div class="container-xxl position-relative p-0" id="home">
    <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
        <a href="" class="navbar-brand p-0">
            <h1 class="m-0">ReadCycle</h1>
            <!-- <img src="img/logo.png" alt="Logo"> -->
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav mx-auto py-0">
                <a href="#home" class="nav-item nav-link active">Home</a>
                <a href="#about" class="nav-item nav-link">About</a>
                <a href="#feature" class="nav-item nav-link">Feature</a>
                <a href="#contact" class="nav-item nav-link">Contact</a>
            </div>
            <div class="navbar-nav py-0">
                @auth
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-user me-2"></i>{{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('dashboard.index') }}" class="dropdown-item">
                                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                            <a href="{{ route('dashboard.books.index') }}" class="dropdown-item">
                                <i class="fa fa-book me-2"></i>My Books
                            </a>
                            <a href="{{ route('dashboard.profile') }}" class="dropdown-item">
                                <i class="fa fa-user me-2"></i>Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
        @else
            <a href="{{ route('login') }}" class="nav-item nav-link">
                <i class="fa fa-sign-in-alt me-2"></i>Login
            </a>
            <a href="{{ route('register') }}" class="nav-item nav-link">
                <i class="fa fa-user-plus me-2"></i>Register
            </a>
            <a href="{{ route('swagger') }}" class="nav-item nav-link">
                <i class="fa fa-code me-2"></i>API Docs
            </a>
        @endauth
            </div>
        </div>
    </nav>
</div>
