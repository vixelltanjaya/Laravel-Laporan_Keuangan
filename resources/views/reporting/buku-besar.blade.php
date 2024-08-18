<style>
    .table th,
    .table td {
        width: 100%;
        text-align: center;
    }

    .table thead th {
        background-color: gray;
        color: #fff;
    }
</style>
<div class="col-md-12 mb-lg-0 mb-4">
    <div class="card mt-4">
        <div id="previewSection" class="card-body">
            <div class="card-header pb-0 p-3">
                <div style="text-align: center;">
                    <h3 class="mb-0 text-center">Buku Besar</h3>
                    <h3 class="mb-0">Detail Transaksi</h3>
                    <h5 id="periode" class="card-subtitle text-muted mb-2">
                        {{$formattedDate}}
                    </h5>
                </div>
            </div>
            <div class="table-responsive">
                @foreach ($processedAccounts as $account_id => $accountData)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="3" style="background-color: gray; color: #fff; text-align: left;">
                                Akun: {{ $accountData->first()->account_name }}
                            </th>
                            <th colspan="3" style="background-color: gray; color: #fff; text-align: right;">
                                Akun: {{ $account_id }}
                            </th>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Nomor Bukti</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAmount = 0; @endphp
                        @forelse($accountData as $entry)
                        <tr @if(isset($entry->readonly)) style="background-color: #f5f5f5;" @endif>
                            <td>{{ $entry->formattedDateTrx }}</td>
                            <td>{{ $entry->description }}</td>
                            <td>{{ $entry->evidence_code }}</td>
                            <td>{{ $entry->debit ? number_format($entry->debit, 2) : '' }}</td>
                            <td>{{ $entry->credit ? number_format($entry->credit, 2) : '' }}</td>
                            <td>{{ number_format($entry->amount, 2) }}</td>
                        </tr>
                        @php $totalAmount += $entry->amount; @endphp
                        @empty
                        <tr>
                            <td colspan="6">No Journal Entries</td>
                        </tr>
                        @endforelse
                        <tr>
                            <td colspan="5" style="text-align: right;"><strong>Total:</strong></td>
                            <td><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
                @endforeach
            </div>
        </div>
    </div>
</div>
