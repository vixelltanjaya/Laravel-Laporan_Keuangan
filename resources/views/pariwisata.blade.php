@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
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

        <div class="row">
            @foreach ($bisPariwisata as $index => $pariwisata)
            @if ($index % 3 == 0 && $index != 0)
        </div>
        <div class="row">
            @endif
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Bus Pariwisata</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 pb-0">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNbeS6M-tGimKyz1ku-mF6leMXmmWTivktpgnARz4JMA&s" alt="Bis Pariwisata">
                        <h5 class="mt-3">Medium Bus</h5>
                        <p class="mb-1">Medium Bus ini dapat menampung hingga 33 penumpang</p>
                        <p class="mb-1">Fasilitas:</p>
                        <ul>
                            <li>AC</li>
                            <li>Tempat duduk yang nyaman</li>
                            <li>Sound system</li>
                            <li>TV</li>
                        </ul>
                        <p class="mb-1">Nomor Plat: <strong>{{ $pariwisata->plat_nomor }}</strong> (Hanya untuk pengguna internal yang telah terautentikasi)</p>
                        <!-- Hidden Inputs -->
                        <input type="hidden" name="plat_nomor" value="{{ $pariwisata->plat_nomor }}">
                        <input type="hidden" name="tahun_kendaraan" value="{{ $pariwisata->tahun_kendaraan }}">
                        <input type="hidden" name="karoseri" value="{{ $pariwisata->karoseri }}">
                        <input type="hidden" name="evidence_image" value="{{ $pariwisata->evidence_image }}">

                        <div class="d-flex justify-content-between my-2">
                            <div>
                                <form action="{{ route('pesan-bus.index') }}" method="GET" class="d-inline">
                                    <input type="hidden" name="plat_nomor" value="{{ $pariwisata->plat_nomor }}">
                                    <button type="submit" class="btn btn-default">Pesan</button>
                                </form>
                                <button type="button" class="btn btn-info" onclick="viewDetails(this)">Lihat Detail</button>
                            </div>
                        </div>
                        <p class="mt-2">Status: <span id="statusLabel{{ $index }}" class="fw-bold">Tidak Diketahui</span></p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!--  Table for Internal Uses-->
    <div class="col-md-12 mb-lg-0 mb-4">
        <div class="card mt-4">
            <div class="card-header pb-0 p-3">
                <a href="{{ url('add-data-pariwisata') }}" class="btn btn-primary"> Tambah Data Bis</a>
                <h6 class="mb-0">Data Kendaraan</h6>
            </div>
            <div class="card-body p-3">
                <table id="vehicleTable" class="display">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Plat Nomor</th>
                            <th>Tahun Kendaraan</th>
                            <th>Karoseri</th>
                            <th>Nomor Rangka</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bisPariwisata as $index => $pariwisata)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pariwisata->plat_nomor }}</td>
                            <td>{{ $pariwisata->tahun_kendaraan }}</td>
                            <td>{{ $pariwisata->karoseri }}</td>
                            <td>{{ $pariwisata->no_rangka }}</td>
                            <td>
                                <button class="btn btn-link text-secondary font-weight-bold text-small " data-id="{{$pariwisata->id}}" data-plat="{{$pariwisata->plat_nomor}}" data-brand="{{$pariwisata->karoseri}}" data-tahun_kendaraan="{{$pariwisata->tahun_kendaraan}}" data-no_rangka="{{$pariwisata->no_rangka}}" data-bs-toggle="modal" data-bs-target="#editBusModal">
                                    <i class="fas fa-user-edit"></i> Edit
                                </button>
                                <button class="btn btn-link text-danger font-weight-bold text-small" data-bs-toggle="modal" data-bs-target="#deleteBusModal" data-id="{{$pariwisata->id}}" data-plat="{{$pariwisata->plat_nomor}}">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Edit -->
<div class="modal" id="editBusModal" tabindex="-1" role="dialog" aria-labelledby="editBusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBusModalLabel">Edit Bus</h5>
            </div>
            <div class="modal-body">
                <form id="editBusForm" method="POST" action="">
                    @method('PATCH')
                    @csrf
                    <input type="hidden" id="pariwisata_id" name="id">
                    <div class="form-group">
                        <label for="edit_plat">Plat Nomor</label>
                        <input type="text" class="form-control" id="edit_plat" name="plat" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_tahun_kendaraan">Tahun Kendaraan</label>
                        <input type="text" class="form-control" id="edit_tahun_kendaraan" name="tahun_kendaraan" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_brand">Karoseri</label>
                        <input type="text" class="form-control" id="edit_brand" name="brand" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_no_rangka">No Rangka</label>
                        <input type="text" class="form-control" id="edit_no_rangka" name="no_rangka" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteBusModal" tabindex="-1" role="dialog" aria-labelledby="deleteBusLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBusLabel">Hapus Bis Pariwisata</h5>
            </div>
            <form id="deleteBusForm" method="POST">
                @method('DELETE')
                @csrf
                <div class="modal-body">
                    <p id="deleteBusMessage"></p>
                    <input type="hidden" id="delete_bus" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBus">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Detail Bus Pariwisata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="evidenceImage" src="{{ isset($pariwisata) && $pariwisata->evidence_image ? Storage::url($pariwisata->evidence_image) : 'path/to/default-image.jpg' }}" alt="Evidence Image" class="img-fluid mb-3">
                <p id="detailPlatNomor"></p>
                <p id="detailTahunKendaraan"></p>
                <p id="detailKaroseri"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the image element
        var evidenceImage = document.getElementById('evidenceImage');

        // Set the src attribute using the data-image-path
        evidenceImage.src = evidenceImage.getAttribute('data-image-path');

        console.log('path', evidenceImage);
    });

    function viewDetails(button) {
        // Get the data attributes from the button
        var platNomor = $(button).closest('.card-body').find('input[name="plat_nomor"]').val();
        var tahunKendaraan = $(button).closest('.card-body').find('input[name="tahun_kendaraan"]').val();
        var karoseri = $(button).closest('.card-body').find('input[name="karoseri"]').val();
        var noRangka = $(button).closest('.card-body').find('input[name="no_rangka"]').val();
        var evidenceImagePath = $(button).closest('.card-body').find('input[name="evidence_image"]').val();

        // Set the modal data
        $('#evidenceImage').attr('src', '/storage/' + evidenceImagePath);
        $('#detailPlatNomor').text('Plat Nomor: ' + platNomor);
        $('#detailTahunKendaraan').text('Tahun Kendaraan: ' + tahunKendaraan);
        $('#detailKaroseri').text('Karoseri: ' + karoseri);
        $('#detailNoRangka').text('Nomor Rangka: ' + noRangka);

        // Show the modal
        $('#viewDetailsModal').modal('show');
    }

    // function toggleStatus(button) {
    //     var index = button.getAttribute('data-index');

    //     console.log('apa indexnya ' +index);

    //     if (button.dataset.status === 'booked') {
    //         button.dataset.status = 'tersedia';
    //         button.classList.remove('btn-secondary');
    //         button.classList.add('btn-success');
    //         button.innerHTML = 'tersedia';
    //         setStatus(button, 'tersedia');
    //     } else {
    //         button.dataset.status = 'booked';
    //         button.classList.remove('btn-success');
    //         button.classList.add('btn-secondary');
    //         button.innerHTML = 'Booked';
    //         setStatus(button, 'booked');
    //     }
    // }

    function setStatus(button, status) {
        var index = button.getAttribute('data-index');
        var statusLabel = document.getElementById('statusLabel' + index);
        statusLabel.innerText = status.charAt(0).toUpperCase() + status.slice(1);
    }

    $(document).ready(function() {
        $('#vehicleTable').DataTable();

        $('#editBusModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var plat = button.data('plat');
            var tahun_kendaraan = button.data('tahun_kendaraan');
            var no_rangka = button.data('no_rangka');
            var brand = button.data('brand');

            console.log("apa nama brand? " + brand);

            var modal = $(this);
            modal.find('.modal-body #pariwisata_id').val(id);
            modal.find('.modal-body #edit_plat').val(plat);
            modal.find('.modal-body #edit_tahun_kendaraan').val(tahun_kendaraan);
            modal.find('.modal-body #edit_no_rangka').val(no_rangka);
            modal.find('.modal-body #edit_brand').val(brand);

            // var form = modal.find('form');
            // var action = form.attr('action').replace(':id', id);
            // form.attr('action', action);
            // console.log("Form action URL: " + form.attr('action'));
            // console.log("ID Akun di modal: " + modal.find('.modal-body #pariwisata_id').val());
        });

        $('#deleteBusModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var plat = button.data('plat');

            console.log("Req Id to be deleted ? " + id);

            var modal = $(this);
            var message = 'Apakah Anda yakin ingin menghapus data ' + plat + ' ?';
            modal.find('.modal-body #deleteBusMessage').text(message);
            modal.find('.modal-body #delete_bus').val(id);
            $('#deleteBusForm').attr('action', '/pariwisata/' + id);
        });

        // Handle form submission
        $('#deleteBusForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>