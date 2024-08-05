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
                            <div class="col-md-10">
                                <img src="{{ Storage::url($bus->evidence_image_bus) }}" alt="Bus Pariwisata" class="img-fluid" style="width: 300x; height: 200px;">
                            </div>
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
                                <a href="{{ route('pariwisata.index') }}" class="btn btn-secondary">Kembali</a>
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

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 text-center">
                        <h6>
                            Tabel Keberangkatan
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Description</th>
                                        <th>Start Book</th>
                                        <th>End Book</th>
                                        <th>Kendaraan Keluar</th>
                                        <th>Kendaraan Balik</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                @foreach ($bookPlat as $booking )
                                <tbody>
                                    <tr>
                                        <td>{{$booking->name}}</td>
                                        <td>{{$booking->description}}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->start_book)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->end_book)->format('d-m-Y') }}</td>
                                        <td>{{ $booking->fleet_departure }}</td>
                                        <td>{{ $booking->fleet_arrivals }}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#jamModal" data-bs-booking-id="{{ $booking->id }}" data-bs-fleet-departure="{{ $booking->fleet_departure }}" data-bs-fleet-arrivals="{{ $booking->fleet_arrivals }}" data-bs-start-date="{{$booking->start_book}}" data-bs-end-date="{{$booking->end_book}}" data-bs-description="{{$booking->description}}" data-bs-customer-id="{{$booking->customer_id}}">
                                                Update
                                            </button>
                                            <form action="{{ route('pesan-bus.destroy', $booking->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this booking?')">
                                                    Batal
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- modal add event -->
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
                <form id="event-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="start-date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start-book" name="start_date">
                    </div>
                    <div class="mb-3">
                        <label for="end-date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end-book" name="end_date">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="desc" name="description"></textarea>
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
                    <input type="hidden" id="bus-id" name="bus_pariwisata_id" value="{{$bus->id}}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-event">Save changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Set Jam-->
<div class="modal fade" id="jamModal" tabindex="-1" aria-labelledby="jamModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jamModalLabel">Update Data Keberangkatan dan Pulang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateJamForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="bus-id" name="bus_pariwisata_id" value="{{$bus->id}}">
                    <input type="hidden" id="platNo" name="plat_nomor" value="{{$bus->plat_nomor}}">
                    <div class="mb-3">
                        <label for="start-date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start-date" name="start_date">
                    </div>
                    <div class="mb-3">
                        <label for="end-date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end-date" name="end_date">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="customer-id" class="form-label">Customer</label>
                        <select class="form-control" id="customer-id" name="customer_id" readonly>
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fleet_departure" class="form-label">Jam Keberangkatan</label>
                        <input type="datetime-local" class="form-control" id="fleet_departure" name="fleet_departure">
                    </div>
                    <div class="mb-3">
                        <label for="fleet_arrivals" class="form-label">Jam Kedatangan</label>
                        <input type="datetime-local" class="form-control" id="fleet_arrivals" name="fleet_arrivals">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

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
            var busId = $('#bus-id').val();
            formData.set('bus_pariwisata_id', busId);

            var evidenceImage = $('#evidence_image')[0].files[0];
            if (evidenceImage) {
                formData.append('evidence_image', evidenceImage);
            }
            
            console.log('bus id nya apaaa ', busId);
            console.log('evidenceImage', evidenceImage);

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

        var jamModal = document.getElementById('jamModal');
        jamModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var bookingId = button.getAttribute('data-bs-booking-id');
            var fleetDeparture = button.getAttribute('data-bs-fleet-departure');
            var fleetArrivals = button.getAttribute('data-bs-fleet-arrivals');
            var startDate = button.getAttribute('data-bs-start-date');
            var endDate = button.getAttribute('data-bs-end-date');
            var description = button.getAttribute('data-bs-description');
            var customerId = button.getAttribute('data-bs-customer-id');

            var form = document.getElementById('updateJamForm');
            form.action = `/pesan-bus/${bookingId}`;

            document.getElementById('fleet_departure').value = fleetDeparture;
            document.getElementById('fleet_arrivals').value = fleetArrivals;
            document.getElementById('start-date').value = startDate;
            document.getElementById('end-date').value = endDate;
            document.getElementById('description').value = description;
            document.getElementById('customer-id').value = customerId;
            document.getElementById('bus-id').value = bookingId;
        });
    });
</script>