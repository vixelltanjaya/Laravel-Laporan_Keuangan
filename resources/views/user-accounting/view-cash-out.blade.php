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
                            <p class="mb-1">Dibuat Tanggal: <strong>{{ \Carbon\Carbon::parse($journalEntry->created_at)->format('d M Y') }}</strong></p>
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

                    @if($journalEntry->is_reversed == 0 && empty($journalEntry->evidence_code_origin))
                    <p class="mb-1">Status: <strong>Baru</strong></p>
                    @elseif($journalEntry->is_reversed == 0 && !empty($journalEntry->evidence_code_origin))
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">Status: <strong>Baru</strong></p>
                            <p class="mb-1">Kode Bukti Asal: <strong>{{ $journalEntry->evidence_code_origin }}</strong></p>
                        </div>
                    </div>
                    @elseif($journalEntry->is_reversed == 1)
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">Status: <strong>Koreksi</strong></p>
                            <p class="mb-1">Kode Bukti Asal: <strong>{{ $journalEntry->evidence_code_origin }}</strong></p>
                        </div>
                        <div class="text-right">
                            <p class="mb-1">Koreksi Oleh: <strong>{{ $journalEntry->reversed_by }}</strong></p>
                            <p class="mb-1">Waktu Koreksi: <strong>{{ $journalEntry->reversed_at }}</strong></p>
                        </div>
                    </div>
                    @elseif($journalEntry->is_reversed == 2)
                    <p class="mb-1">Status: <strong>Terkoreksi</strong></p>
                    @endif
                    <div class="d-flex justify-content-between">
                        <div class="d-flex">
                            @if($id)
                            @if($journalEntry->is_reversed == 1 || $journalEntry->is_reversed == 0)
                            <a href="{{ route('correcting-entry.index', ['id' => $id]) }}" id="reversedJournalButton" class="btn bg-gradient-dark me-2">Jurnal Koreksi</a>
                            @elseif($journalEntry->is_reversed == 2)
                            <a href="#" id="reversedJournalButton" class="btn bg-gradient-dark me-2 disabled">Jurnal Koreksi</a>
                            @endif
                            @if($hasAccount2101)
                            <a href="{{ route('pelunasan-pariwisata.index', ['id' => $id]) }}" id="pelunasanJournalButton" class="btn btn-info">Pelunasan</a>
                            @endif
                            @endif
                        </div>
                        <div class="flex-grow-1"></div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('cash-out.index') }}" class="btn btn-secondary"><i class="fas fa-angle-left me-1"></i>Kembali</a>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#evidenceModal">Lihat Bukti</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.modal-bukti-transaksi')

</main>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var reversedJournalButton = document.getElementById('reversedJournalButton');
        var isReversed = @json($journalEntry -> is_reversed);

        if (isReversed === 2 || isReversed === 1) {
            reversedJournalButton.classList.add('disabled');
        }

        // Get the image element
        var evidenceImage = document.getElementById('evidenceImage');

        // Set the src attribute using the data-image-path
        var imagePath = evidenceImage.getAttribute('data-image-path');
        evidenceImage.src = imagePath;

        document.getElementById('openImage').addEventListener('click', function() {
            const imagePath = document.getElementById('evidenceImage').dataset.imagePath;
            window.open(imagePath, '_blank');
        });

        console.log('Image path:', imagePath);
        console.log('Evidence image element:', evidenceImage);
    });
</script>