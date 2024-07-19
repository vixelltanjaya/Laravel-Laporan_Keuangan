@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Jurnal Koreksi</h6>
                </div>

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

                <div class="card-body">
                    <form id="journalForm" action="{{ route('correcting-entry.store', ['id' => $journalEntry->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="created_by" class="form-label">Dibuat Oleh</label>
                            <input type="text" class="form-control" id="created_by" name="created_by" value="{{ Auth::user()->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="no_ref_asal" class="form-label">No Ref Asal</label>
                            <input type="text" class="form-control" id="no_ref_asal" name="no_ref_asal" value="{{ $journalEntry->evidence_code }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="no_ref" class="form-label">No Ref</label>
                            <select class="form-control" id="no_ref" name="no_ref" required>
                                <option value="" disabled selected>Select No Ref</option>
                                @foreach($prefixCode as $code)
                                <option value="{{ $code->prefix_code }}">
                                    {{ $code->prefix_code }} - {{ $code->code_title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="division" class="form-label">Divisi</label>
                            <select class="form-control" id="division" name="division" required>
                                <option value="">Pilih divisi / Kosongkan</option>
                                @foreach($division as $divisions)
                                <option value="{{ $divisions->id }}">
                                    {{ $divisions->description }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="transaction_date">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan Transaksi</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="evidence_image" class="form-label">Bukti Transaksi</label>
                            <input type="file" class="form-control" id="evidence_image" name="evidence_image" accept="image/jpeg, image/png">
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
                                <div class="col-md-5">
                                    Jurnal
                                </div>
                                <div class="col-md-3">
                                    Sign Akun
                                </div>
                                <div class="col-md-3">
                                    Nominal
                                </div>
                            </div>
                            <div class="journal-entries">
                                <div class="form-group row">
                                    <div class="col-md-5">
                                        <select class="form-control" name="noAccount[]" required>
                                            <option value="" disabled selected>Nomor Akun</option>
                                            @foreach($accounts as $coa)
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
                                    <div class="col-md-3">
                                        <input type="text" class="form-control amount-input" name="formatted_amount[]" oninput="formatNumber(this)" required>
                                        <input type="hidden" class="raw-amount-input" name="amount[]" />
                                    </div>
                                    <div class="col-md-1 d-flex flex-column justify-content-between">
                                        <button type="button" class="btn btn-info btn-sm mb-2 addRow">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#previewModal">Preview</button>
                        <button type="button" class="btn btn-default" id="resetButton">Reset</button>
                        <a href="{{ url()->previous() }}" type="button" class="btn btn-info">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview Jurnal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.addRow', function(e) {
            e.preventDefault();
            const entry = $(this).closest('.form-group.row').clone();
            entry.find('.addRow').removeClass('addRow').addClass('removeRow').removeClass('btn-info').addClass('btn-danger').text('-');
            entry.find('input').val(''); // Clear input values
            entry.find('select').val(''); // Clear select values
            entry.appendTo('.journal-entries');
        });

        $(document).on('click', '.removeRow', function(e) {
            e.preventDefault();
            $(this).closest('.form-group.row').remove();
        });

        $('#resetButton').click(function() {
            $('#journalForm')[0].reset();
        });

        $('[data-bs-target="#previewModal"]').click(function() {
            let previewContent = `
                <p><strong>Dibuat Oleh:</strong> ${$('#created_by').val()}</p>
                <p><strong>No Ref:</strong> ${$('#no_ref').val()}</p>
                <p><strong>Tanggal Transaksi:</strong> ${$('#transaction_date').val()}</p>
                <p><strong>Catatan Transaksi:</strong> ${$('#notes').val()}</p>
                <p><strong>Bukti Transaksi:</strong> ${$('#evidence_image').val()}</p>
                <hr>
                <h5>Jurnal Akuntansi</h5>
                <div class="table-responsive">
                    <table class="table border text-center">
                        <thead>
                            <tr>
                                <th>Nomor Akun</th>
                                <th>Sign Akun</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            $('.journal-entries .form-group.row').each(function() {
                let account = $(this).find('select[name="noAccount[]"]').val();
                let sign = $(this).find('select[name="accountSign[]"]').val();
                let amount = $(this).find('input[name="formatted_amount[]"]').val();
                previewContent += `
                    <tr>
                        <td>${account}</td>
                        <td>${sign}</td>
                        <td>${amount}</td>
                    </tr>
                `;
            });
            previewContent += `
                        </tbody>
                    </table>
                </div>
            `;
            $('#previewContent').html(previewContent);
        });
    });

    function formatNumber(input) {
        let value = input.value.replace(/[^0-9.]/g, ''); // Remove non-numeric characters except the decimal point
        let number = parseFloat(value);
        if (!isNaN(number)) {
            input.value = number.toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }
        updateRawValue(input);
    }

    function updateRawValue(input) {
        const rawValue = input.value.replace(/,/g, ''); // Remove commas for raw value
        const index = Array.from(document.querySelectorAll('.amount-input')).indexOf(input);
        document.querySelectorAll('.raw-amount-input')[index].value = rawValue;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const amountInputs = document.querySelectorAll('.amount-input');
        amountInputs.forEach(input => {
            input.addEventListener('blur', () => formatNumber(input));
        });
    });
</script>
@endsection