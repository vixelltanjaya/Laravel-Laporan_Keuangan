@extends('layouts.user_type.external')
@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-8 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row" id="bus-container">
            @include('components.alert-danger-success')
            @foreach ($bisPariwisata as $index => $pariwisata)
            <div class="col-lg-4 mb-4 bus-item" data-index="{{ $index }}">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Bus Pariwisata</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 pb-0">
                        <div class="col-md-10">
                            <img src="{{ Storage::url($pariwisata->evidence_image_bus) }}" alt="Bus Pariwisata" class="img-fluid" style="width: 250px; height: 250px;">
                        </div>
                        <p class="mb-1">Medium Bus ini dapat menampung hingga 33 penumpang</p>
                        <p class="mb-1">Fasilitas:</p>
                        <ul>
                            <li>AC</li>
                            <li>Tempat duduk yang nyaman</li>
                            <li>Sound system</li>
                            <li>TV</li>
                        </ul>
                        <p class="mb-1">Nomor Plat: <strong>{{ $pariwisata->plat_nomor }}</strong></p>
                        <p class="mb-1">Harga Sewa/hari : <strong>{{ number_format($pariwisata->selling_price, 0, ',', '.') }}</strong></p>
                        <!-- Hidden Inputs -->
                        <input type="hidden" name="plat_nomor" value="{{ $pariwisata->plat_nomor }}">
                        <input type="hidden" name="tahun_kendaraan" value="{{ $pariwisata->tahun_kendaraan }}">
                        <input type="hidden" name="karoseri" value="{{ $pariwisata->karoseri }}">
                        <input type="hidden" name="evidence_image" value="{{ $pariwisata->evidence_image }}">

                        <div class="d-flex justify-content-between my-2">
                            <div>
                                <form action="{{ route('book-bus-external.index') }}" method="GET" class="d-inline">
                                    <input type="hidden" name="plat_nomor" value="{{ $pariwisata->plat_nomor }}">
                                    <button type="submit" class="btn btn-default">Pesan</button>
                                </form>
                                <button type="button" class="btn btn-info" onclick="viewDetails(this)">Lihat Detail</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12">
                <nav>
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination items will be generated here by jQuery -->
                    </ul>
                </nav>
            </div>
        </div>
</main>

<!-- view details -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Surat Layak Jalan</h5>
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/2.5.0/remixicon.min.css">
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

    $(document).ready(function() {
        const itemsPerPage = 3;
        const busItems = $('.bus-item');
        const totalItems = busItems.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        function showPage(page) {
            busItems.hide();
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            busItems.slice(start, end).show();

            // Update active class on pagination buttons
            $('#pagination li').removeClass('active');
            $('#pagination li[data-page="' + page + '"]').addClass('active');
        }

        function createPagination() {
            for (let i = 1; i <= totalPages; i++) {
                $('#pagination').append('<li class="page-item" data-page="' + i + '"><a class="page-link" href="#">' + i + '</a></li>');
            }

            // Add click event for pagination
            $('#pagination li').click(function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                showPage(page);
            });
        }

        // Initialize the pagination
        createPagination();

        // Show the first page by default
        showPage(1);
    });
</script>