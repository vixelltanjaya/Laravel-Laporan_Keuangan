@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
            </div>
            <a href="{{ route('cash-in.index')}}" class="btn btn-default"> <i class="fas fa-angle-left me-1">Back</i></a>
            <div class="card border-none">

                <div class="card-body">
                    @include('components.alert-danger-success')
                    <form action="{{ route('cash-in.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row align-items-center">

                            <div class="form-group col-md-6">
                                <label for="made_by">Dibuat Oleh:</label>
                                <input type="text" class="form-control" name="made_by" value="{{ auth()->user()->name }}" readonly>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="ref">No Ref</label>
                                <input type="text" class="form-control" name="ref" id="ref" value="" readonly>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="master_transaction_id">Pilih Transaksi</label>
                                <select name="master_transaction_id" id="master_transaction_id" class="form-control" required>
                                    <option value="">Pilih jenis transaksi</option>
                                    @foreach($masterTransaction as $transactionMaster)
                                    <option value="{{ $transactionMaster->id }}" data-ref="{{ $transactionMaster->prefix_code }}">
                                        {{ $transactionMaster->description }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="division">Pilih Divisi</label>
                                <select name="division" id="division" class="form-control" required>
                                    <option value="0">Pilih Divisi / Kosongkan</option>
                                    @foreach($division as $divisions)
                                    <option value="{{ $divisions->id }}">
                                        {{ $divisions->description }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="transaction_date">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="notes">Catatan Transaksi <span class="text-danger">*</span></label>
                                <textarea rows="2" class="form-control" name="notes" placeholder="Masukkan catatan ..."></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="evidence_image">Bukti Transaksi</label> <small class="form-text text-muted">(Maks. 2MB)</small>
                                <input type="file" class="form-control-file file-selector-button" name="evidence_image" id="evidence_image">
                            </div>

                            <div class="form-group col-md-12">
                                <input type="text" class="form-control" name="status" id="status" value="success" hidden>
                            </div>

                        </div>

                        @foreach($masterTransaction as $transactionMaster)
                        <div id="journalDetails_{{ $transactionMaster->id }}" style="display: none;">
                            <hr>
                            <div>
                                <div style="display: flex; align-items: center;">
                                    <h4 style="color:#121F3E">Jurnal Akuntansi</h4>
                                </div>
                                <p style="color:#ABB3C4;">Masukkan nominal debit dan kredit dengan jumlah yang sama</p>
                            </div>
                            <div class="form-group row font-weight-bold text-center">
                                <div class="col-md-6">Jurnal</div>
                                <div class="col-md-3">Debit</div>
                                <div class="col-md-3">Kredit</div>
                            </div>

                            @foreach($detailMasterTransaction as $index => $transactionDetail)
                            @if($transactionDetail->id == $transactionMaster->id)
                            <div class="form-group row {{ $transactionDetail->account_position == 'credit' ? 'credit-row' : '' }}">
                                <div class="col-md-6 d-flex align-items-center">
                                    {{ $transactionDetail->gl_account }}
                                    &emsp;
                                    {{ $transactionDetail->account_name }}
                                </div>
                                <div class="col-md-3">
                                    @if($transactionDetail->account_position == 'debit')
                                    <input type="hidden" name="gl_account[{{ $index }}]" value="{{ $transactionDetail->gl_account }}">
                                    <input type="text" class="form-control" name="debit[{{ $index }}]" placeholder="Debit" oninput="formatNumber(this)">
                                    <input type="hidden" class="form-control" name="credit[{{ $index }}]" value="0">
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    @if($transactionDetail->account_position == 'credit')
                                    <input type="hidden" name="gl_account[{{ $index }}]" value="{{ $transactionDetail->gl_account }}">
                                    <input type="text" class="form-control" name="credit[{{ $index }}]" placeholder="Credit" oninput="formatNumber(this)">
                                    <input type="hidden" class="form-control" name="debit[{{ $index }}]" value="0">
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @endforeach

                        @include('components.alert-amount-balance')

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary mr-2" id="submitButton" disabled>Submit</button>
                            <a class="btn btn-danger" href="{{ route('cash-in.index')}}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

@endsection

<style>
    .credit-row {
        padding-left: 35px;
    }
</style>


<script>
    function formatNumber(input) {
        let value = input.value.replace(/[^0-9.]/g, ''); // Remove non-numeric characters except the decimal point
        let number = parseFloat(value);
        if (!isNaN(number)) {
            input.value = number.toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }
    }

    function unformatNumber(value) {
        return value.replace(/,/g, ''); // Remove all commas
    }


    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('master_transaction_id').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var ref = selectedOption.getAttribute('data-ref');
            var selectedId = selectedOption.value;
            document.getElementById('ref').value = ref;

            var journalDetailsContainer = document.getElementById('journalDetailsContainer');
            var submitButton = document.getElementById('submitButton');

            // Clear previous content
            journalDetailsContainer.innerHTML = '';

            // Get the journal details for the selected transaction type
            var journalDetailsElement = document.getElementById('journalDetails_' + selectedId);
            var journalDetails = journalDetailsElement ? journalDetailsElement.innerHTML : '';

            // Log the journal details in a readable format
            console.log("Journal Details:", JSON.stringify(journalDetails, null, 2));

            // Display journal details in the container
            journalDetailsContainer.innerHTML = journalDetails;

            // Add event listeners to each debit and credit input
            var debitInputs = document.querySelectorAll('input[name^="debit["]');
            var creditInputs = document.querySelectorAll('input[name^="credit["]');

            submitButton.disabled = true;

            // Function to validate total debit and credit
            function validateDebitCredit() {
                var totalDebit = 0;
                var totalCredit = 0;

                // Calculate total debit
                debitInputs.forEach(input => {
                    totalDebit += parseFloat(input.value) || 0;
                });

                // Calculate total credit
                creditInputs.forEach(input => {
                    totalCredit += parseFloat(input.value) || 0;
                });

                // Check if total debit and credit are equal
                if (totalDebit !== totalCredit) {
                    // Show error message
                    document.getElementById('errorAlert').style.display = 'block';
                    document.getElementById('successAlert').style.display = 'none';
                    // Disable submit button
                    submitButton.disabled = true;
                } else {
                    document.getElementById('errorAlert').style.display = 'none';
                    document.getElementById('successAlert').style.display = 'block';
                    submitButton.disabled = false;
                }
            }

            // Add change event listener to validate when values change
            debitInputs.forEach(input => {
                input.addEventListener('change', validateDebitCredit);
            });

            creditInputs.forEach(input => {
                input.addEventListener('change', validateDebitCredit);
            });
        });

        // Remove commas before form submission
        document.querySelector('form').addEventListener('submit', function(event) {
            var debitInputs = document.querySelectorAll('input[name^="debit["]');
            var creditInputs = document.querySelectorAll('input[name^="credit["]');

            debitInputs.forEach(input => {
                input.value = unformatNumber(input.value);
            });

            creditInputs.forEach(input => {
                input.value = unformatNumber(input.value);
            });
        });
    });
</script>