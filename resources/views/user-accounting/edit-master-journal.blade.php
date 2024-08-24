@extends('layouts.user_type.auth')

@section('content')


<div class="row">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-default"> <i class="fas fa-angle-left me-1">Back</i></a>
        <div class="card border-none">
            <div class="card-body">
                <h4>Edit Master Journal</h4>
            </div>
            @include('components.alert-danger-success');
            <div class="card-body">
                <form id="masterJournalForm" action="{{ route('master-journal.update', $masterJournal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- begin: Input Data -->
                    <div class="form-group col-md-6">
                        <label for="description">Deskripsi Template Journal<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="description" value="{{ $masterJournal->description }}" required>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="evidence_id">Kode Bukti Template Journal<span class="text-danger">*</span></label>
                        <select name="evidence_id" id="evidence_id" class="form-control" required>
                            <option value="">Pilih Kode Bukti</option>
                            @foreach($EvidenceCode as $evidence)
                            <option value="{{ $evidence->id }}" {{ $masterJournal->evidence_id == $evidence->id ? 'selected' : '' }}>{{ $evidence->prefix_code }} - {{ $evidence->code_title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="DetailJournals_" class="detail-journal">
                        <hr>
                        <div>
                            <div style="display: flex; align-items: center;">
                                <h4 style="color:#121F3E">Jurnal Akuntansi</h4>
                            </div>
                            <p style="color:#ABB3C4;">Buat Jurnal Template</p>
                        </div>
                        <div class="form-group row font-weight-bold text-center">
                            <div class="col-md-6">
                                Jurnal
                            </div>
                            <div class="col-md-3">
                                Sign Akun
                            </div>
                        </div>
                        <div class="journal-entries">
                            @if ($detailJournal && $detailJournal->count())
                            @foreach($detailJournal as $index => $detail)
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <select class="form-control" name="noAccount[]" required>
                                        <option value="" disabled selected>Nomor Akun</option>
                                        @foreach($chartOfAccounts as $coa)
                                        <option value="{{ $coa->account_id }}" {{ $detail->gl_account == $coa->account_id ? 'selected' : '' }}>
                                            {{ $coa->account_id }} - {{ $coa->account_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" name="accountSign[]" required>
                                        <option value="debit" {{ $detail->account_position == 'debit' ? 'selected' : '' }}>Debit</option>
                                        <option value="credit" {{ $detail->account_position == 'credit' ? 'selected' : '' }}>Kredit</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex flex-column justify-content-between">
                                    @if ($index == 0)
                                    <button type="button" class="btn btn-info btn-sm mb-2 addRow">+</button>
                                    @else
                                    <button type="button" class="btn btn-danger btn-sm removeRow">-</button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            @else
                            <p>No journal details available.</p>
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#previewModal">Preview</button>
                    <button type="button" class="btn btn-default" id="resetButton">Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="previewModalLabel">Preview Jurnal</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Keterangan</th>
                            <th scope="col">Debit</th>
                            <th scope="col">Kredit</th>
                        </tr>
                    </thead>
                    <tbody id="journalPreviewTable">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        // Function to add a new row
        function addRow(container) {
            var newRow = `
                <div class="form-group row">
                    <div class="col-md-6">
                        <select class="form-control" name="noAccount[]" required>
                            <option value="" disabled selected>Nomor Akun</option>
                            @foreach($chartOfAccounts as $coa)
                            <option value="{{ $coa->account_id }}">{{ $coa->account_id }} - {{ $coa->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="accountSign[]" required>
                            <option value="debit">Debit</option>
                            <option value="credit">Kredit</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex flex-column justify-content-between">
                        <button type="button" class="btn btn-info btn-sm mb-2 addRow">+</button>
                        <button type="button" class="btn btn-danger btn-sm removeRow">-</button>
                    </div>
                </div>`;
            container.append(newRow);
        }

        // Function to remove a row
        function removeRow(button) {
            $(button).closest('.form-group.row').remove();
        }

        // Event listener for adding a new row
        $(document).on('click', '.addRow', function() {
            const container = $(this).closest('.journal-entries');
            addRow(container);
        });

        // Event listener for removing a row
        $(document).on('click', '.removeRow', function() {
            removeRow(this);
        });

        $('#resetButton').on('click', function() {
            $('#masterJournalForm')[0].reset();
            $('.journal-entries').html(`
                <div class="form-group row">
                    <div class="col-md-6">
                        <select class="form-control" name="noAccount[]" required>
                            <option value="" disabled selected>Nomor Akun</option>
                            @foreach($chartOfAccounts as $coa)
                            <option value="{{ $coa->account_id }}">{{ $coa->account_id }} - {{ $coa->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="accountSign[]" required>
                            <option value="debit">Debit</option>
                            <option value="credit">Kredit</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex flex-column justify-content-between">
                        <button type="button" class="btn btn-info btn-sm mb-2 addRow">+</button>
                    </div>
                </div>`);
        });

        // Event listener for previewing the form data
        $('button[data-target="#previewModal"]').on('click', function() {
            // Collect data from the form
            let transactionCode = $('input[name="code"]').val();
            let description = $('input[name="description"]').val();
            let journalEntries = [];

            $('.journal-entries .form-group.row').each(function() {
                let noAccount = $(this).find('select[name="noAccount[]"]').val();
                let accountSign = $(this).find('select[name="accountSign[]"]').val();

                if (noAccount && accountSign) {
                    journalEntries.push({
                        noAccount,
                        accountSign
                    });
                }
            });

            // Populate the modal
            let tbody = $('#journalPreviewTable');
            tbody.empty();

            journalEntries.forEach(entry => {
                let row = `<tr>
                <td>${entry.noAccount}</td>
                <td>${entry.accountSign === 'debit' ? 'Rp.XXX' : ''}</td>
                <td>${entry.accountSign === 'credit' ? 'Rp.XXX' : ''}</td>
            </tr>`;
                tbody.append(row);
            });

            // view modal
            $('#previewModal').modal('show');
        });
    });
</script>