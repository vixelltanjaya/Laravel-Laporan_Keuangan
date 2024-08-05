@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="card mb-4 w-100">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Buku Besar</h5>
                        </div>
                        <div class="nav-item d-flex align-self-end">
                            <button id="printButton" class="btn btn-dark active mb-0 text-white me-2">
                                <i class="fas fa-print me-1"></i>Print</button>
                            <button id="lihatBukuBesarButton" type="button" class="btn btn-primary active mb-0">
                                Lihat Buku Besar
                            </button>
                        </div>
                    </div>
                </div>
                @include('components.alert-danger-success')
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <form id="filterForm" method="GET" action="{{ route('general-ledger.getRequest') }}">
                                <label for="month_year" class="form-label">Bulan dan Tahun</label>
                                <input type="month" id="month_year" name="month_year" class="form-control" value="{{ $monthYear ?? '' }}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-lg-0 mb-4">
            <div class="card mt-4">
                <div id="previewSection" class="card-body">
                    <div class="card-header pb-0 p-3">
                        <div class="text-center">
                            <h5 class="mb-0">Buku Besar</h5>
                            <h5 class="mb-0">Detail Transaksi</h5>
                            <h4 id="periode" class="card-subtitle text-muted mb-3">
                                {{ request('month_year') ? date('F Y', strtotime(request('month_year') . '-01')) : '' }}
                            </h4>
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
                                    <td>{{ \Carbon\Carbon::parse($entry->created_at)->format('Y/m/d') }}</td>
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
    </div>
</main>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#month_year').on('change', function() {
            var monthYearValue = $(this).val();
        });

        $('#lihatBukuBesarButton').click(function() {
            var monthYearValue = $('#month_year').val();
            if (!monthYearValue) {
                alert('Pilih bulan dan tahun terlebih dahulu.');
            } else {
                $('#filterForm').submit();
            }
        });

        $('#printButton').click(function() {
            var monthYearValue = $('#month_year').val();
            if (!monthYearValue) {
                alert('Pilih bulan dan tahun terlebih dahulu.');
            } else {
                window.print();
            }
        });
    });
</script>