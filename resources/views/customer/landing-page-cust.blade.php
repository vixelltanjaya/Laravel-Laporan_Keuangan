@extends('layouts.user_type.external')
@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <!-- Hero Section -->
        <section class="hero mb-4">
            <div class="container">
                <div class="text-center text-white d-flex flex-column justify-content-center align-items-center" style="height: 70vh; background: url('hero-image.jpg') no-repeat center center/cover;">
                    <h1>Welcome to PT Maharani Putra Sejahtera </h1>
                    <p>Your trusted partner for travel experiences</p>
                </div>
            </div>
        </section>
        <!-- About Us Section -->
        <section id="about" class="py-5">
            <div class="container">
                <h2>About Us</h2>
                <p>Maharani Tour menyediakan bus pariwisata terbaik untuk kebutuhan perjalanan Anda. Kami memastikan kenyamanan, keamanan, dan pengalaman perjalanan yang tak terlupakan.</p>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="bg-light py-5">
            <div class="container">
                <h2>Layanan Kami</h2>
                <ul>
                    <li>Kursi nyaman</li>
                    <li>Sound system dan TV/Karaoke</li>
                    <li>Crew yang professional dan berpengalaman</li>
                    <li>Bus ber-AC</li>
                </ul>
            </div>
        </section>

        <!-- Fleet Section -->
        <section id="fleet" class="py-5">
            <div class="container">
                <h2>Armada Kami</h2>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('storage/bus_images/4b3d3c98-cfd8-4abe-b2b4-38c4711f5460.jpg') }}" alt="Bus 1" class="img-fluid">
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('storage/bus_images/552722ce-d490-4ac6-a8a0-8e4bd4cfaf3a.jpg') }}" alt="Bus 2" class="img-fluid">
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('storage/bus_images/dfd2efbd-8e6c-4d06-90bd-03c885676bc5.jpg') }}" alt="Bus 3" class="img-fluid">
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Us Section -->
        <section id="contact" class="py-5">
            <div class="container">
                <h2>Contact Us</h2>
                <div class="d-flex justify-content-around align-items-center">
                    <div class="contact-item mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <p id="address">Jl. Candi No.56, Kudorejan, Urutsewu, Kec. Ampel, Kabupaten Boyolali, Jawa Tengah 57352.</p>
                    </div>
                    <div class="contact-item mb-3 text-center">
                        <label for="whatsapp" class="form-label">WhatsApp</label>
                        <p id="whatsapp">
                            <a href="https://wa.me/6287832412825" target="_blank">
                                <i class="ri-whatsapp-line" style="font-size: 2em;"></i> <!-- Increase the icon size here -->
                            </a>
                        </p>
                    </div>
                    <div class="contact-item mb-3 text-center">
                        <label for="instagram" class="form-label">Instagram</label>
                        <p id="instagram">
                            <a href="https://www.instagram.com/maharanibyl62" target="_blank">
                                <i class="ri-instagram-line" style="font-size: 2em;"></i> <!-- Increase the icon size here -->
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/2.5.0/remixicon.min.css">
@endsection
