@extends('layouts.user_type.auth')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                            <button id="printButton" class="btn btn-dark active mb-0 text-white me-2" disabled>
                                <i class="fas fa-print me-1"></i>Print</button>
                            <button id="lihatBukuBesarButton" type="button" class="btn btn-primary active mb-0" disabled>
                                Lihat Buku Besar
                            </button>
                        </div>
                    </div>
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
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="month_year" class="form-label">Bulan dan Tahun</label>
                            <input type="month" id="month_year" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="col-md-12 mb-lg-0 mb-4">
    <div class="card mt-4">
        <div id="previewSection" class="card-body d-none">
            <div class="card-header pb-0 p-3">
                <div class="text-center">
                    <h5 class="mb-0">Preview Buku Besar</h5>
                </div>
            </div>
            <h5 class="mb-3 text-center">Detail Transaksi</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        @foreach ($chartOfAccounts as $coa )
                        <tr>
                            <th colspan="3" style="background-color: gray; color: #fff; text-align: left;">Akun :{{$coa->account_name}}</th> <th colspan="3" style="background-color: gray; color: #fff; text-align: right;" >Akun :{{$coa->account_id}}</th>
                        </tr>
                        <tr>
                            <th>Nomor Bukti</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $coa->account_id }}</td>
                            <td>{{ $coa->created_at }}</td>
                            <td>{{ $coa->name }}</td>
                            <td>{{ $coa->transaction }}</td>
                            <td>{{ $coa->address }}</td>
                            <td>{{ $coa->amount }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#month_year').on('change', function() {
            var monthYearValue = $('#month_year').val();
            if (monthYearValue) {
                $('#printButton, #lihatBukuBesarButton').prop('disabled', false);
            } else {
                $('#printButton, #lihatBukuBesarButton').prop('disabled', true);
            }
        });

        $('#lihatBukuBesarButton').click(function() {
            $('#previewSection').removeClass('d-none');
        });

        $('#printButton').click(function() {
            window.print();
        });
    });
</script>

@endsection