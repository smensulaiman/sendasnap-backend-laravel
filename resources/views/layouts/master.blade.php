<!DOCTYPE html>
<html lang="en">

@include('layouts.header')

<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="51">
<div class="container-xxl bg-white p-0">
    <!-- Spinner Start -->
    @include('layouts.spinner')
    <!-- Spinner End -->


    <!-- Navbar Start -->
    @include('layouts.nav')
    <!-- Navbar End -->

    @yield('content')

    <!-- Footer Start -->
    @include('layouts.footer')
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-lg-square back-to-top pt-2"><i class="bi bi-arrow-up text-white"></i></a>
</div>

@stack('scripts')

</body>

</html>
