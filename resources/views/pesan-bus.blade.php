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
                                <a href="{{ route('pariwisata.index') }}" class="btn btn-secondary">Batal</a>
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
                    <div class="card-header pb-0 text-center">
                        <h6>Calendar</h6>
                    </div>
                    <div class="card-body">
                        <div id='calendar' class="text-center"></div>
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
                    @csrf <!-- CSRF token -->
                    <input type="hidden" id="bus-id" name="bus_pariwisata_id" value="{{ $bus->id }}">
                    <div class="mb-3">
                        <label for="start-date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start-date" name="start_date">
                    </div>
                    <div class="mb-3">
                        <label for="end-date" class="form-label">End Date <small class="form-text text-muted"> (Plus 1 hari dari end date aslinya) </small></label>
                        <input type="date" class="form-control" id="end-date" name="end_date">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="customer" class="form-label">Customer</label>
                        <select class="form-control" id="customer" name="customer_id">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="evidence_image">Bukti Transaksi</label>
                        <small class="form-text text-muted">(Maks. 2MB)</small>
                        <input type="file" class="form-control-file file-selector-button" name="evidence_image" id="evidence_image">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-event">Save changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection



<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-UTO9MLQXrC7CcgjFx96uhO1wL/8TgF1VbHQ3SyV2/cJb2KvQ8T2g1jilOPXqzzILccaqTJPzI9Nk4dY6Pfj78A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-wpU+gwhgekh8wpoRW5jGn/Hlw2GZLkX1oN8dQ8uTzVD5bC/UgHe4R8C5FjP8K4y2Jbq0Xox1gXjPRQyt8GVt7A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src='fullcalendar/dist/index.global.js'></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var amountInput = document.getElementById('amount');

        amountInput.addEventListener('input', function(e) {
            var value = e.target.value.replace(/\./g, '');
            if (!isNaN(value) && value.length > 0) {
                value = parseFloat(value).toLocaleString('de-DE');
            }
            e.target.value = value;
        });

        amountInput.addEventListener('blur', function(e) {
            var value = e.target.value.replace(/\./g, '');
            if (!isNaN(value) && value.length > 0) {
                e.target.value = parseFloat(value).toLocaleString('de-DE');
            }
        });

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
                    url: `{{ route('pesan-bus.list') }}?plat_nomor=${encodeURIComponent(platNomor)}`,
                    type: 'GET',
                    success: function(data) {
                        console.log('Events data:', data);
                        successCallback(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to fetch events:', error);
                        failureCallback(error);
                    }
                });
            },
            eventContent: function(arg) {
                // Return HTML elements to display within the event
                var title = document.createElement('div');
                title.innerHTML = `<strong>Customer ${arg.event.title}</strong>`;

                var description = document.createElement('div');
                description.innerHTML = `${arg.event.extendedProps.description}`;

                return {
                    domNodes: [title, description]
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

        $('#save-event').on('click', function() {
            var formData = new FormData($('#event-form')[0]);
            $.ajax({
                url: `{{ route('pesan-bus.store') }}`,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#modal-action').modal('hide');
                    calendar.refetchEvents();
                    alert('Event saved successfully!');
                },
                error: function(response) {
                    alert('Failed to save event.');
                }
            });
        });
    });
</script>