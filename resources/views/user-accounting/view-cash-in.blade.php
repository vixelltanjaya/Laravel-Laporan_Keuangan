@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Detail Transaksi</h5>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">Deskripsi: <strong>{{ $journalEntry->description }}</strong></p>
                            <p class="mb-1">Kode: <strong>{{ $journalEntry->evidence_code }}</strong></p>
                        </div>
                        <div class="text-right">
                            <p class="mb-1">{{ \Carbon\Carbon::parse($journalEntry->created_at)->format('d M Y') }}</p>
                            <p class="mb-1">Dibuat Oleh: <strong>{{ $journalEntry->user_name }}</strong></p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Akun</th>
                                    <th scope="col">Debit</th>
                                    <th scope="col">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details as $detail)
                                <tr>
                                    <td>{{ $detail->account_id }} - {{ $detail->account_name }}</td>
                                    <td>Rp{{ number_format($detail->debit, 0, ',', '.') }}</td>
                                    <td>Rp{{ number_format($detail->credit, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="mb-1">Status: <strong>{{ $journalEntry->is_reversed ? 'Batal' : 'Baru' }}</strong></p>
                    <div class="d-flex justify-content-between">
                        <button id="cancelJournalButton" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelJournalModal">
                            Batalkan Jurnal
                        </button>
                        <a href="{{ route('cash-in.index') }}" class="btn btn-secondary"><i class="fas fa-angle-left me-1"></i>Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- bukti transaksi -->
    <div class="row">
        <div class="col-12">
            <!-- Add margin-top to create space between the card and the content above -->
            <div class="card" style="width: 18rem; margin-top: 20px;">
                <div class="card-body">
                    <h5 class="card-title">Bukti Transaksi</h5>
                    <img id="evidenceImage" data-image-path="{{ Storage::url($detailJournal->evidence_image) }}" alt="Evidence Image" class="img-fluid mb-3" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

</main>
@endsection

<!-- Modal -->
<div class="modal fade" id="cancelJournalModal" tabindex="-1" aria-labelledby="cancelJournalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelJournalModalLabel">Konfirmasi Pembatalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin membatalkan jurnal ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <form action="{{ route('view-cash-in.cancel', $journalEntry->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#confirmCancelButton').on('click', function() {
            // Perform the cancellation logic here
            alert('Jurnal berhasil dibatalkan!');
            // You can add an AJAX request here to handle the cancellation on the server side
            $('#cancelJournalModal').modal('hide');
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        var cancelJournalButton = document.getElementById('cancelJournalButton');
        var isReversed = @json($journalEntry -> is_reversed);

        if (isReversed) {
            cancelJournalButton.classList.add('disabled');
            cancelJournalButton.removeAttribute('data-bs-toggle');
            cancelJournalButton.removeAttribute('data-bs-target');
        }

        // Get the image element
        var evidenceImage = document.getElementById('evidenceImage');

        // Set the src attribute using the data-image-path
        var imagePath = evidenceImage.getAttribute('data-image-path');
        evidenceImage.src = imagePath;

        console.log('Image path:', imagePath);
        console.log('Evidence image element:', evidenceImage);
    });
</script>