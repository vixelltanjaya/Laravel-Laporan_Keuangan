@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Pesan Bus</h6>
                    </div>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if (session('berhasil'))
                    <div class="alert alert-success">
                        <ul>
                            <li>{{ session('berhasil') }}</li>
                        </ul>
                    </div>
                    @endif
                    @if (isset($bus))
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                        </div>
                        <div class="card-body p-3 pb-0">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNbeS6M-tGimKyz1ku-mF6leMXmmWTivktpgnARz4JMA&s" alt="Bis Pariwisata1">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNbeS6M-tGimKyz1ku-mF6leMXmmWTivktpgnARz4JMA&s" alt="Bis Pariwisata2">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNbeS6M-tGimKyz1ku-mF6leMXmmWTivktpgnARz4JMA&s" alt="Bis Pariwisata3">
                            <p class="mb-1">Fasilitas:</p>
                            <ul>
                                <li>AC</li>
                                <li>Tempat duduk yang nyaman</li>
                                <li>Sound system</li>
                                <li>TV/Karaoke</li>
                                <li>33 Seat</li>
                            </ul>
                            <p class="mb-1">Nomor Plat: <strong>{{ $bus->plat_nomor }}</strong></p>
                            <label for="evidence_image">Bukti Transaksi</label> <small class="form-text text-muted">(Maks. 2MB)</small>
                            <input type="file" class="form-control-file file-selector-button" name="evidence_image" id="evidence_image">
                            <div class="d-flex justify-content-between my-2">
                                <div>
                                    <form action="{{ route('pesan-bus.store') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="plat_nomor" value="{{ $bus->plat_nomor }}">
                                        <button type="submit" class="btn btn-primary me-2">Melakukan Pemesanan</button>
                                    </form>
                                    <a href="{{ route('pariwisata.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <p>Bus tidak ditemukan.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        Calendar
                    </div>
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection


<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth'
        });
        calendar.render();
    });
</script>