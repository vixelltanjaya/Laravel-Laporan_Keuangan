@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="card mb-4 w-100">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Tutup Buku</h5>
                        </div>
                        <div class="d-flex align-items-center">
                            <form method="GET" action="{{ route('closed-balance.index') }}" class="d-flex align-items-center">
                                <div class="me-2">
                                    <label for="month_year" class="form-label">Pilih Bulan dan Tahun:</label>
                                </div>
                                <div class="me-2">
                                    <input type="month" id="month_year" name="month_year" value="{{ request('month_year') }}" class="form-control" />
                                </div>
                                <button type="submit" id="lihatBukuBesarButton" class="btn btn-primary active" {{ request('month_year') ? '' : 'disabled' }}>Filter</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('components.alert-danger-success')
                </div>
            </div>
        </div>
    </div>

    <div id="previewSection" class="card-body">
        <div class="card text-center p-4 mb-4">
            <div class="card-body">
                <h3 class="card-title mb-2">
                    PT Maharani Putra Sejahtera
                </h3>
                <h4 id="periode" class="card-subtitle text-muted">
                    Periode {{ request('month_year') ? date('F Y', strtotime(request('month_year') . '-01')) : '' }}
                </h4>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th>Saldo Awal Bulan</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Saldo Akhir Bulan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(request('month_year'))
                        @foreach ($sumBalance as $balance)
                        <tr>
                            <td>{{ $balance->account_id }} - {{ $balance->account_name }}</td>
                            <td>{{ number_format($balance->beginning_balance_next_month) }}</td>
                            <td>{{ number_format($balance->total_debit) }}</td>
                            <td>{{ number_format($balance->total_credit) }}</td>
                            <td>
                                @if(strtolower($balance->account_sign) === 'debit')
                                {{ number_format($balance->beginning_balance_next_month + $balance->total_debit - $balance->total_credit) }}
                                @else
                                {{ number_format($balance->beginning_balance_next_month +  $balance->total_credit - $balance->total_debit) }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td><strong>Total</strong></td>
                            <td></td>
                            <td><strong>{{ number_format($sumBalance->sum('total_debit')) }}</strong></td>
                            <td><strong>{{ number_format($sumBalance->sum('total_credit')) }}</strong></td>
                            <td><strong>{{ number_format($totalSum) }}</strong></td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-center mt-3">
                    <button type="button" id="tutupSaldoButton" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#tutupSaldoModal"  @if($isClose || $isPastOneMonth) disabled @endif>
                        Tutup Buku
                    </button>
                </div>

                @else
                <tr>
                    <td colspan="5">Silakan pilih bulan dan tahun</td>
                </tr>
                @endif

            </div>
        </div>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="tutupSaldoModal" tabindex="-1" aria-labelledby="tutupSaldoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('closed-balance.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tutupSaldoModalLabel">Tutup Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="tutupSaldoModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tutup</button>
                </div>
                <!-- Input untuk data yang diperlukan -->
                @foreach($sumBalance as $index => $account)
                <input type="hidden" name="balances[{{ $index }}][account_id]" value="{{ $account->account_id }}">
                <input type="hidden" name="balances[{{ $index }}][beginning_balance_next_month]" value="{{ $account->beginning_balance_next_month }}">
                <input type="hidden" name="balances[{{ $index }}][debit]" value="{{ $account->total_debit }}">
                <input type="hidden" name="balances[{{ $index }}][credit]" value="{{ $account->total_credit }}">
                <input type="hidden" name="balances[{{ $index }}][balance_difference]" value="{{ $account->balance_difference }}">
                <input type="hidden" name="balances[{{ $index }}][month_year]" value="{{ request('month_year') }}">
                @endforeach
            </form>
        </div>
    </div>
</div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#month_year').on('change', function() {
            var monthYearValue = $('#month_year').val();
            if (monthYearValue) {
                var [year, month] = monthYearValue.split('-');
                var nextMonthDate = new Date(year, month, 1);
                var lastDate = new Date(nextMonthDate - 1);

                var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                var formattedMonthYear = "Periode " + monthNames[lastDate.getMonth()] + " " + lastDate.getFullYear();

                console.log('Last Date of the Month: ' + lastDate.toDateString());
                $('#periode').text(formattedMonthYear);

                $('#lihatBukuBesarButton').prop('disabled', false);
            } else {
                $('#lihatBukuBesarButton').prop('disabled', true);
                $('#periode').text('Periode');
            }
        });

        // Handle button click to set the text in modal
        $('#tutupSaldoButton').on('click', function() {
            var monthYearValue = $('#month_year').val();
            if (!monthYearValue) {
                alert('Silakan pilih bulan dan tahun terlebih dahulu.');
                return;
            }

            // Get the year and month from the input value
            var [year, month] = monthYearValue.split('-');
            // Create a date object for the first day of the next month
            var nextMonthDate = new Date(year, month, 1);
            // Subtract one day to get the last date of the current month
            var lastDate = new Date(nextMonthDate - 1);

            // Format the date for display
            var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            var formattedMonthYear = "Periode " + monthNames[lastDate.getMonth()] + " " + lastDate.getFullYear();

            console.log('Last Date of the Month: ' + lastDate.toDateString());
            // Set the confirmation message in the modal
            $('#tutupSaldoModalBody').html('Apakah Anda yakin menutup saldo pada ' + formattedMonthYear +
                ' ? <strong> Anda tidak dapat mengubah transaksi setelah saldo bulan ' + formattedMonthYear + ' </strong> ditutup');
        });
    });
</script>