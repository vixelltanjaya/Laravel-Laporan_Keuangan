<!-- Navbar -->
<nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 my-3 {{ (Request::is('static-sign-up') ? 'w-100 shadow-none  navbar-transparent mt-4' : 'blur blur-rounded shadow py-2 start-0 end-0 mx4') }}">
    <div class="container-fluid {{ (Request::is('static-sign-up') ? 'container' : 'container-fluid') }}">
        <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon mt-2">
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navigation">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link me-2" href="{{ auth()->user() ? route('pariwisata-external.index') : url('pariwisata-external') }}">
                        <i class="ri-bus-fill opacity-6 me-1 {{ (Request::is('pariwisata-external.index') ? '' : 'text-dark') }}"></i>
                        Book Now!
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="{{ auth()->user() ? route('landing-page') : url('landing-page') }}">
                        <i class="ri-team-fill opacity-6 me-1 {{ (Request::is('landing-page') ? '' : 'text-dark') }}"></i>
                        About Us
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav d-lg-block d-none">
                <!-- Additional Nav Items can go here -->
            </ul>
        </div>
    </div>
</nav>

<!-- End Navbar -->