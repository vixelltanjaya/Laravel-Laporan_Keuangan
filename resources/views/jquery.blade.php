@extends('dashboard.body.main')

@section('title', 'Transaction')


@section('specificpagestyles')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
                <div>
                    <div>
                        <div style="display: flex; align-items: center;">
                            <a href="{{ route('transaction.index') }}">
                                <h6 style="color:#121F3E; text-decoration: underline;">Transaction</h6>
                            </a>
                            <span class="mx-2 mb-2" style="color:#ABB3C4;">/</span>
                            <a class="mb-2" style="color:#676e8a;">Create Transaction</a>
                        </div>
                        <p style="color:#ABB3C4;">Create your transaction</p>
                    </div>
                </div>
            </div>
            <div class="card border-none">

                <div class="card-body">
                    <form action="{{ route('transaction.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <!-- begin: Input Data -->

                        <div class=" row align-items-center">

                            <div class="form-group col-md-6">
                                <label for="made_by">Dibuat Oleh:</label>
                                <input type="text" class="form-control" name="made_by" value="{{ auth()->user()->name }}" readonly></input>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="ref">Nomor Referensi<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ref" value="{{ $ref }}" readonly></input>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="journal_master_id">Pilih Transaksi</label>
                                <select name="journal_master_id" id="journal_master_id" class="form-control" required>
                                    <option value="">Pilih jenis transaksi</option>
                                    @foreach($journalMasters as $journalMaster)
                                        <option value="{{ $journalMaster->id }}">{{ $journalMaster->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="transaction_date">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" >
                            </div>

                            <div class="form-group col-md-12">
                                <label for="notes">Catatan Transaksi<span class="text-danger">*</span></label>
                                <textarea rows="2" class="form-control" name="notes" placeholder="Masukkan catatan ..."></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="evidence_image">Bukti Transaksi</label>
                                <input type="file" class="form-control-file file-selector-button" name="evidence_image" id="evidence_image">
                            </div>

                            <div class="form-group col-md-12">
                                <input type="text" class="form-control" name="status" id="status" value="success" hidden>
                            </div>

                            @foreach($journalMasters as $journalMaster)
                                <div id="journalDetails_{{ $journalMaster->id }}" style="display: none;">
                                    <hr>
                                    <div>
                                        <div style="display: flex; align-items: center;">
                                            <h4 style="color:#121F3E">Jurnal Akuntansi</h4>
                                        </div>
                                        <p style="color:#ABB3C4;">Masukkan nominal debit dan kredit dengan jumlah yang sama</p>
                                    </div>
                                    <div class="form-group row font-weight-bold text-center ">
                                        <div class="col-md-6">
                                            Jurnal
                                        </div>
                                        <div class="col-md-3">
                                            Debit
                                        </div>
                                        <div class="col-md-3">
                                            Kredit
                                        </div>
                                    </div>
                                    @foreach($journalMaster->details as $index => $detail)
                                        {{-- <input type="hidden" name="coa_id[]" value="{{ $detail->coa_id }}"> --}}
                                        @if($detail->debit_or_credit == 'debit')
                                            <div class="form-group row">
                                                <div class="col-md-6 d-flex align-items-center">
                                                    {{ $detail->coa->kode }}
                                                    &emsp; 
                                                    {{ $detail->coa->nama }}                                                
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="coa_id[{{ $index }}]" value="{{ $detail->coa_id }}">
                                                    <input type="number" class="form-control" name="debit[{{ $index }}]" placeholder="Debit">
                                                    <input type="hidden" class="form-control" name="credit[{{ $index }}]" placeholder="Debit" value="0">
                                                </div>
                                                <div class="col-md-3">
                                                </div>
                                            </div>
                                        @elseif($detail->debit_or_credit == 'kredit')
                                            <div class="form-group row">
                                                <div class="col-md-6 d-flex align-items-center">
                                                    &emsp;
                                                    &emsp;
                                                    {{ $detail->coa->kode }}
                                                    &emsp;
                                                    {{ $detail->coa->nama }}
                                                </div>
                                                <div class="col-md-3">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="coa_id[{{ $index }}]" value="{{ $detail->coa_id }}">
                                                    <input type="number" class="form-control" name="credit[{{ $index }}]" placeholder="Credit">
                                                    <input type="hidden" class="form-control" name="debit[{{ $index }}]" placeholder="Debit" value="0">
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                                                                                                                
                                </div>

                            @endforeach

                            
                            
                        </div>
                        
                        <div id="journalDetailsContainer"></div>
                        <div id="errorAlert" class="alert alert-danger" style="display: none;">
                            <strong>Alert!</strong> Nominal debit dan kredit belum seimbang.
                        </div>
                        <div id="errorAlert" class="alert alert-danger" style="display: none;">
                            <strong>Alert!</strong> Nominal debit dan kredit belum seimbang.
                        </div>
                        <div id="successAlert" class="alert alert-success" style="display: none;">
                            <strong>Success!</strong> Nominal debit dan kredit sudah seimbang.
                        </div>

                        <!-- end: Input Data -->
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary mr-2" id="submitButton" disabled>Submit</button>
                            <a class="btn bg-danger" href="{{ route('transaction.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

@endsection

@push('js')
<script>
    document.getElementById('journal_master_id').addEventListener('change', function() {
        var selectedId = this.value;
        var journalDetailsContainer = document.getElementById('journalDetailsContainer');
        var submitButton = document.getElementById('submitButton');

        // Clear previous content
        journalDetailsContainer.innerHTML = '';

        // Ambil data detail jurnal
        var journalDetails = document.getElementById('journalDetails_' + selectedId).innerHTML;

        // Tampilkan detail jurnal ke dalam container
        journalDetailsContainer.innerHTML = journalDetails;

        // Tambahkan event listener untuk setiap input debit dan kredit
        var debitInputs = document.querySelectorAll('input[name^="debit["]');
        var creditInputs = document.querySelectorAll('input[name^="credit["]');

        submitButton.disabled = true;

        // Validasi ketika nilai input berubah
        debitInputs.forEach(input => {
            input.addEventListener('change', function() {
                validateDebitCredit();
            });
        });

        creditInputs.forEach(input => {
            input.addEventListener('change', function() {
                validateDebitCredit();
            });
        });

        // Fungsi untuk validasi jumlah total debit dan kredit
        function validateDebitCredit() {
            var totalDebit = 0;
            var totalCredit = 0;

            // Hitung total debit
            debitInputs.forEach(input => {
                totalDebit += parseFloat(input.value) || 0;
            });

            // Hitung total kredit
            creditInputs.forEach(input => {
                totalCredit += parseFloat(input.value) || 0;
            });

            // Periksa apakah total debit dan kredit sama
            if (totalDebit !== totalCredit) {
                // Tampilkan pesan error
                document.getElementById('errorAlert').style.display = 'block';
                document.getElementById('successAlert').style.display = 'none';
                // Disable tombol Submit
                submitButton.disabled = true;
            } else {
                document.getElementById('errorAlert').style.display = 'none';
                document.getElementById('successAlert').style.display = 'block';
                submitButton.disabled = false;
            }
        }
    });
</script>
@endpush

<!-- Modal batal jurnal
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
</div> -->