@extends('layouts.user_type.guest')
@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Pesan Bus</h6>
                    </div>

                    @include('components.alert-danger-success')

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
                            <div class="d-flex justify-content-between my-2">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
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
                    <div class="card-header pb-0 text-left">
                        <h3>Calendar</h3>
                        <strong class="text-danger"><span class="text-danger">*</span></label>Klik tanggal untuk booking tanggal keberangkatan</strong>
                    </div>
                    <div class="card-body">
                        <div id="calendar-container" style="display:flex ;justify-content:center ;margin: 0 auto">
                            <div id="calendar" style="max-width:800px; width:100%" class="text-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- modal -->
<div id="modal-action" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Event</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="event-form">
                    <div class="mb-3">
                        <label for="start-date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start-date" name="start_date">
                    </div>
                    <div class="mb-3">
                        <label for="end-date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end-date" name="end_date">
                    </div>
                    <div class="mb-3">
                        <label for="nama_customer" class="form-label">Nama Lengkap Pemesan</label>
                        <input type="text" class="form-control" id="nama_customer" name="nama_customer"></input>
                    </div>
                    <div class="mb-3">
                        <label for="no_telp" class="form-label">No Telepon (HP)</label>
                        <input type="text" class="form-control" id="no_telp" name="no_telp"></input>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <input type="hidden" id="plat_nomor" name="plat_nomor" value="{{ $bus->plat_nomor }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" id="save-event">
                    <i class="ri-whatsapp-line"></i> Chat WA
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/2.5.0/remixicon.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var platNomor = @json($bus -> plat_nomor);

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            selectable: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            buttonText: {
                dayGridMonth: 'Booking',
                listWeek: 'List Booking'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: `{{ route('book-bus-external.list') }}?plat_nomor=${encodeURIComponent(platNomor)}`,
                    type: 'GET',
                    success: function(data) {
                        console.log('events data:', data);
                        successCallback(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('gagal fetching events data:', error);
                        failureCallback(error);
                    }
                });
            },
            eventContent: function(arg) {
                var description = document.createElement('div');
                description.innerHTML = `${arg.event.extendedProps.description}`;

                return {
                    domNodes: [description]
                };
            },
            dateClick: function(info) {
                var selectedDate = new Date(info.dateStr);
                selectedDate.setHours(0, 0, 0, 0);

                var today = new Date();
                today.setHours(0, 0, 0, 0);

                if (selectedDate < today) {
                    alert("Tanggal yang dipilih sudah lewat.");
                    return;
                }

                // Check if the selected date already has events
                var hasEvents = calendar.getEvents().some(function(event) {
                    var eventStart = new Date(event.start);
                    eventStart.setHours(0, 0, 0, 0);
                    return eventStart.getTime() === selectedDate.getTime();
                });

                console.log('Has events: ', hasEvents); // Log the result for debugging

                if (hasEvents) {
                    alert("Tanggal yang dipilih sudah ada booking.");
                    return;
                }
                $('#start-date').val(info.dateStr);
                $('#modal-action').modal('show');
            }
        });
        calendar.render();
    });
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('save-event').addEventListener('click', function() {
            var namaCustomer = document.getElementById('nama_customer').value;
            var startDate = document.getElementById('start-date').value;
            var endDate = document.getElementById('end-date').value;
            var noTelp = document.getElementById('no_telp').value;
            var description = document.getElementById('description').value;
            var platNomor = document.getElementById('plat_nomor').value;

            var message = `Nama: ${namaCustomer}\nTanggal Berangkat: ${startDate} \n Tanggal Pulang: ${endDate}\nNo Telepon: ${noTelp}\nDescription: ${description}\nPlat nomor: ${platNomor}`;
            var whatsappUrl = `https://wa.me/6287832412825?text=${encodeURIComponent(message)}`; // testing only using dev phone number

            window.open(whatsappUrl, '_blank');
        });
    });
</script>